<?php

namespace Blarkinov\PhpDbCourse\Models\Repository\User;

interface UserRepositoryInterface
{
    public function select(string $statement = "", $parameters = []);

    public function delete(string $statement = "", $parameters = []);

    public function insert(string $statement = "", $parameters = []);

    public function update(string $statement = "", $parameters = []);
}
