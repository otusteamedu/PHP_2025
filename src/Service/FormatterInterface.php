<?php

declare(strict_types=1);


namespace Dinargab\Homework5\Service;

interface FormatterInterface
{
    public function format(array $returnArray): string;
}