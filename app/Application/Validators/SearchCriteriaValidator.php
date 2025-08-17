<?php

declare(strict_types=1);

namespace App\Application\Validators;

use App\Domain\Models\SearchCriteria;
use InvalidArgumentException;

final readonly class SearchCriteriaValidator
{
    /**
     * Валидирует критерии поиска
     */
    public function validate(SearchCriteria $criteria): void
    {
        if ($criteria->getMaxPrice() !== null && $criteria->getMaxPrice() < 0) {
            throw new InvalidArgumentException('Максимальная цена не может быть отрицательной');
        }
    }
}
