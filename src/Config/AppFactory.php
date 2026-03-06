<?php
declare(strict_types=1);

namespace App\Config;

use App\Application\UseCase\ProcessStatementUseCase;
use App\Application\UseCase\SubmitStatementUseCase;
use App\Application\Service\BankStatementService;
use App\Infrastructure\Mailer\Mailer;
use App\Infrastructure\Mailer\MailerConfig;
use App\Infrastructure\Mailer\PhpMailerFactory;
use App\Infrastructure\RabbitMq\RabbitMqChannelProvider;
use App\Infrastructure\RabbitMq\RabbitMqConnectionFactory;
use App\Infrastructure\RabbitMq\Statement\StatementConsumer;
use App\Infrastructure\RabbitMq\Statement\StatementProducer;
use App\Presentation\Controller\Application;
use App\Presentation\Controller\StatementController;
use App\Presentation\Http\Router;
use App\Presentation\View\TemplateRenderer;

class AppFactory
{
    public function __construct(
        private readonly AppConfig $config
    ) {}

    public function createHttpApplication(): Application
    {
        $logger = LoggerFactory::create('http', false);

        $renderer = new TemplateRenderer(dirname(__DIR__) . '/Presentation/View/Template');

        $statementProducer = new StatementProducer($this->createRabbitMqChannelProvider(), $logger);
        $submitStatementUseCase = new SubmitStatementUseCase($statementProducer);

        $controller = new StatementController($renderer, $submitStatementUseCase);

        $router = new Router();
        $router->get('/', [$controller, 'form']);
        $router->post('/statements', [$controller, 'submit']);

        return new Application($router);
    }

    public function createStatementConsumer(): StatementConsumer
    {
        $logger = LoggerFactory::create('worker', true);

        $mailerConfig = new MailerConfig(
            host: $this->config->mailHost,
            port: $this->config->mailPort,
            fromEmail: $this->config->mailFromEmail,
            fromName: $this->config->mailFromName,
        );

        $mailer = new Mailer(new PhpMailerFactory($mailerConfig), $logger);
        $processUseCase = new ProcessStatementUseCase(new BankStatementService(), $mailer, $logger);

        return new StatementConsumer($this->createRabbitMqChannelProvider(), $processUseCase, $logger);
    }

    private function createRabbitMqChannelProvider(): RabbitMqChannelProvider
    {
        $rabbitMqFactory = new RabbitMqConnectionFactory(
            host: $this->config->rabbitHost,
            port: $this->config->rabbitPort,
            user: $this->config->rabbitUser,
            pass: $this->config->rabbitPass,
            vhost: $this->config->rabbitVhost,
        );

        return new RabbitMqChannelProvider($rabbitMqFactory);
    }
}
