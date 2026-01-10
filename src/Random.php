<?php

namespace Larkinov\Random;

class Random
{
    public function fiftyFifty(): bool
    {
        return random_int(0, 1) ? true : false;
    }

    public function getRandomBetween1And10():int{
        return random_int(1,10);
    }

}
