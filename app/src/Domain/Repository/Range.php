<?php
declare(strict_types=1);


namespace App\Domain\Repository;

class Range
{
    public ?int $max = null {
        get {
            return $this->max;
        }
        set {
            $this->max = $value;
        }
    }
    public ?int $min = null {
        get {
            return $this->min;
        }
        set {
            $this->min = $value;
        }
    }

    public function isEmpty(): bool
    {
        return $this->max === null && $this->min === null;
    }

}