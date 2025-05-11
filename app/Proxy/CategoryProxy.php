<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Proxy;

use Zibrov\OtusPhp2025\Application;
use Zibrov\OtusPhp2025\DataMappers\OffersMapper;
use Zibrov\OtusPhp2025\Entities\Category;
use Zibrov\OtusPhp2025\Entities\Offers;

class CategoryProxy
{

    private OffersMapper $mapper;
    private Category|null|false $category = false;

    public function __construct()
    {
        $this->mapper = new OffersMapper(Application::$app->getPDO());
    }

    public function getCategory(Offers $offers): ?Category
    {
        $categoryId = $offers->getCategoryId();

        if ($this->category === false) {
            $this->category = $categoryId ? $this->mapper->findById($categoryId) : null;
        }

        return $this->category;
    }
}
