<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025Hw4;

class Numbers
{
    private const MIN = 1;
    private const MAX = 10;

    /**
     * @throws \Exception
     */
    public function getRandom(): int
    {
        return random_int(self::MIN, self::MAX);
    }
}
