<?php
declare(strict_types=1);

namespace App\Domain\ValueObject;

class Url
{
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidUrl($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidUrl(string $value)
    {
        if (!preg_match('~^(https?://)?(([^/]+?\.[a-z]{2,8})|([12]?[0-9]{2}\.[12]?[0-9]{2}\.[12]?[0-9]{2}\.[12]?[0-9]{2}\.))(\:\d+)?/.+$~', $value)) {
            throw new \InvalidArgumentException('URL is not valid.');
        }
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        debug_print_backtrace(3);
        exit();
    }


}