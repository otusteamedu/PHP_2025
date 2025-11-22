<?php

namespace Blarkinov\Hw1500\Application\UseCase\Request;


class ChangeOrderStatusDto
{

    public function __construct(
        private int $id,
        private string $status,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
}
