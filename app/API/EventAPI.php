<?php

declare(strict_types=1);

namespace App\API;

use App\Manager\EventManager;
use App\Storage\RedisEventStorage;
use App\Storage\TarantoolEventStorage;
use Exception;

class EventAPI
{
    private EventManager $eventManager;
    
    /**
     * Инициализирует API с выбранным типом хранилища
     */
    public function __construct()
    {
        $storageType = $_GET['storage'] ?? 'redis';
        
        if ($storageType === 'tarantool') {
            $storage = new TarantoolEventStorage();
        } else {
            $storage = new RedisEventStorage();
        }

        $this->eventManager = new EventManager($storage);
    }
    
    /**
     * Обрабатывает HTTP запрос и маршрутизирует к соответствующему методу
     */
    public function handleRequest(): void
    {
        header('Content-Type: application/json');
        
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $action = $_GET['action'] ?? '';

            switch ($method) {
                case 'POST':
                    $this->handlePost($action);
                    break;

                case 'DELETE':
                    $this->handleDelete($action);
                    break;

                case 'GET':
                default:
                    $this->handleGet();
                    break;
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Обрабатывает POST запросы: add, find, clear
     */
    private function handlePost(string $action): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'add') {
            $priority = $input['priority'] ?? 0;
            $conditions = $input['conditions'] ?? [];
            $eventData = $input['event'] ?? [];
            
            $result = $this->eventManager->addEvent($priority, $conditions, $eventData);
            echo json_encode(['success' => $result, 'message' => $result ? 'Event added' : 'Failed to add event']);
            
        } elseif ($action === 'find') {
            $params = $input ?? [];
            $event = $this->eventManager->findBestEvent($params);
            
            if ($event) {
                echo json_encode([
                    'success' => true,
                    'event' => $event->toArray()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No matching event found']);
            }
        } elseif ($action === 'clear') {
            $result = $this->eventManager->clearAllEvents();
            echo json_encode(['success' => $result, 'message' => $result ? 'All events cleared' : 'Failed to clear events']);
        }
    }
    
    /**
     * Обрабатывает DELETE запросы: clear
     */
    private function handleDelete(string $action): void
    {
        if ($action === 'clear') {
            $result = $this->eventManager->clearAllEvents();
            echo json_encode(['success' => $result, 'message' => $result ? 'All events cleared' : 'Failed to clear events']);
        }
    }
    
    /**
     * Обрабатывает GET запросы (получение всех событий)
     */
    private function handleGet(): void
    {
        $events = $this->eventManager->getAllEvents();
        $eventsArray = array_map(fn($event) => $event->toArray(), $events);
        echo json_encode(['success' => true, 'events' => $eventsArray, 'count' => count($events)]);
    }
} 