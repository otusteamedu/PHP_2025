<?php
declare(strict_types=1);

namespace App\tests;

use App\Application\Application as AppApplication;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as CliApplication;

final class ApplicationTest extends TestCase
{
    #[Test]
    public function test_initialize_returns_container_and_provides_cli_application(): void
    {
        $container = AppApplication::initialize();
        $this->assertInstanceOf(ContainerInterface::class, $container);

        $cli = $container->get(CliApplication::class);
        $this->assertInstanceOf(CliApplication::class, $cli);
    }
}
