<?php

namespace App\Service;

abstract class DB
{
    protected string $host;
    protected string $password;
    protected string $user;
    protected string $dbname;
    protected string $table;

    public function table(string $table): DB {
        $this->table = $table;
        return $this;
    }

    public abstract function fetch(int $id): ?array;

    public abstract function fetchAll(): ?array;

    public abstract function create(array $data);

    public abstract function update(array $data): bool;

    public abstract function delete(int $id): bool;
}