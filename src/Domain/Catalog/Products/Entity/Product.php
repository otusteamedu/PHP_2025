<?php
declare(strict_types=1);

namespace Domain\Catalog\Products\Entity;

use Domain\Catalog\Products\ValueObject\Description;
use Domain\Catalog\Products\ValueObject\Name;
use Domain\Catalog\Products\ValueObject\Version;

class Product
{

    private ?int $id = null;

    public function __construct(
        private readonly Name $name,
        private readonly Description $description,
        private readonly Version $version)
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }
}