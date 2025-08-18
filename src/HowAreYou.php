<?php

namespace Larkinov\HowAreYou;

use Larkinov\Random\Random;

class HowAreYou
{
    private const PHRASES = [
        "I'am tired",
        'I feel great',
        "I'am excited",
        "I'am upset",
        "I'am calm",
        "I'am hungry",
        "I'am happy",
        "I'am depressed",
        "I'am focused",
        "I'am irritated",
    ];

    public function say(): string
    {
        $rand = new Random();
        return $rand->fiftyFifty() ? "I'm tired" : "Let's do it";
    }

    public function sayDetail(): string
    {
        $rand = new Random();
        return self::PHRASES[$rand->getRandomBetween1And10() - 1];
    }
}
