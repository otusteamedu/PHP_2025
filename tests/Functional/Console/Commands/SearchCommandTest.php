<?php

declare(strict_types=1);

namespace Tests\Functional\Console\Commands;

use App\Infrastructure\Console\Application;
use App\Infrastructure\Console\Commands\SearchCommand;
use PHPUnit\Framework\TestCase;

final class SearchCommandTest extends TestCase
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
     * Тестирует создание приложения с командой поиска
     */
    public function testApplicationWithSearchCommand(): void
    {
        $this->assertInstanceOf(Application::class, $this->application);

        $commands = $this->application->all();
        $this->assertArrayHasKey('search', $commands);
        $this->assertInstanceOf(SearchCommand::class, $commands['search']);
    }

    /**
     * Тестирует получение имени приложения
     */
    public function testApplicationName(): void
    {
        $name = $this->application->getName();
        $this->assertEquals('Bookstore Search', $name);
    }

    /**
     * Тестирует получение версии приложения
     */
    public function testApplicationVersion(): void
    {
        $version = $this->application->getVersion();
        $this->assertEquals('1.0.0', $version);
    }

    /**
     * Тестирует наличие команды search в списке команд
     */
    public function testSearchCommandExists(): void
    {
        $commands = $this->application->all();
        $this->assertArrayHasKey('search', $commands);
    }

    /**
     * Тестирует описание команды search
     */
    public function testSearchCommandDescription(): void
    {
        $commands = $this->application->all();
        $searchCommand = $commands['search'];
        
        $this->assertNotEmpty($searchCommand->getDescription());
    }

    /**
     * Тестирует параметры команды search
     */
    public function testSearchCommandParameters(): void
    {
        $commands = $this->application->all();
        $searchCommand = $commands['search'];
        
        $definition = $searchCommand->getDefinition();
        $this->assertTrue($definition->hasOption('query'));
        $this->assertTrue($definition->hasOption('category'));
        $this->assertTrue($definition->hasOption('max-price'));
        $this->assertTrue($definition->hasOption('in-stock'));
    }
}
