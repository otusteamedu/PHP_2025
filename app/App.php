<?php

namespace App;

use App\Commands\OtusShopCreateCommand;
use App\Commands\OtusShopBulkCommand;
use App\Commands\OtusShopDropCommand;
use App\Commands\OtusShopSearchCommand;
use App\Services\InputService;
use Exception;
use Throwable;

class App
{
    /**
     * @var array|class-string[]
     */
    protected array $commands = [
        OtusShopBulkCommand::class,
        OtusShopCreateCommand::class,
        OtusShopSearchCommand::class,
        OtusShopDropCommand::class,
    ];

    /**
     * @return void
     */
    public function run() {
        try {
            $inputService = (new InputService());
            $commandName = $inputService->getCommandName();
            $command = $this->searchCommand($commandName);
            (new $command($inputService))->execute();
            echo "\nSUCCESS\n";
        } catch (Exception $e) {
            echo "\nEXCEPTION: " . $e->getMessage() ?? "Команда была завершена исключением\n";
        } catch (Throwable $e) {
            echo "\nERROR: " . $e->getMessage() ?? "Команда завершена с ошибкой\n";
            echo "\n" . $e->getTraceAsString() . "\n";
        }
    }

    /**
     * @param string $commandName
     * @return mixed|string
     * @throws Exception
     */
    public function searchCommand(string $commandName) {
        foreach ($this->commands as $command) {
            if ($command::getName() === $commandName) {
                return $command;
            }
        }

        throw new Exception("Команда не была найдена");
    }
}