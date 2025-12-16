<?php

declare(strict_types=1);

namespace Tests\Functional\Console\Commands;

use App\Infrastructure\Console\Application;
use App\Infrastructure\Console\Commands\IndexCommand;
use PHPUnit\Framework\TestCase;

final class IndexCommandTest extends TestCase
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
     * Тестирует создание приложения с командой индексации
     */
    public function testApplicationWithIndexCommand(): void
    {
        $this->assertInstanceOf(Application::class, $this->application);

        $commands = $this->application->all();
        $this->assertArrayHasKey('index', $commands);
        $this->assertInstanceOf(IndexCommand::class, $commands['index']);
    }

    /**
     * Тестирует наличие команды index в списке команд
     */
    public function testIndexCommandExists(): void
    {
        $commands = $this->application->all();
        $this->assertArrayHasKey('index', $commands);
    }

    /**
     * Тестирует описание команды index
     */
    public function testIndexCommandDescription(): void
    {
        $commands = $this->application->all();
        $indexCommand = $commands['index'];
        
        $this->assertNotEmpty($indexCommand->getDescription());
    }

    /**
     * Тестирует параметры команды index
     */
    public function testIndexCommandParameters(): void
    {
        $commands = $this->application->all();
        $indexCommand = $commands['index'];
        
        $definition = $indexCommand->getDefinition();
        $this->assertTrue($definition->hasOption('file'));
        $this->assertTrue($definition->hasOption('recreate'));
    }

    /**
     * Тестирует, что приложение содержит наши команды
     */
    public function testApplicationContainsOurCommands(): void
    {
        $commands = $this->application->all();
        
        $this->assertArrayHasKey('search', $commands);
        $this->assertArrayHasKey('index', $commands);
        $this->assertGreaterThanOrEqual(2, count($commands));
    }
}
