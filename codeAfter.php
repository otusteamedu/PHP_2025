<?php
// Код после
interface TaskStorageInterface {
    public function save(array $tasks);
    public function load(): array;
}

class FileTaskStorage implements TaskStorageInterface {
    private $filename;

    public function __construct($filename) {
        $this->filename = $filename;
    }

    public function save(array $tasks) {
        file_put_contents($this->filename, json_encode($tasks));
    }

    public function load(): array {
        if (!file_exists($this->filename)) return [];
        return json_decode(file_get_contents($this->filename), true);
    }
}

class Task {
    public $title;
    public $dueDate;
    public $completed = false;

    public function __construct($title, $dueDate) {
        $this->title = $title;
        $this->dueDate = $dueDate;
    }

    public function complete() {
        $this->completed = true;
    }
}

class TaskManager {
    private $tasks = [];
    private $storage;

    public function __construct(TaskStorageInterface $storage) {
        $this->storage = $storage;
        $this->tasks = $this->storage->load();
    }

    public function addTask($title, $dueDate) {
        $this->tasks[] = new Task($title, $dueDate);
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
