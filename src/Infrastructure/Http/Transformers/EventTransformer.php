<?php

namespace Infrastructure\Http\Transformers;

class EventTransformer
{
    public static function transform(array $events): array {
        $data = [];

        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'type' => $event->getType(),
                'title' => $event->getTitle(),
                'priority' => $event->getPriority(),
                'comment' => $event->getComment(),
                'createdAt' => $event->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $event->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $data;
    }
}