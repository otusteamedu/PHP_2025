<?php

declare(strict_types=1);

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function isSelected(string $actual, string $expected): string
{
    return $actual === $expected ? 'selected' : '';
}
