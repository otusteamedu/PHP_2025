<?php
declare(strict_types=1);

namespace App\Food\Domain\Aggregate;

use App\Food\Domain\Aggregate\VO\FoodTitle;
use App\Food\Domain\Event\FoodCookingStatusUpdated;
use App\Shared\Application\Publisher\PublisherInterface;
use App\Shared\Domain\Service\UuidService;

abstract class Food implements FoodInterface
{
    private string $id;
    private array $ingredients = [];
    private FoodCookingStatusType $cookingStatus;
    private \DateTimeImmutable $statusCreatedAt;
    private \DateTimeImmutable $statusUpdatedAt;
    private FoodType $type;
    private string $orderId;

    public function __construct(
        protected FoodTitle                 $title,
        string                              $orderId,
        FoodType                            $type,
        private readonly PublisherInterface $publisher
    )
    {
        $this->id = UuidService::generate();
        $this->setCookingStatus(FoodCookingStatusType::IN_QUEUE);
        $this->statusCreatedAt = new \DateTimeImmutable();
        $this->orderId = $orderId;
        $this->type = $type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): FoodTitle
    {
        return $this->title;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function addIngredient(FoodIngredient $ingredient): void
    {
        $this->ingredients[] = $ingredient;
    }

    public function removeIngredient(FoodIngredient $ingredient): void
    {
        if (in_array($ingredient, $this->ingredients)) {
            unset($this->ingredients[array_search($ingredient, $this->ingredients)]);
        }
    }

    public function getCookingStatus(): FoodCookingStatusType
    {
        return $this->cookingStatus;
    }

    public function setCookingStatus(FoodCookingStatusType $cookingStatus): void
    {
        $this->cookingStatus = $cookingStatus;
        $this->statusUpdatedAt = new \DateTimeImmutable();
        $this->publisher->handle(new FoodCookingStatusUpdated($this->id, $cookingStatus));
    }

    public function getStatusCreatedAt(): \DateTimeImmutable
    {
        return $this->statusCreatedAt;
    }

    public function getStatusUpdatedAt(): \DateTimeImmutable
    {
        return $this->statusUpdatedAt;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getType(): FoodType
    {
        return $this->type;
    }

}