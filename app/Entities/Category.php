<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Entities;

use Zibrov\OtusPhp2025\Proxy\OffersProxy;

class Category extends AbstractEntity
{

    private OffersProxy $offersProxy;

    public function __construct(?int $id, private string $name, private string $code)
    {
        parent::__construct($id);
        $this->offersProxy = new OffersProxy();
    }

    public static function create(array $data): static
    {
        return new Category(
            $data['id'] ?? null,
            $data['name'],
            $data['code'],
        );
    }

    public function getAttributes(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'code' => $this->getCode(),
        ];
    }

    public function getOffers(): array
    {
        return $this->offersProxy->getOffers($this);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
