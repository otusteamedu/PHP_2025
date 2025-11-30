<?php
declare(strict_types=1);

namespace App\Classes;

class StatusStore
{
    private string $file;

    /**
     * @throws \JsonException
     */
    public function __construct(?string $file = null)
    {
        $this->file = $file ?? $_ENV['STATUS_FILE'];

        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        }
    }

    public function set(string $id, string $status): void
    {
        $arData = $this->read();
        $arData[$id] = $status;
        $this->write($arData);
    }

    public function get(string $id): ?string
    {
        $arData = $this->read();
        return $arData[$id] ?? null;
    }

    /**
     * @throws \JsonException
     */
    private function read(): array
    {
        if (!file_exists($this->file)) {
            return [];
        }

        $json = file_get_contents($this->file);
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR) ?? [];
    }

    /**
     * @throws \JsonException
     */
    private function write(array $data): void
    {
        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->file, $json);
    }
}
