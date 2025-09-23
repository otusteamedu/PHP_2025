<?php
declare(strict_types=1);

namespace App\EventSystem;

final readonly class EventMatcher
{
    public function __construct(private EventSystemRepository $repository)
    {
    }

    public function findBestMatch(array $params): ?array
    {
        $ids = $this->repository->getEventIdsByPriorityDesc();

        foreach ($ids as $id) {
            $stored = $this->repository->get($id);
            if ($stored === null) {
                continue;
            }

            if ($this->matchesConditions($stored['conditions'], $params)) {
                return [
                    'id' => $id,
                    'priority' => $stored['priority'],
                    'event' => $stored['event'],
                    'conditions' => $stored['conditions'],
                ];
            }
        }

        return null;
    }


    private function matchesConditions(array $conditions, array $params): bool
    {
        foreach ($conditions as $k => $v) {
            if (!array_key_exists($k, $params)) {
                return false;
            }

            if ((string)$params[$k] !== (string)$v) {
                return false;
            }
        }

        return true;
    }
}
