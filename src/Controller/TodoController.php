<?php

namespace App\Controller;

use App\Service\TodoService;

class TodoController {
    public function __construct(private TodoService $service) {
        header('Content-Type: application/json');
    }

    public function index(): void {
        $todos = $this->service->getTodos();
        echo json_encode(array_map(fn($todo) => $todo->toArray(), $todos));
    }

    public function create(): void {
        $input = json_decode(file_get_contents("php://input"), true);
        try {
            $todo = $this->service->addTodo($input['title'] ?? '');
            http_response_code(201);
            echo json_encode($todo->toArray());
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void {
        if ($this->service->deleteTodo($id)) {
            http_response_code(204);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        }
    }
}
