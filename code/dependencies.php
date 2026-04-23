<?php
declare(strict_types=1);

use App\Application\UseCase;
//use App\Domain\Repository\TrainingPlanRepositoryInterface;
//use App\Domain\Repository\TrainingScheduleRepositoryInterface;
use App\Domain\Repository;


/*use App\Infrastructure\Repository\PostgresTrainingPlanRepository;
use App\Infrastructure\Repository\PostgresTrainingScheduleRepository;
use App\Infrastructure\Repository\PostgresUserRepository;
use App\Infrastructure\Repository\PostgresUserTrainingPlanRepository;*/
use App\Presentation\Controller\TrainingPlan\TrainingPlanController;
use App\Presentation\Controller\User\UserController;
use App\Presentation\Validation\TrainingPlanValidator;
use App\Presentation\Validation\UserValidator;
use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

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

    // Repositories
    $container->set(Repository\UserRepositoryInterface::class, function (ContainerInterface $c) {
        return new Repository\PostgresUserRepository($c->get(PDO::class));
    });
    $container->set(TrainingScheduleRepositoryInterface::class, function (ContainerInterface $c) {
        return new PostgresTrainingScheduleRepository($c->get(PDO::class));
    });
    $container->set(TrainingPlanRepositoryInterface::class, function (ContainerInterface $c) {
        return new PostgresTrainingPlanRepository(
            $c->get(PDO::class),
            $c->get(TrainingScheduleRepositoryInterface::class)
        );
    });
    $container->set(Repository\UserTrainingPlanRepositoryInterface::class, function (ContainerInterface $c) {
        return new PostgresUserTrainingPlanRepository($c->get(PDO::class));
    });

    // Use Cases
    $container->set(UseCase\User\CreateUserUseCase::class, function (ContainerInterface $c) {
        return new UseCase\User\CreateUserUseCase($c->get(Repository\UserRepositoryInterface::class));
    });
    $container->set(UseCase\TrainingPlan\CreateTrainingPlanUseCase::class, function (ContainerInterface $c) {
        return new UseCase\TrainingPlan\CreateTrainingPlanUseCase($c->get(TrainingPlanRepositoryInterface::class));
    });
    $container->set(UseCase\TrainingPlan\AssignTrainingPlanToUserUseCase::class, function (ContainerInterface $c) {
        return new UseCase\TrainingPlan\AssignTrainingPlanToUserUseCase(
            $c->get(Repository\UserTrainingPlanRepositoryInterface::class),
            $c->get(Repository\UserRepositoryInterface::class),
            $c->get(TrainingPlanRepositoryInterface::class)
        );
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
            $c->get(UseCase\TrainingPlan\AssignTrainingPlanToUserUseCase::class)
        );
    });
};
