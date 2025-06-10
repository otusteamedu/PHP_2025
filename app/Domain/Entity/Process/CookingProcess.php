<?php
declare(strict_types=1);

namespace App\Domain\Entity\Process;

class CookingProcess
{

    public const PREPARED = 'prepared';
    public const READY = 'ready';
    public const BAD = 'bad';

    private string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}