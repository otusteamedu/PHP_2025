<?php

namespace App\Base;

abstract class Singleton
{

    /**
     * Массив экземпляров для каждого наследника.
     * @var array<string, object>
     */
    private static array $instances = [];

    /**
     *
     */
    protected function __construct()
    {
    }

    /**
     * @return void
     */
    protected function __clone()
    {
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }


    /**
     * Возвращает единственный экземпляр вызывающего класса.
     *
     * @return static
     */
    public static function getInstance(): static
    {
        $className = static::class;
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new static();
        }
        return self::$instances[$className];
    }
}