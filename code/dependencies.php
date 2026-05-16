<?php
declare(strict_types=1);

use App\Application\UseCase;
use App\Domain\Repository;
use App\Infrastructure\Repository\PDO as PdoRepository;
use App\Presentation\Controller\TrainingPlan\TrainingPlanController;
use App\Presentation\Controller\User\UserController;
use App\Presentation\Validation\TrainingPlanValidator;
use App\Presentation\Validation\UserValidator;
use App\Presentation\Controller\TrainingPlan\UserController as TrainingUserController;
use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

return function (Container $container) {
    // Logger
    $container->set(LoggerInterface::class, function (ContainerInterface $c) {
        $settings = [
            'name' => 'app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => Logger::DEBUG,
        ];
        $logger = new Logger($settings['name']);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));
        return $logger;
    });

    // Slim App
    $container->set(App::class, function (ContainerInterface $c) {
        AppFactory::setContainer($c);
        return AppFactory::create();
    });

    // Database Connection (PDO)
    $container->set(PDO::class, function (ContainerInterface $c) {
        $host = getenv('POSTGRES_HOST');
        $port = getenv('POSTGRES_PORT');
        $db = getenv('POSTGRES_DB');
        $user = getenv('POSTGRES_USER');
        $pass = getenv('POSTGRES_PASSWORD');
        $dsn = "pgsql:host={$host};port={$port};dbname={$db}";

        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    });

    // RabbitMQ Connection
    $container->set(AMQPStreamConnection::class, function (ContainerInterface $c) {
        return new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            $_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_LOGIN'],
            $_ENV['RABBITMQ_PASSWORD']
        );
    });

    // Repositories
    $container->set(Repository\UserRepositoryInterface::class, function (ContainerInterface $c) {
        return new PdoRepository\PostgresUserRepository($c->get(PDO::class));
    });
    $container->set(Repository\TrainingScheduleRepositoryInterface::class, function (ContainerInterface $c) {
        return new PdoRepository\PostgresTrainingScheduleRepository($c->get(PDO::class));
    });
    $container->set(Repository\TrainingPlanRepositoryInterface::class, function (ContainerInterface $c) {
        return new PdoRepository\PostgresTrainingPlanRepository(
            $c->get(PDO::class),
            $c->get(Repository\TrainingScheduleRepositoryInterface::class),
            $c->get(Repository\ExerciseRepositoryInterface::class)
        );
    });
    $container->set(Repository\UserTrainingPlanRepositoryInterface::class, function (ContainerInterface $c) {
        return new PdoRepository\PostgresUserTrainingPlanRepository(
            $c->get(PDO::class),
            $c->get(Repository\UserRepositoryInterface::class)
        );
    });
    $container->set(Repository\ExerciseRepositoryInterface::class, function (ContainerInterface $c) {
        return new PdoRepository\PostgresExerciseRepository($c->get(PDO::class));
    });

    // Use Cases
    $container->set(UseCase\User\CreateUserUseCase::class, function (ContainerInterface $c) {
        return new UseCase\User\CreateUserUseCase($c->get(Repository\UserRepositoryInterface::class));
    });
    $container->set(UseCase\TrainingPlan\CreateTrainingPlanUseCase::class, function (ContainerInterface $c) {
        return new UseCase\TrainingPlan\CreateTrainingPlanUseCase($c->get(Repository\TrainingPlanRepositoryInterface::class));
    });
    $container->set(UseCase\TrainingPlan\AssignTrainingPlanToUserUseCase::class, function (ContainerInterface $c) {
        return new UseCase\TrainingPlan\AssignTrainingPlanToUserUseCase(
            $c->get(Repository\UserTrainingPlanRepositoryInterface::class),
            $c->get(Repository\UserRepositoryInterface::class),
            $c->get(Repository\TrainingPlanRepositoryInterface::class)
        );
    });
    $container->set(UseCase\TrainingPlan\GetTrainingPlanWithExercisesByDay::class, function (ContainerInterface $c) {
        return new UseCase\TrainingPlan\GetTrainingPlanWithExercisesByDay($c->get(Repository\TrainingPlanRepositoryInterface::class));
    });
    $container->set(UseCase\TrainingPlan\GetExercisesByTrainingSchedule::class, function (ContainerInterface $c) {
        return new UseCase\TrainingPlan\GetExercisesByTrainingSchedule($c->get(Repository\TrainingPlanRepositoryInterface::class));
    });

    // Validators
    $container->set(UserValidator::class, function () {
        return new UserValidator();
    });
    $container->set(TrainingPlanValidator::class, function () {
        return new TrainingPlanValidator();
    });

    // Controllers
    $container->set(UserController::class, function (ContainerInterface $c) {
        return new UserController(
            $c->get(UserValidator::class),
            $c->get(UseCase\User\CreateUserUseCase::class),
            $c->get(UseCase\User\GetUserByIdUseCase::class),
            $c->get(UseCase\User\UpdateUserUseCase::class),
            $c->get(UseCase\User\DeleteUserUseCase::class),
        );
    });
    $container->set(TrainingPlanController::class, function (ContainerInterface $c) {
        return new TrainingPlanController(
            $c->get(TrainingPlanValidator::class),
            $c->get(UseCase\TrainingPlan\CreateTrainingPlanUseCase::class),
            $c->get(UseCase\TrainingPlan\GetTrainingPlanWithExercisesByDay::class)
        );
    });

    $container->set(TrainingUserController::class, function (ContainerInterface $c) {
       return new TrainingUserController(
           $c->get(UseCase\TrainingPlan\AssignTrainingPlanToUserUseCase::class)
       );
    });

    // Event Publisher
    $container->set(\App\Domain\Event\EventPublisherInterface::class, function (ContainerInterface $c) {
        return new \App\Infrastructure\Event\RabbitMQEventPublisher(
            $c->get(AMQPStreamConnection::class),
            'fitness'
        );
    });

    // Use Case for Notifications
    $container->set(\App\Application\UseCase\GenerateTrainingNotificationEvents::class, function (ContainerInterface $c) {
        return new \App\Application\UseCase\GenerateTrainingNotificationEvents(
            $c->get(Repository\TrainingScheduleRepositoryInterface::class),
            $c->get(Repository\UserTrainingPlanRepositoryInterface::class),
            $c->get(\App\Domain\Event\EventPublisherInterface::class),
            $c->get(Repository\TrainingPlanRepositoryInterface::class),
        );
    });

    // Console Command
    $container->set(\App\Presentation\Console\Command\GenerateTrainingNotificationsCommand::class, function (ContainerInterface $c) {
        return new \App\Presentation\Console\Command\GenerateTrainingNotificationsCommand(
            $c->get(\App\Application\UseCase\GenerateTrainingNotificationEvents::class)
        );
    });
};
