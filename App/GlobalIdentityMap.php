<?php

namespace App;

class GlobalIdentityMap
{
    private array $all = [];
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function globalKey(object $obj): string
    {
        return \get_class($obj) . "." . $obj->getId();
    }

    public static function add(object $obj): void
    {
        $inst = self::instance();
        $inst->all[$inst->globalKey($obj)] = $obj;
    }

    public static function exists(string $classname, int $id): ?object
    {
        $inst = self::instance();
        $key = "{$classname} . {$id}";

        if (isset($inst->all[$key])) {
            return $inst->all[$key];
        }

        return null;
    }
}
