<?php

declare(strict_types=1);

use Otus\DataMapper\ABC\A;
use Otus\DataMapper\ABC\B;
use Otus\DataMapper\ABC\C;
use Otus\DataMapper\Bus\Bus;
use Otus\DataMapper\Command\ContainerCommand;
use Otus\DataMapper\Command\DeleteCommand;
use Otus\DataMapper\Command\EagerCommand;
use Otus\DataMapper\Command\InsertCommand;
use Otus\DataMapper\Command\LazyCommand;
use Otus\DataMapper\Command\RunCommand;
use Otus\DataMapper\Command\SqlCommand;
use Otus\DataMapper\Command\UpdateCommand;
use Otus\DataMapper\Config\ArrayReader;
use Otus\DataMapper\Config\Config;
use Otus\DataMapper\Config\ConfigInterface;
use Otus\DataMapper\Config\ReaderInterface;
use Otus\DataMapper\Di\Container;

return [
    'singletons' => [
        ReaderInterface::class => static function (): ReaderInterface {
            return new ArrayReader(dirname(__DIR__) . '/.env.php');
        },
        ConfigInterface::class => static function (Container $container): ConfigInterface {
            $list = $container->get(ReaderInterface::class)->get();

            return new Config($list);
        },
        Bus::class => static function (): Bus {
            return new Otus\DataMapper\Bus\Bus([
                'run' => RunCommand::class,
                'sql' => SqlCommand::class,
                'insert' => InsertCommand::class,
                'lazy' => LazyCommand::class,
                'eager' => EagerCommand::class,
                'update' => UpdateCommand::class,
                'delete' => DeleteCommand::class,
                'container' => ContainerCommand::class,
            ]);
        },
        A::class => static function (): A {
            return new A(true);
        },
    ],
    'definitions' => [
        C::class => static function (Container $container): C {
            $b = $container->get(B::class);

            return new C($b, __LINE__);
        },
    ],
];
