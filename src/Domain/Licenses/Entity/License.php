<?php
declare(strict_types=1);

namespace Domain\Licenses\Entity;

use Domain\Licenses\ValueObject\ConnectionService;
use Domain\Licenses\ValueObject\ConnectionUser;
use Domain\Licenses\ValueObject\CreateDate;
use Domain\Licenses\ValueObject\Period;
use Domain\Licenses\ValueObject\SerialNumber;

class License
{

    private ?int $id = null;

    public function __construct(
        private readonly ConnectionUser $user,
        private readonly ConnectionService $service,
        private readonly CreateDate $createDate,
        private readonly Period $period,
        private readonly SerialNumber $serialNumber)
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ConnectionUser
    {
        return $this->user;
    }

    public function getService(): ConnectionService
    {
        return $this->service;
    }

    public function getCreateDate(): CreateDate
    {
        return $this->createDate;
    }

    public function getPeriod(): Period
    {
        return $this->period;
    }

    public function getSerialNumber(): SerialNumber
    {
        return $this->serialNumber;
    }
}