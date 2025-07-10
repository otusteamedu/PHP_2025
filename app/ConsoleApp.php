<?php declare(strict_types=1);

namespace EManager;

use EManager\Storage\RedisStorage;
use EManager\Storage\MongoStorage;
use EManager\Storage\ElasticsearchStorage;

class ConsoleApp
{
    private EventManager $eventManager;
    private string $storageType;

    public function __construct(string $storageType)
    {
        $this->storageType = $storageType;

        switch (strtolower($storageType)) {
            case 'redis':
                $storage = new RedisStorage();
                break;
            case 'mongo':
            case 'mongodb':
                $storage = new MongoStorage();
                break;
            case 'elastic':
            case 'elasticsearch':
                $storage = new ElasticsearchStorage();
                break;
            default:
                throw new \InvalidArgumentException("Неподдерживаемый тип хранилища: $storageType");
        }

        $this->eventManager = new EventManager($storage);
    }

    public function run(array $argv): void
    {
        // Первый аргумент (bin/console) пропускаем
        array_shift($argv);

        $storageTypes = ['redis', 'mongo', 'mongodb', 'elastic', 'elasticsearch'];
        $storageType = $this->storageType;

        $commandTypes = ['add', 'clear', 'find'];

        if (isset($argv[0]))
        {
            if (in_array(strtolower($argv[0]), $storageTypes)
                && in_array(strtolower($argv[1]), $commandTypes))
            {
                $storageType = array_shift($argv);
            } elseif (in_array(strtolower($argv[1]), $commandTypes))
            {
                $storageType = array_shift($argv);
                $this->__construct($storageType);
            }
        }

        // Если storageType изменился, пересоздаем менеджер
        if ($storageType !== $this->storageType)
        {
            $this->__construct($storageType);
        }

        $command = $argv[0] ?? null;

        try {
            switch ($command) {
                case 'add':
                    $this->handleAddCommand($argv);
                    break;
                case 'clear':
                    $this->eventManager->clearEvents();
                    echo "Все события были очищены.\n";
                    break;
                case 'find':
                    $this->handleFindCommand($argv);
                    break;
                default:
                    $this->showHelp();
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    private function handleAddCommand(array $argv): void
    {
        if (!isset($argv[1])) {
            throw new \InvalidArgumentException('Для добавления команды требуется событие в формате JSON');
        }

        $eventData = json_decode($argv[1], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Недопустимый формат JSON');
        }

        $this->eventManager->addEvent($eventData);
        echo "Событие было успешно добавлено.\n";
    }

    private function handleFindCommand(array $argv): void
    {
        if (!isset($argv[1])) {
            throw new \InvalidArgumentException('Params JSON is required for find command');
        }

        $matching = json_decode($argv[1], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Недопустимый формат JSON');
        }

        $event = $this->eventManager->findBestMatchingEvent($matching);

        if ($event) {
            echo "Найдено подходящее событие:\n";
            echo json_encode($event, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "Совпадающих событий не найдено.\n";
        }
    }

    private function showHelp(): void
    {
        echo "Event Manager Console Application\n";
        echo "Usage:\n";
        echo "  php bin/console <storage> add '<event>'\n";
        echo "  php bin/console <storage> clear\n";
        echo "  php bin/console <storage> find '<message>'\n";
    }
}