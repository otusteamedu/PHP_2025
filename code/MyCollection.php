<?php

class MyCollection
{
    const string KEY = "collection";

    private Redis $redis;

    public function __construct()
    {
        $redis = new Redis();
        $redis->connect("redis", 6379);
        $this->redis = $redis;
    }

    public function add(string $key, mixed $content): void
    {
        $this->redis->rpush($this->key($key), json_encode($content));
    }

    public function get(string $key): mixed
    {
        return json_decode($this->redis->lpop($this->key($key)), true);
    }

    private function key(string $key): string
    {
        return self::KEY . "/" . $key;
    }
}