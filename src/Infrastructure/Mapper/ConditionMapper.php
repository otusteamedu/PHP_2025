<?php

namespace App\Infrastructure\Mapper;

use App\Domain\Entity\Condition;
use App\Domain\ValueObject\Condition\ConditionName;
use App\Domain\ValueObject\Condition\ConditionValue;

class ConditionMapper
{
    public static function toArray(Condition $condition): array
    {
        return [$condition->getName() => $condition->getValue()];
    }

    public static function getKeyForRedis(Condition $condition): string
    {
        return "conditions:{$condition->getName() }:{$condition->getValue()}";
    }

    /**
     * @param Condition[] $conditions
     * @return array
     */
    public static function getConditionsToRedisData(array $conditions): array
    {
        $conditionsRedis = [];

        foreach ($conditions as $condition) {
            if ($condition instanceof Condition) {
                $conditionsRedis[] = self::getKeyForRedis($condition);
            }
        }

        return $conditionsRedis;
    }

    /** создаёт из строки "param1:paramValue1;param2:paramValue2" массив
     * @param $stringConditions
     * @return Condition[]
     */
    public static function createFromString($stringConditions): array
    {
        $conditions = \explode(';', $stringConditions);
        $arrConditions = [];


        if (\count($conditions) > 0) {
            foreach ($conditions as $condition) {
                [$conditionName, $conditionValue] = \explode(':', $condition);
                $arrConditions[] = new Condition(
                    new ConditionName($conditionName),
                    new ConditionValue($conditionValue)
                );
            }
        }

        return $arrConditions;
    }
}
