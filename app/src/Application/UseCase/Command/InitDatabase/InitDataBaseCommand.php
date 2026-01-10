<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\InitDatabase;

class InitDataBaseCommand
{
    public function __construct(public string $dbName, public ?array $mappings = null, public ?array $settings = null)
    {
    }

}