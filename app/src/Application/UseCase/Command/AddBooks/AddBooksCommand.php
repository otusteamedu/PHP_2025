<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\AddBooks;

class AddBooksCommand
{
    public function __construct(public string $dbName, public string $filePath)
    {
    }

}