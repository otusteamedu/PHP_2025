<?php

declare(strict_types=1);

namespace App\UserInterface;

use App\Application\Order\CreateOrder;
use App\Application\Order\CreateOrderCommand as ApplicationCreateOrderCommand;
use App\Domain\Cooking\Exception\ProductIsBadException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Throwable;

#[AsCommand(name: 'order:make', description: 'Создание заказа с разными ингредиентами')]
readonly class CreateOrderCommand
{
    public function __construct(
        private CreateOrder $createOrder
    ) {
    }

    public function __invoke(InputInterface $input, OutputInterface $output, SymfonyStyle $io): int
    {
        $productType = $this->askForProductType($io);
        $quantity = $this->askForQuantity($io);
        $ingredients = $this->askForIngredients($io);

        $this->displayOrderSummary($io, $productType, $quantity, $ingredients);

        $createOrderCommand = new ApplicationCreateOrderCommand(
            name: $productType,
            quantity: $quantity,
            ingredients: $ingredients
        );

        return $this->createAndDisplayOrder($createOrderCommand, $io);
    }

    private function askForProductType(SymfonyStyle $io): string
    {
        $question = new ChoiceQuestion(
            'Выберите продукт: ',
            ['Бургер', 'Сэндвич', 'Хот-дог'],
            0
        );
        $question->setErrorMessage('Недопустимый выбор');
        return $io->askQuestion($question);
    }

    private function askForQuantity(SymfonyStyle $io): int
    {
        $quantityQuestion = new ChoiceQuestion(
            'Введите количество: ',
            [1, 2, 3, 4, 5],
            0
        );
        return (int) $io->askQuestion($quantityQuestion);
    }

    private function askForIngredients(SymfonyStyle $io): array
    {
        $ingredients = [];
        $addIngredient = true;

        while ($addIngredient) {
            $ingredientQuestion = new ChoiceQuestion(
                'Вы хотите добавить ингредиент? (выберите или нажмите Enter для завершения): ',
                ['Лук', 'Сыр', 'Халапеньо', 'Томаты', 'Ничего'],
                4
            );
            $ingredient = $io->askQuestion($ingredientQuestion);

            if ($ingredient === 'Ничего') {
                $addIngredient = false;
            } else {
                $ingredients[] = $ingredient;
            }
        }

        return $ingredients;
    }

    private function displayOrderSummary(SymfonyStyle $io, string $productType, int $quantity, array $ingredients): void
    {
        $io->writeln("\n---------------------------");
        $io->writeln("Вы выбрали: $productType - $quantity шт");
        $io->writeln("Добавленные ингредиенты: " . (empty($ingredients) ? 'Нет ингредиентов' : implode(', ', $ingredients)));
        $io->writeln("---------------------------\n");
    }

    private function createAndDisplayOrder(ApplicationCreateOrderCommand $createOrderCommand, SymfonyStyle $io): int
    {
        try {
            $order = $this->createOrder->execute($createOrderCommand);

            $io->writeln("\n---------------------------");
            $io->writeln("Ваш заказ готов:");
            foreach ($order->getItems() as $item) {
                $io->writeln(
                    $item->getProduct()->getName() . ' x ' . $item->getQuantity() .
                    ' = ' . $item->getPrice()
                );
            }

            $io->success('Заказ успешно создан!');
        } catch (ProductIsBadException $exception) {
            $io->error($exception->getMessage());
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());
        }

        return 0;
    }
}
