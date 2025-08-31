<?php

declare(strict_types=1);

namespace Dinargab\Homework5\Result;

interface ResultInterface
{
    public function getInputValue();
    public function isValid(): bool;
    public function getError(): ?string;
}
