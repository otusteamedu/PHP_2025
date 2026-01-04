<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\ES\Queries;

readonly class FuzzinessQuery extends AbstractQuery
{
    /**
     * @param string $field
     * @param string $value
     * @param string|int $fuzziness
     */
    public function __construct(protected string $field, protected string $value, protected string|int $fuzziness = 'AUTO')
    {
    }

    /**
     * @return string[][]
     */
    public function toArray(): array
    {
        return ['match' => [$this->field => ['query' => $this->value, 'fuzziness' => $this->fuzziness]]];
    }
}
