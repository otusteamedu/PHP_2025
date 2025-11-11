<?php

namespace App\Infrastructure\Storage;

use App\Application\StorageEventInterface;
use App\Infrastructure\Mapper\EventMapper;
use Exception;

class StorageEvent implements StorageEventInterface
{
    private const STORAGE_DEFAULT = 'redis';

    private function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public static function createStorage(string $storageName = self::STORAGE_DEFAULT): StorageEventDBInterface
    {
        $storageMap = self::getStorageMap();

        if ($storageName === '') {
            $storageName = self::STORAGE_DEFAULT;
        }

        if (!array_key_exists($storageName, $storageMap)) {
            throw new Exception('Хранилища "' . $storageName . '" с таким именем нет. Доступные имена: ' . self::getStorageNames());
        }

        return $storageMap[$storageName]();
    }

    /**
     * @return array{
     *     redis: Closure(): StorageEventDBInterface,
     *     es: Closure(): StorageEventDBInterface
     * }
     */
    private static function getStorageMap(): array
    {
        return [
            'redis' => fn() => new StorageRedisEvent(),
            'es' => fn() => new StorageElasticsearchEvent(),
        ];
    }

    private static function getStorageNames(): string
    {
        return \implode(', ', \array_keys(self::getStorageMap()));
    }

    /**
     * @return string[]
     */
    public static function getStorageNamesArray(): array
    {
        return \array_keys(self::getStorageMap());
    }

    public static function addTestData(): void
    {
        $dataEvents = [];

        $dataEvents[] = EventMapper::createFromArray([
            'priority' => 1000,
            'event' => ['id' => 1, 'name' => 'eventName1'],
            'conditions' => [
                'param1' => 'paramValue1',
            ]
        ]);

        $dataEvents[] = EventMapper::createFromArray([
            'priority' => 2000,
            'event' => ['id' => 2, 'name' => 'eventName2'],
            'conditions' => [
                'param2' => 'paramValue2',
                'param3' => 'paramValue3',
            ]
        ]);

        $dataEvents[] = EventMapper::createFromArray([
            'priority' => 3000,
            'event' => ['id' => 3, 'name' => 'eventName3'],
            'conditions' => [
                'param1' => 'paramValue1',
                'param2' => 'paramValue2',
            ]
        ]);

        foreach (self::getStorageMap() as $storageCreteFn) {
            $storage = $storageCreteFn();

            if ($storage instanceof StorageEventDBInterface) {
                $storage->dataToDatabase($dataEvents);
            }
        }
    }
}
