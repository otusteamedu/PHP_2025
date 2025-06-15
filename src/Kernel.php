<?php

namespace App;

use App\Application\Http\Response;
use App\Application\Product\ProductUseCase;
use App\Infrastructure\Handler\BurgerIngredientHandler;
use App\Infrastructure\Handler\HotDogIngredientHandler;
use App\Infrastructure\Handler\ProductStatusUtilizedHandler;
use App\Infrastructure\Handler\SandwichIngredientHandler;
use App\Infrastructure\Notifier\ProductNotifier;
use App\Infrastructure\Product\BurgerCook;
use App\Infrastructure\Product\HotDogCook;
use App\Infrastructure\Product\SandwichCook;
use App\Infrastructure\Repository\ProductRepository;
use App\Infrastructure\Service\ProductStatusChangeService;
use InvalidArgumentException;
use Throwable;

class Kernel
{
    public function run(): void {
        try {
            $type = $_REQUEST['type'] ?? null;

            // --- Observer --- \\
            $notifier = new ProductNotifier([
                'status' => 'cooking'
            ]);

            $notifier
                ->subscribe(new ProductStatusChangeService());
            // --- Observer --- \\

            if ($type === 'burger') {
                $handlers = new BurgerIngredientHandler(); // Chain \\
                $productCook = new BurgerCook(); // Strategy \\
            } else if ($type === 'sandwich') {
                $handlers = new SandwichIngredientHandler(); // Chain \\
                $productCook = new SandwichCook(); // Strategy \\
            } else if ($type === 'hotdog') {
                $handlers = new HotDogIngredientHandler(); // Chain \\
                $productCook = new HotDogCook(); // Strategy \\
            } else {
                throw new InvalidArgumentException('Неизвестный тип продукта');
            }

            $handlers
                ->setNext(new ProductStatusUtilizedHandler()); // Chain \\

            $result = (new ProductUseCase(
                $productCook,
                $notifier,
                $handlers,
                new ProductRepository()
            ))->run();

            $responseData = [
                'product' => [
                    'status' => $result->getStatus(),
                    'type' => $result->getType(),
                ],
            ];
            $response = new Response($responseData, 200, "OK");
        } catch (InvalidArgumentException $e) {
            $response = new Response([], 400, $e->getMessage());
        } catch (Throwable) {
            $response = new Response([], 500, "Something wrong");
        } finally {
            $response->init();
        }
    }
}