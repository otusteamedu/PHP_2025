<?php

namespace Src\Infrastructure\Http;
use Src\Infrastructure\Queue\Producer\Producer;

class Common {

    public function __invoke() {

        (new Producer)();

    }

}
