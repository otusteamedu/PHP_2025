<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use App\Application\BankStatementConsumer\BankStatementConsumerHandler;
use App\Application\GetBankStatement\GetBankStatementHandler;
use App\Domain\Interfaces\ConfigInterface;
use App\Infrastructure\Config\EnvConfig;
use App\Infrastructure\Messenger\AmqpConnectFactory;
use App\Infrastructure\Messenger\Consumer\BankStatementConsumer;
use App\Infrastructure\Messenger\GetBankStatement\BankStatementProducer;
use App\UserInterface\GetBankStatement;
use App\UserInterface\StartBankStatementConsumer;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        $container->singleton(ConfigInterface::class, function () {
            return new EnvConfig();
        });

        $config = $container->get(ConfigInterface::class);
        $rabbitHost = $config->get('RABBIT_HOST');
        $rabbitPort = $config->get('RABBIT_PORT');
        $rabbitLogin = $config->get('RABBIT_LOGIN');
        $rabbitPassword = $config->get('RABBIT_PASSWORD');
        $rabbitVhost = $config->get('RABBIT_VHOST');

        $container->set(AmqpConnectFactory::class, function () use (
            $rabbitHost,
            $rabbitPort,
            $rabbitLogin,
            $rabbitPassword,
            $rabbitVhost
        ) {
            return new AmqpConnectFactory(
                $rabbitHost,
                $rabbitPort,
                $rabbitLogin,
                $rabbitPassword,
                $rabbitVhost
            );
        });

        //Consumer
        $container->singleton(BankStatementConsumer::class, function (Container $container) {
            return new BankStatementConsumer(
                $container->get(AmqpConnectFactory::class),
            );
        });

        $container->singleton(BankStatementConsumerHandler::class, function (Container $container) {
            return new BankStatementConsumerHandler(
                $container->get(BankStatementConsumer::class),
            );
        });

        $container->set(StartBankStatementConsumer::class, function (Container $container) {
            return new StartBankStatementConsumer(
                $container->get(BankStatementConsumerHandler::class),
            );
        });

        //Producer
        $container->singleton(BankStatementProducer::class, function (Container $container) {
            return new BankStatementProducer(
                $container->get(AmqpConnectFactory::class),
            );
        });

        $container->set(GetBankStatementHandler::class, function (Container $container) {
            return new GetBankStatementHandler(
                $container->get(BankStatementProducer::class),
            );
        });

        $container->set(GetBankStatement::class, function (Container $container) {
            return new GetBankStatement(
                $container->get(GetBankStatementHandler::class),
            );
        });

        return $container;
    }
}
