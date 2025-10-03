<?php
declare(strict_types=1);

namespace App\Application;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

final readonly class Application
{
    public static function initialize(): ContainerInterface
    {
        return self::initContainer();
    }

    private static function definitions(): array
    {
        return require __DIR__ . '/definitions.php';
    }

    private static function initContainer(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->useAttributes(false);
        $builder->addDefinitions(self::definitions());
        return $builder->build();
    }
}
