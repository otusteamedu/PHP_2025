<?php

namespace App\Models;

class FileModel
{
    public int $id;
    public string $fileName;

    /**
     * @param string $fileName
     * @return static
     */
    public static function create(string $fileName): self
    {
        $model = new self();
        $model->fileName = $fileName;
        return $model;
    }

    /**
     * @param int $id
     * @return FileModel
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fileName' => $this->fileName
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}