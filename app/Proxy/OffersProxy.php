<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Proxy;

use Zibrov\OtusPhp2025\Application;
use Zibrov\OtusPhp2025\DataMappers\OffersMapper;
use Zibrov\OtusPhp2025\Entities\Category;

class OffersProxy
{

    private OffersMapper $mapper;
    private ?array $offers = null;

    public function __construct()
    {
        $this->mapper = new OffersMapper(Application::$app->getPDO());
    }

    public function getOffers(Category $category): array
    {
        if ($this->offers === null) {
            $this->offers = $this->mapper->findByCategory($category);
        }

        return $this->offers;
    }
}
