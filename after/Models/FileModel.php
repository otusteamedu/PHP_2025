<?php

namespace Models;

class FileModel
{
    public int $id;
    private string $fileName;

    /**
     * @param string $fileName
     * @param array|null $sharedUsers
     * @return static
     */
    public static function create(string $fileName, ?array $sharedUsers): self
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
}