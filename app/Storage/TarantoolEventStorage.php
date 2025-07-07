<?php

declare(strict_types=1);

namespace App\Storage;

use App\Contracts\EventStorageInterface;
use App\Models\Event;
use Tarantool\Client\Client;
use Exception;

class TarantoolEventStorage implements EventStorageInterface
{
    private Client $client;
    
    private string $spaceName = 'events';

    public function __construct()
    {
        $host = getenv('TARANTOOL_HOST') ?: 'tarantool';
        $port = (int) (getenv('TARANTOOL_PORT') ?: 3301);
        
        try {
            $dsn = "tcp://{$host}:{$port}";
            $this->client = Client::fromDsn($dsn);
        } catch (Exception $e) {
            throw new Exception("Failed to connect to Tarantool: " . $e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function addEvent(Event $event): bool
    {
        try {
            // используется Lua код для добавления события
            $id = $event->getId();
            $priority = $event->getPriority();
            $conditions = json_encode($event->getConditions());
            $eventData = json_encode($event->getEventData());
            
            $luaCode = "
                local json = require('json')
                local id = '{$id}'
                local priority = {$priority}
                local conditions = json.decode('{$conditions}')
                local event_data = json.decode('{$eventData}')
                local created_at = os.time()
                
                return box.space.events:insert{id, priority, conditions, event_data, created_at}
            ";
            
            $result = $this->client->evaluate($luaCode);
            
            return !empty($result);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function clearAllEvents(): bool
    {
        try {
            $result = $this->client->evaluate('
                box.space.events:truncate()
                return true
            ');
            return $result[0] === true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function findBestMatchingEvent(array $params): ?Event
    {
        try {
            // используется Lua код для поиска событий
            $paramsJson = json_encode($params);
            
            $luaCode = "
                local json = require('json')
                local params = json.decode('{$paramsJson}')
                local matching_events = {}
                
                for _, tuple in box.space.events.index.priority:pairs(nil, {iterator = 'REQ'}) do
                    local conditions = tuple[3]
                    local matches = true
                    
                    for key, value in pairs(params) do
                        if conditions[key] == nil or tostring(conditions[key]) ~= tostring(value) then
                            matches = false
                            break
                        end
                    end
                    
                    if matches then
                        table.insert(matching_events, {
                            id = tuple[1],
                            priority = tuple[2],
                            conditions = tuple[3],
                            event = tuple[4],
                            created_at = tuple[5]
                        })
                    end
                end
                
                return matching_events
            ";
            
            $result = $this->client->evaluate($luaCode);
            
            if (empty($result[0])) {
                return null;
            }
            
            $events = $result[0];
            
            $matchingEvents = [];
            foreach ($events as $eventData) {
                $matchingEvents[] = Event::fromArray([
                    'id' => $eventData['id'],
                    'priority' => $eventData['priority'],
                    'conditions' => $eventData['conditions'],
                    'event' => $eventData['event']
                ]);
            }
            
            if (empty($matchingEvents)) {
                return null;
            }
            
            return $matchingEvents[0];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getAllEvents(): array
    {
        try {
            $result = $this->client->evaluate('
                local events = {}
                for _, tuple in box.space.events.index.priority:pairs(nil, {iterator = "REQ"}) do
                    table.insert(events, {
                        id = tuple[1],
                        priority = tuple[2],
                        conditions = tuple[3],
                        event = tuple[4],
                        created_at = tuple[5]
                    })
                end
                return events
            ');
            
            if (empty($result[0])) {
                return [];
            }
            
            $events = [];
            foreach ($result[0] as $eventData) {
                $events[] = Event::fromArray([
                    'id' => $eventData['id'],
                    'priority' => $eventData['priority'],
                    'conditions' => $eventData['conditions'],
                    'event' => $eventData['event']
                ]);
            }
            
            return $events;
        } catch (Exception $e) {
            return [];
        }
    }
} 