<?php
// Код после
interface TaskStorageInterface {
    public function save(array $tasks);
    public function load(): array;
}

interface TaskFactoryInterface {
    public function create(string $title, string $dueDate): Task;
    public function createFromArray(array $data): Task;
}

class DefaultTaskFactory implements TaskFactoryInterface {
    public function create(string $title, string $dueDate): Task {
        return new Task($title, $dueDate);
    }

    public function createFromArray(array $data): Task {
        return Task::fromArray($data);
    }
}

class FileTaskStorage implements TaskStorageInterface {
    private $filename;
    private $taskFactory;

    public function __construct($filename, TaskFactoryInterface $taskFactory) {
        $this->filename = $filename;
        $this->taskFactory = $taskFactory;
    }

    public function save(array $tasks) {
        $array = array_map(fn($task) => $task->toArray(), $tasks);
        file_put_contents($this->filename, json_encode($array, JSON_PRETTY_PRINT));
    }

    public function load(): array {
        if (!file_exists($this->filename)) return [];

        $data = json_decode(file_get_contents($this->filename), true);
        if (!is_array($data)) return [];

        return array_map(fn($item) => $this->taskFactory->createFromArray($item), $data);
    }
}

class Task {
    public $title;
    public $dueDate;
    public $completed = false;

    public function __construct($title, $dueDate, $completed = false) {
        $this->title = $title;
        $this->dueDate = $dueDate;
        $this->completed = $completed;
    }

    public function complete() {
        $this->completed = true;
    }

    public function toArray(): array {
        return [
            'title' => $this->title,
            'dueDate' => $this->dueDate,
            'completed' => $this->completed,
        ];
    }

    public static function fromArray(array $data): Task {
        return new Task($data['title'], $data['dueDate'], $data['completed'] ?? false);
    }
}

class TaskManager {
    private $tasks = [];
    private $storage;
    private $taskFactory;

    public function __construct(TaskStorageInterface $storage, TaskFactoryInterface $taskFactory) {
        $this->storage = $storage;
        $this->taskFactory = $taskFactory;
        $this->tasks = $this->storage->load();
    }

    public function addTask($title, $dueDate) {
        $this->tasks[] = $this->taskFactory->create($title, $dueDate);
    }

    public function completeTask($index) {
        if (isset($this->tasks[$index])) {
            $this->tasks[$index]->complete();
        }
    }

    public function saveTasks() {
        $this->storage->save($this->tasks);
    }

    public function listTasks() {
        return $this->tasks;
    }
}
