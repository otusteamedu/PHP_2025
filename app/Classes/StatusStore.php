<?php

namespace App\Classes;

class StatusStore {  //хранения статуса в JSON-файле
    private string $file;

    public function __construct(?string $file = null) {
        $this->file = $file ?? $_ENV['STATUS_FILE'];

        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    //устанавливаем и читаем статус заявки
    public function set(string $id, string $status): void {
        $data = $this->read();
        $data[$id] = $status;
        $this->write($data);
    }

    public function get(string $id): ?string {
        $data = $this->read();
        return $data[$id] ?? null;
    }

    //читаем и пишем JSON-файл который мы используем в качестве асинхронного хранилища
    private function read(): array {
        if (!file_exists($this->file)) {
            return [];
        }

        $json = file_get_contents($this->file);
        return json_decode($json, true) ?? [];
    }

    private function write(array $data): void {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
