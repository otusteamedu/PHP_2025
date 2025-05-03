<?php
declare(strict_types=1);


namespace App\Food\Infrastructure\Mapper;

use App\Food\Domain\Aggregate\Order\FoodOrder;
use App\Food\Domain\Aggregate\Order\FoodOrderStatusType;

class OrderMapper
{

    public function __construct()
    {
    }

    public function orderMap(array $row): FoodOrder
    {
        //    private array $foodItems = [];
        $order = new FoodOrder();
        $reflection = new \ReflectionClass($order);
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