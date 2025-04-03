<?php

namespace App\Commands;

use App\Services\InputService;

abstract class Command
{
    /** @var string */
    protected static string $name;
    /** @var string */
    protected static string $description;
    /** @var InputService */
    protected InputService $inputService;

    /**
     * @param InputService $inputService
     */
    public function __construct(InputService $inputService) {
        $this->inputService = $inputService;
    }

    /**
     * @return mixed
     */
    public function execute() {
        return $this->handle();
    }

    /**
     * @return string
     */
    public static function getName(): string {
        return static::$name;
    }

    /**
     * @return mixed
     */
    abstract public function handle();
}