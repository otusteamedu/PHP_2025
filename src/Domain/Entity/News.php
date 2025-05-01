<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\Url;
use App\Domain\ValueObject\Title;
use JsonSerializable;

class News implements JsonSerializable
{
    private ?int $id = null;

    public function __construct(
        private Url  $url,
        private \DateTime $date,
        private Title $title
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setIdFromDb(int $id): void
    {
        $this->id = $id;
    }


    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d H:i:s.u'),
            'url' => $this->url->getValue(),
            'title' => $this->title->getValue()
        ];
    }
}