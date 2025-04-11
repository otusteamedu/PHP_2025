<?php

namespace App\Service;

abstract class DB
{
    /** @var string */
    protected string $host;
    /** @var string */
    protected string $password;
    /** @var string */
    protected string $user;
    /** @var string */
    protected string $dbname;
    /** @var string */
    protected string $table;

    /**
     * @param string $table
     * @return DB
     */
    public function table(string $table): DB {
        $this->table = $table;
        return $this;
    }

    /**
     * @param $id
     * @return array|null
     */
    public abstract function find($id): ?array;

    /**
     * @param array $data
     * @return mixed
     */
    public abstract function create(array $data);

    /**
     * @param $id
     * @param array $data
     * @return bool
     */
    public abstract function update($id, array $data): bool;

    /**
     * @param $id
     * @return bool
     */
    public abstract function delete($id): bool;
}