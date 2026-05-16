<?php

error_reporting(E_ALL & ~E_DEPRECATED);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\UseCase\TrainingPlan\GetExercisesByTrainingSchedule;
use App\Infrastructure\RabbitMQ\EventConsumer;
use DI\Container;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Create a new DI container
$container = new Container();

// Inject dependencies
(require __DIR__ . '/../dependencies.php')($container);

class ConsumeEventsCommand extends Command
{
    protected static $defaultName = 'app:consume-events';

    public function __construct(
        private readonly AMQPStreamConnection $connection,
        private readonly GetExercisesByTrainingSchedule $getExercisesByTrainingSchedule
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Consumes events from RabbitMQ.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $consumer = new EventConsumer($this->connection, $this->getExercisesByTrainingSchedule);
        $consumer->consume();

        return Command::SUCCESS;
    }
}

$application = new Application();
$application->add(new ConsumeEventsCommand(
    $container->get(AMQPStreamConnection::class),
    $container->get(GetExercisesByTrainingSchedule::class)
));
$application->run();
