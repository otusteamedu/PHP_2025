<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http;

final readonly class Route
{
    /**
     * @param Method $method
     * @param string $pattern
     * @param string $controller
     * @param string $action
     */
    public function __construct(
        public Method $method,
        public string $pattern,
        public string $controller,
        public string $action = '__invoke',
    ) {
    }

    /**
     * @param string $uri
     *
     * @return array|null
     */
    public function match(string $uri): ?array
    {
        $regex = preg_replace('#{(\w+)}#', '(?P<$1>[^/]+)', $this->pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $uri, $matches)) {
            return array_filter($matches, is_string(...), ARRAY_FILTER_USE_KEY);
        }

        return null;
    }
}
