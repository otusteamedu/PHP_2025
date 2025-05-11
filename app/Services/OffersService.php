<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Services;

use PDO;
use Zibrov\OtusPhp2025\DataMappers\OffersMapper;
use Zibrov\OtusPhp2025\Entities\Offers;
use Zibrov\OtusPhp2025\Helpers\OffersHelper;

class OffersService implements InterfaceService
{

    private OffersMapper $mapper;

    public function __construct(PDO $pdo)
    {
        $this->mapper = new OffersMapper($pdo);
    }

    public function create(Offers $offers): Offers
    {
        return $this->mapper->insert($offers);
    }


    public function update(Offers $offers): void
    {
        $this->mapper->update($offers);
    }


    public function delete(Offers $offers): void
    {
        $this->mapper->delete($offers);
    }

    public function findById(int $id): ?Offers
    {
        return $this->mapper->findById($id);
    }

    public function getAll(): array
    {
        $offersCollection = $this->mapper->findAll();
        OffersHelper::printOffers($offersCollection);

        return OffersHelper::getOffers($offersCollection);
    }
}
