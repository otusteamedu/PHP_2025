<?php declare(strict_types=1);

namespace EManager\Storage;

class StorageFactory
{
	public static function create(string $storageType): StorageInterface
	{
		return match (strtolower($storageType))
		{
			'redis' => new RedisStorage(),
			'mongo', 'mongodb' => new MongoStorage(),
			'elastic', 'elasticsearch' => new ElasticsearchStorage(),
			default => throw new \InvalidArgumentException("Неподдерживаемый тип хранилища: $storageType"),
		};
	}
}