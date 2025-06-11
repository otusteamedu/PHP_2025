<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\DTO\FoodCooking;
use App\Application\Factory\FoodFactory;
use App\Application\Observer\Publisher;
use App\Application\UseCase\Cooker;
use App\Application\UseCase\FoodDecorator;
use App\Application\UseCase\NotifierCooking;
use App\Infrastructure\Factory\OrderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'command.order:order-food')]
final class CommandOrderFood extends Command
{
    private FoodFactory $productFactory;
    private Publisher $publisher;

    public function __construct(
        FoodFactory $productFactory,
        Publisher $publisher,
    )
    {
        parent::__construct();

        $this->productFactory = $productFactory;
        $this->publisher = $publisher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('TEST')
            ->addOption(
                'productName',
                null,
                InputOption::VALUE_REQUIRED,
                'Product name',
            )
            ->addOption(
                'byRecipe',
                null,
                inputOption::VALUE_OPTIONAL,
                'Added ingredients by recipe',
            )
            ->addOption(
                'myIngredient',
                null,
                inputOption::VALUE_OPTIONAL | inputOption::VALUE_IS_ARRAY,
                'Added ingredients by user',
            )
            ->setHelp(
                'Пример: ./bin/console ' . $this->getName() . ' --productName=burger --byRecipe=y --myIngredient=parsley'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $products = [];
            $food = new FoodDecorator($this->productFactory->createFood($input->getOption('productName')));

            if ($input->getOption('byRecipe') === 'y') {
                $food->addIngredientsByRecipe();
            }

            foreach ($input->getOption('myIngredient') as $ingredient) {
                $food->setClientIngredient($ingredient);
            }

            $this->publisher->subscribe(new NotifierCooking());

            $cooker = new Cooker($food);
            $cooker->setClosureCook(fn() => (new FoodCooking($this->publisher))($food));
            $cooker->cocking();

            $products[] = $cooker->getFood();

            $order = OrderFactory::createOrder($products);
            $order->save();
        } catch (\Exception $e) {
            $io->error('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
