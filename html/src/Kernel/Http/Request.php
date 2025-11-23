<?php

declare(strict_types=1);

namespace Otus\Kernel\Http;

use Otus\Kernel\ValueObject;

readonly class Request
{
    /**
     * @param ValueObject $get
     * @param ValueObject $post
     * @param ValueObject $session
     * @param ValueObject $server
     */
    public function __construct(
        public ValueObject $get,
        public ValueObject $post,
        public ValueObject $session,
        public ValueObject $server,
    ) {
    }
}
