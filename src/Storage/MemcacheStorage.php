<?php

namespace App\Storage;

use Memcached;

class MemcachedStorage implements StorageInterface {
	private Memcached $client;

	public function __construct() {
		$this->client = new Memcached();
		$this->client->addServer($_ENV['MEMCACHED_HOST'] ?: 'memcached', $_ENV['MEMCACHED_PORT'] ?: 11211);
	}

	public function addEvent(array $event): void {
        return;
	}

	public function clearEvents(): void {
        return;
	}

	public function findBestMatch(array $params): ?array {
        return null;
	}
}