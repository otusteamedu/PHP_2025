<?php

namespace App\Services;

use Redis;

class RedisService implements ServiceInterface
{
    private $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('redis', 6379);
    }

    public function add(string $event, int $priority, array $conditions):string
    {
        $conditions1 = [];
        foreach ($conditions as $key => $val) {
            $conditions1[] = $key . ':' . $val;
        }
        $conditionStr = implode('::', $conditions1);

        $this->redis->hSet('events', $event, $conditionStr);
        $this->redis->zAdd('priorities', $priority, $event);

        $message = 'Событие добавлено в систему';
        return $message;
    }

    public function answer(array $parameters): string
    {
        $params = [];
        foreach ($parameters as $par => $val) {
            $params[] = "$par:$val";
        }

        $events = $this->redis->hGetAll('events');
        $rightEvents = [];
        foreach ($events as $ev => $conditions) {
            $condArr = explode('::', $conditions);
            $res = true;
            foreach ($params as $par) {
                if (!in_array($par, $condArr)) {
                    $res = false;
                    break;
                }
            }
            if ($res) {
                $rightEvents[] = $ev;
            }
        }

        if (empty($rightEvents)) {
            return 'Нет событий';
        }

        $event = '';
        $currentPriority = 0;
        foreach ($rightEvents as $ev) {
            $priority = $this->redis->zScore('priorities', $ev);
            if ($priority > $currentPriority) {
                $currentPriority = $priority;
                $event = $ev;
            }
        }

        return $event;
    }

    public function getEvents(): string
    {
        $events = $this->redis->hGetAll('events');
        $priorities = $this->redis->zRevRange('priorities', 0, -1, ['WITHSCORES' => true]);

        $message = '';
        foreach ($events as $ev => $cond) {
            $cond1 = explode('::', $cond);
            $conditions = [];
            foreach ($cond1 as $item) {
                [$key, $val] = explode(':', $item);
                $conditions[$key] = $val;
            }
            $arr = ['event' => $ev, 'conditions' => $conditions, 'priority' => $priorities[$ev]];
            $message .= json_encode($arr) . PHP_EOL;
        }

        $message = !empty($message) ? $message : 'Нет зарегистрированных событий';

        return $message;
    }

    public function clear(): bool
    {
        $this->redis->del('events');
        $this->redis->del('priorities');
        return true;
    }
}