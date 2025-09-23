<?php

namespace BookstoreApp\Application\Command;

interface CommandInterface
{
    public function execute(array $args = []): void;
    public function getDescription(): string;
}