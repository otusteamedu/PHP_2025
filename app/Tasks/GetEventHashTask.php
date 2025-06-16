<?php

namespace App\Tasks;

class GetEventHashTask extends Task
{
    /**
     * @param array $params
     * @return string
     */
    public function run(array $params): string {
        ksort($params);
        return md5(serialize($params));
    }
}