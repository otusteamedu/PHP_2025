<?php declare(strict_types=1);

namespace App\Elastic;

/**
 * Class Query
 * @package App\Elastic
 */
class Query
{
    /**
     * @var array
     */
    private array $query = [
        'query' => [
            'bool' => [
                'must' => [],
                'should' => [],
                'filter' => [],
                'must_not' => [],
            ]
        ]
    ];

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     * @return void
     */
    public function addMustQuery(array $query): void
    {
        $this->query['query']['bool']['must'][] = $query;
    }

    /**
     * @param array $query
     * @return void
     */
    public function addShouldQuery(array $query): void
    {
        $this->query['query']['bool']['should'][] = $query;
    }

    /**
     * @param array $query
     * @return void
     */
    public function addFilterQuery(array $query): void
    {
        $this->query['query']['bool']['filter'][] = $query;
    }

    /**
     * @param array $query
     * @return void
     */
    public function addMustNotQuery(array $query): void
    {
        $this->query['query']['bool']['must_not'][] = $query;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $params
     * @return array
     */
    public function prepareMatch(string $attribute, mixed $value, array $params = []): array
    {
        $result = [];

        if (empty($params)) {
            $result['match'] = [
                $attribute => $value,
            ];
        } else {
            $result['match'] = [
                $attribute => array_merge(
                    [
                        'query' => $value,
                    ],
                    $params
                ),
            ];
        }

        return $result;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return array
     */
    public function prepareTerm(string $attribute, mixed $value): array
    {
        return [
            'term' => [
                $attribute => $value,
            ],
        ];
    }

    /**
     * @param string $attribute
     * @param string $operator
     * @param mixed $value
     * @return array
     */
    public function prepareRange(string $attribute, string $operator, mixed $value): array
    {
        return [
            'range' => [
                $attribute => [
                    $operator => $value,
                ],
            ],
        ];
    }
}
