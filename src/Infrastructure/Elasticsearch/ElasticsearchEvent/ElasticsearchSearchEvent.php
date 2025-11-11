<?php

namespace App\Infrastructure\Elasticsearch\ElasticsearchEvent;

class ElasticsearchSearchEvent
{
    private ElasticsearchIndexEvent $event;

    public function __construct(ElasticsearchIndexEvent $event)
    {
        $this->event = $event;
    }
    /** поиск
     * @param array $query
     * @param array $options
     * @return array
     */
    public function search(array $query, array $options = []): array
    {
        $resul = $this->event->searchDocument($query, $options);

        if (isset($resul['hits']['total']['value']) && $resul['hits']['total']['value'] > 0) {
            return $this->transformEsDocumentEventDataToArray($resul['hits']['hits'][0]['_source']);
        }

        return [];
    }

    private function transformEsDocumentEventDataToArray(array $data): array
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if (
                $key === 'conditions' && \is_array($value) && \count($value) > 0 &&
                \is_array($value['conditionName']) && \is_array($value['conditionValue'])
            ) {
                $keys = \array_values($value['conditionName']);
                $values = \array_values($value['conditionValue']);

                $newData[$key] = \array_combine($keys, $values);
            } else {
                $newData[$key] = $value;
            }
        }

        return $newData;
    }
}
