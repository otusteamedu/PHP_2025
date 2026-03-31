<?php
use PHPUnit\Framework\TestCase;
use App\Service\TodoService;
use App\Repository\InMemoryTodoRepository;

class TodoServiceTest extends TestCase {
    public function testAddTodo(): void {
        $service = new TodoService(new InMemoryTodoRepository());
        $todo = $service->addTodo("Read book");
        $this->assertEquals("Read book", $todo->getTitle());
    }

    public function testAddTodoWithEmptyTitle(): void {
        $this->expectException(\InvalidArgumentException::class);
        $service = new TodoService(new InMemoryTodoRepository());
        $service->addTodo("  ");
    }

    public function testGetTodos(): void {
        $service = new TodoService(new InMemoryTodoRepository());
        $service->addTodo("Task 1");
        $service->addTodo("Task 2");
        $this->assertCount(2, $service->getTodos());
    }

    public function testDeleteTodoReturnsTrueWhenDeleted(): void {
        $repo = new InMemoryTodoRepository();
        $service = new TodoService($repo);
        $todo = $service->addTodo("Task to delete");

        $result = $service->deleteTodo($todo->getId());
        $this->assertTrue($result);
        $this->assertCount(0, $service->getTodos());
    }

    public function testDeleteTodoReturnsFalseWhenIdNotFound(): void {
        $service = new TodoService(new InMemoryTodoRepository());
        $result = $service->deleteTodo(999);
        $this->assertFalse($result);
    }

    public function testAddMultipleTodosAndGetAll(): void {
        $service = new TodoService(new InMemoryTodoRepository());
        $titles = ["Buy milk", "Write tests", "Go jogging"];

        foreach ($titles as $title) {
            $service->addTodo($title);
        }

        $todos = $service->getTodos();
        $this->assertCount(count($titles), $todos);

        foreach ($todos as $index => $todo) {
            $this->assertEquals($titles[$index], $todo->getTitle());
        }
    }

    public function testAddTodoTrimsTitle(): void {
        $service = new TodoService(new InMemoryTodoRepository());
        $todo = $service->addTodo("  Trim me  ");
        $this->assertEquals("Trim me", $todo->getTitle());
    }
}
