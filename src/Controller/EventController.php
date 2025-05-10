<?php
namespace App\Controller;

use App\Service\EventService;

class EventController {
    private EventService $service;

    public function __construct(EventService $service) {
        $this->service = $service;
    }

    public function handleRequest(): void {
        $action = $_POST['action'] ?? null;

        if ($action === 'add') {
            $priority = (int)$_POST['priority'];

            $keys = $_POST['paramName'];
            $values = $_POST['paramValue'];
            $conditions = [];
            foreach ($keys as $index => $key) {
                if (trim($key) !== '') {
                    $conditions[$key] = (int)$values[$index]; 
                }
            }

            $event = [
                'event' => $_POST['event']
            ];

            $this->service->addEvent([
                'priority' => $priority,
                'conditions' => $conditions,
                'event' => $event,
            ]);

            echo "Событие добавлено";
            echo '<br><a href="index.php">Вернуться назад</a>';

        } elseif ($action === 'clear') {
            $this->service->clearEvents();
            echo "Все события очищены";
            echo '<br><a href="index.php">Вернуться назад</a>';

        } elseif ($action === 'search') {
            $keys = $_POST['searchParamName'];
            $values = $_POST['searchParamValue'];
            $params = [];
            foreach ($keys as $index => $key) {
                if (trim($key) !== '') {
                    $params[$key] = (int)$values[$index]; 
                }
            }

            $result = $this->service->getBestMatch($params);

            echo "Лучшее событие: " . json_encode($result, JSON_UNESCAPED_UNICODE);
            echo '<br><a href="index.php">Вернуться назад</a>';

        } else {
            include __DIR__ . '/../../templates/index.html';
        }
    }
}
