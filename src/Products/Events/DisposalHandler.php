<?php declare(strict_types=1);

namespace Fastfood\Products\Events;

class DisposalHandler implements PreparationEventListenerInterface
{

    public function onPrePreparation(PrePreparationEvent $event): void
    {
        // Логика проверки перед началом приготовления
        echo("\nПРИГОТОВЛЕНИЕ: Начинаем приготовление " . $event->getProduct()->getDescription());
    }

    public function onPostPreparation(PostPreparationEvent $event): void
    {
        // Проверяем результаты контроля качества
        if (!$event->passedQualityControl()) {
            // Утилизируем продукт
            echo(
                "\nУТИЛИЗАЦИЯ: Продукт '" . $event->getProduct()->getDescription() .
                "' неудачный контроль качества. Причина: " . $event->getQualityMessage() .
                ". Продукт утилизирован."
            );
        } else {
            // Проверяем, был ли продукт переприготовлен
            $qualityInfo = [];
            $reflection = new \ReflectionClass($event->getProduct());
            if ($reflection->hasProperty('qualityInfo')) {
                $property = $reflection->getProperty('qualityInfo');
                $property->setAccessible(true);
                $qualityInfo = $property->getValue($event->getProduct()) ?? [];
            }

            if (isset($qualityInfo['recooked']) && $qualityInfo['recooked']) {
                echo("\nПРИГОТОВЛЕНИЕ: Продукт '" . $event->getProduct()->getDescription() . "' успешно прошел контроль качества после повторного приготовления.");
            } else {
                echo("\nПРИГОТОВЛЕНИЕ: Продукт '" . $event->getProduct()->getDescription() . "' успешно прошел контроль качества.");
            }
        }
    }
}