<?php

namespace App\Forms;

use App\Models\Condition;
use InvalidArgumentException;

class EventSearch
{
    /**
     * @var Condition[]
     */
    private array $conditions = [];

    /**
     * @param array $conditions
     */
    public function __construct(array $conditions)
    {
        if (empty($conditions)) {
            throw new InvalidArgumentException('Conditions must not be empty.');
        }

        foreach ($conditions as $name => $value) {
            $this->conditions[] = new Condition($name, (string)$value);
        }
    }

    /**
     * @param string $json
     * @return self
     */
    public static function createFromJson(string $json): self
    {
        $data = json_decode($json, true);

        if (!$data) {
            throw new InvalidArgumentException('Incorrect json');
        }

        return new EventSearch($data['params'] ?? []);
    }

    /**
     * @return Condition[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }
}
