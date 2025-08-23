<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Console;

use App\Infrastructure\Console\Application;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    private Application $application;

    /**
     * Настройка тестового окружения
     */
    protected function setUp(): void
    {
        $this->application = new Application();
    }

    /**
     * Тестирует создание приложения
     */
    public function testApplicationCreation(): void
    {
        $this->assertInstanceOf(Application::class, $this->application);
    }

    /**
     * Тестирует получение имени приложения
     */
    public function testGetName(): void
    {
        $name = $this->application->getName();
        $this->assertIsString($name);
        $this->assertNotEmpty($name);
    }

    /**
     * Тестирует получение версии приложения
     */
    public function testGetVersion(): void
    {
        $version = $this->application->getVersion();
        $this->assertIsString($version);
        $this->assertNotEmpty($version);
    }
}
