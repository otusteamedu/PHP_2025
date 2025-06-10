<?php
declare(strict_types=1);

namespace Infrastructure\Factory;

use DateTime;
use Domain\Catalog\Services\Entity\Service;
use Domain\Licenses\Entity\License;
use Domain\Licenses\ValueObject\ConnectionService;
use Domain\Licenses\ValueObject\ConnectionUser;
use Domain\Licenses\ValueObject\CreateDate;
use Domain\Licenses\ValueObject\Period;
use Domain\Licenses\ValueObject\SerialNumber;
use Domain\Users\Entity\User;

class CommonLicenseFactory
{
    public function create(User $user, Service $service, DateTime $createDate, int $period, string $serialNumber): License
    {
        return new License(
            new ConnectionUser($user),
            new ConnectionService($service),
            new CreateDate($createDate),
            new Period($period),
            new SerialNumber($serialNumber),
        );
    }
}