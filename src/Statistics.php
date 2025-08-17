<?php

namespace Elisad5791\Phpapp;

class Statistics 
{
    const INDEX_NAME = 'books';

    public function __construct(
        private ElasticSearchClientInterface $elasticClient
    ) {}

    public function getAverageRatingBySubject() {
        $query = [
            'index' => self::INDEX_NAME,
            'body' => [
                'aggs' => [
                    'subjects' => [
                        'terms' => ['field' => 'subject_id.keyword'],
                        'aggs' => ['avg_rating' => ['avg' => ['field' => 'rating']]]
                    ]
                ]
            ]
        ];

        $result = $this->elasticClient->search($query);
        return $result;
    }

    public function getLongestBooks(int $limit = 5) {
        $query = [
            'index' => self::INDEX_NAME,
            'body' => [
                'sort' => [['page_count' => ['order' => 'desc']]],
                'size' => $limit
            ]
        ];

        $result = $this->elasticClient->search($query);
        return $result;
    }
}