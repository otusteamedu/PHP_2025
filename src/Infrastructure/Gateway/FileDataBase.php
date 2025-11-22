<?php


namespace Blarkinov\Hw1500\Infrastructure\Gateway;

use Blarkinov\Hw1500\Application\Composite\Order;
use Blarkinov\Hw1500\Application\Factory\FoodCustomFabric;
use Blarkinov\Hw1500\Application\Factory\FoodFabric;
use Blarkinov\Hw1500\Application\Gateway\DataBaseGateway;
use Blarkinov\Hw1500\Application\Repository\Food\FoodRepository;
use Blarkinov\Hw1500\Application\Strategies\BaseCookingStrategy;
use Blarkinov\Hw1500\Application\Strategies\CustomCookingStrategy;
use Blarkinov\Hw1500\Domain\Fabric\FoodFabricInterface;
use Blarkinov\Hw1500\Domain\Strategies\CookingStrategyInterface;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Burger\Burger;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Food;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Hotdog\Hotdog;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Sandwich\Sandwich;

class FileDataBase implements DataBaseGateway
{
    private static string $path = __DIR__ . '/../../Storage/db.txt';

    public function getCountOrder(): int
    {
        return count(explode("\n", file_get_contents(self::$path)));
    }

    public function getOrder(int $id): ?Order
    {
        $orders = explode("\n", file_get_contents(self::$path));

        if (!isset($orders[$id - 1]))
            return null;

        return $this->mockDataMapper(json_decode($orders[$id - 1], true));
    }

    public function setOrder(Order $order): int
    {
        file_put_contents(self::$path, json_encode($order, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);

        return count(explode("\n", file_get_contents(self::$path))) - 1;
    }

    public function updateOrder(Order $order)
    {
        $orders = explode("\n", file_get_contents(self::$path));
        $orders = array_map(fn($elem) => json_decode($elem), $orders);
        $orders[$order->getId() - 1] = $order;

        file_put_contents(self::$path, "");

        foreach ($orders as $data) {
            if ($data)
                file_put_contents(self::$path, json_encode($data, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
        }
    }

    private function mockDataMapper(array $data): Order
    {

        $foodRepostitory = new FoodRepository();

        foreach ($data['food'] as $food) {
            if (strripos($food, 'custom') === false)
                $foodRepostitory->save($this->createMock($food, new FoodFabric, new BaseCookingStrategy));
            else
                $foodRepostitory->save($this->createMock($food, new FoodCustomFabric, new CustomCookingStrategy));
        }

        return new Order($foodRepostitory, (new FileDataBase()), $data['id'], $data['status']);
    }

    private function createMock(
        string $food,
        FoodFabricInterface $foodFabric,
        CookingStrategyInterface $cook,
    ): Food {

        if (strripos($food, basename(Burger::class)) !== false)
            $foodObject = $foodFabric->makeBurger();
        if (strripos($food, basename(Sandwich::class)) !== false)
            $foodObject = $foodFabric->makeSandwich();
        if (strripos($food, basename(Hotdog::class)) !== false)
            $foodObject = $foodFabric->makeHotdog();

        $foodObject = $cook->cooking($foodObject);

        return $foodObject;
    }
}
