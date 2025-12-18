<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Infrastructure\Elasticsearch\ElasticsearchService;
use App\Model\Event;
use Closure;
use Exception;

class Storage
{
    protected const STORAGE_DEFAULT = 'redis';

    private function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public static function createStorage(string $storageName = self::STORAGE_DEFAULT): StorageEventInterface
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
     *     redis: Closure(): StorageEventInterface,
     *     es: Closure(): StorageEventInterface
     * }
     */
    private static function getStorageMap(): array
    {
        return [
            'redis' => fn() => new StorageRedisEvent(),
            'es' => fn() => new StorageEsEvent(new ElasticsearchService()),
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
        $dataEvents = [
            new Event(
                1000,
                ['id' => 1, 'name' => 'eventName1'],
                [
                    'param1' => 'paramValue1',
                ]
            ),
            new Event(
                2000,
                ['id' => 2, 'name' => 'eventName2'],
                [
                    'param2' => 'paramValue2',
                    'param3' => 'paramValue3',
                ]
            ),
            new Event(
                3000,
                ['id' => 3, 'name' => 'eventName3'],
                [
                    'param1' => 'paramValue1',
                    'param2' => 'paramValue2',
                ]
            )
        ];

        foreach (self::getStorageMap() as $storageCreteFn) {
            $storage = $storageCreteFn();

            if ($storage instanceof StorageEventInterface) {
                $storage->dataToDatabase($dataEvents);
            }
        }
    }
}
