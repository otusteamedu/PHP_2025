<?php

declare(strict_types=1);

namespace App\Application\Command;

use InvalidArgumentException;

/**
 * Команда поиска события по параметрам пользовательского запроса
 */
final class FindEventCommand
{
    /**
     * @var array<string, int|float|string|bool|null>
     */
    private array $params;

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params)
    {
        foreach ($params as $name => $value) {
            if (!is_string($name) || $name === '') {
                throw new InvalidArgumentException('Param name must be a non-empty string.');
            }

            if (!is_scalar($value) && $value !== null) {
                throw new InvalidArgumentException('Param value must be scalar or null.');
            }
        }

        $this->params = $params;
    }

    /**
     * @return array<string, int|float|string|bool|null>
     */
    public function params(): array
    {
        return $this->params;
    }
}
