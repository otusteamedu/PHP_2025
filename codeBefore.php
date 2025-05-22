<?php
// Код до
class TaskManager {
    private $tasks = [];

    public function addTask($title, $dueDate) {
        $this->tasks[] = [
            'title' => $title,
            'dueDate' => $dueDate,
            'completed' => false
        ];
    }

    public function completeTask($index) {
        if (isset($this->tasks[$index])) {
            $this->tasks[$index]['completed'] = true;
        }
    }

    public function saveTasksToFile($filename) {
        file_put_contents($filename, json_encode($this->tasks));
    }

    public function loadTasksFromFile($filename) {
        $this->tasks = json_decode(file_get_contents($filename), true);
    }

    public function listTasks() {
        return $this->tasks;
    }
}
