<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Statement\Entity;

use Dinargab\Homework20\Domain\ValueObject\DateValueObject;
use Dinargab\Homework20\Domain\ValueObject\Url;

class BankStatement
{
    private int $id;

    public function __construct(
        private DateValueObject $dateFrom,
        private DateValueObject $dateTo,
        private Url $url
    )
    {

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDateFrom(): DateValueObject
    {
        return $this->dateFrom;
    }

    public function getDateTo(): DateValueObject
    {
        return $this->dateTo;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = new Url($url);
    }
}