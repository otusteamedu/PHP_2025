<?php

namespace Restaurant\Domain\Interfaces;

interface ObserverInterface
{
    public function update($data): void;
}
