<?php
declare(strict_types=1);

namespace App\Food\Infrastructure\Mapper;

use App\Food\Domain\Aggregate\Order\FoodOrderStatusType;
use App\Food\Infrastructure\Proxy\FoodOrderProxy;
use App\Food\Infrastructure\Repository\FoodRepository;

class OrderMapper
{
    public function __construct(
        private FoodRepository $foodRepository,
    )
    {
    }

    public function orderMap(array $row): FoodOrderProxy
    {
        $order = new FoodOrderProxy($this->foodRepository);

        $reflection = new \ReflectionClass(get_parent_class($order));
        $property = $reflection->getProperty('id');
        $property->setValue($order, $row['id']);

        $property = $reflection->getProperty('status');
        $property->setValue($order, FoodOrderStatusType::from($row['status']));

        $property = $reflection->getProperty('statusCreatedAt');
        $property->setValue($order, new \DateTimeImmutable($row['status_created_at']));

        $property = $reflection->getProperty('statusUpdatedAt');
        $property->setValue($order, new \DateTimeImmutable($row['status_updated_at']));

        return $order;
    }

}