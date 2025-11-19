<?php

declare(strict_types=1);

namespace Otus\Kernel\Http;

use Stringable;

readonly class Response implements Stringable
{
    /**
     * @param string $content
     * @param array $headers
     */
    public function __construct(
        public string $content,
        public array $headers = [],
    ) {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        foreach ($this->headers as $header) {
            header($header);
        }

        return $this->content;
    }
}
