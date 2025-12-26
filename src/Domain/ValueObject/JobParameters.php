<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\ValueObject;

final class JobParameters implements \JsonSerializable
{

    private ?int $jobResultId = null;
    public function __construct(
        private DateValueObject $dateFrom,
        private DateValueObject $dateTo,
    )
    {

    }

    public function getDateFrom(): DateValueObject
    {
        return $this->dateFrom;
    }

    public function getDateTo(): DateValueObject
    {
        return $this->dateTo;
    }

    public function __toString(): string
    {
        return $this->dateFrom . ' - ' . $this->dateTo;
    }

    public function toArray(): array
    {
        return [
            'dateFrom' => (string) $this->dateFrom,
            'dateTo' => (string) $this->dateTo,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new DateValueObject($data['dateFrom']),
            new DateValueObject($data['dateTo'])
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

}