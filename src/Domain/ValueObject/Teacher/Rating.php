<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\Teacher;

final class Rating
{
    public function __construct(private float $rating)
    {
        $this->assertRatingIsValid($rating);
    }

    public function getRating():float
    {
        return $this->rating;
    }

    private function assertRatingIsValid(float $value):void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Invalid rating');
        }
    }
}
