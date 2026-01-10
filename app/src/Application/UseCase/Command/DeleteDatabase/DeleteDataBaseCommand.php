<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\DeleteDatabase;

class DeleteDataBaseCommand
{
    public function __construct(public string $dbName)
    {
    }

}