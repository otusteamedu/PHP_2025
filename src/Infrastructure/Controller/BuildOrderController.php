<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Infrastructure\Controller;

use Exception;
use Kamalo\BurgersShop\Application\Builder\BurgerBuilder;
use Kamalo\BurgersShop\Application\Builder\HotdogBuilder;
use Kamalo\BurgersShop\Application\Builder\SandwichBuilder;
use Kamalo\BurgersShop\Application\Client\BuildOrderClient;
use Kamalo\BurgersShop\Application\Iterator\ItemsIterator;
use Kamalo\BurgersShop\Application\Manager\CommonOrderManager;
use Kamalo\BurgersShop\Application\Publisher\Publisher;
use Kamalo\BurgersShop\Application\UseCase\BuildOrderRequest;
use Kamalo\BurgersShop\Application\UseCase\BuildOrderUseCase;
use Kamalo\BurgersShop\Infrastructure\Enviropment\Config;
use Kamalo\BurgersShop\Infrastructure\Gateway\CommonPaymentGateway;
use Kamalo\BurgersShop\Infrastructure\Handler\BurgerCookHandler;
use Kamalo\BurgersShop\Infrastructure\Handler\HotdogCookHandler;
use Kamalo\BurgersShop\Infrastructure\Handler\SandwichCookHandler;
use Kamalo\BurgersShop\Infrastructure\View\ConsoleView;

class BuildOrderController
{
    private readonly BuildOrderUseCase $useCase;


    public function __invoke(): void
    {
        try {
            $config = new Config();
            $view = new ConsoleView();
            $publisher = new Publisher();

            $publisher->subscribe($view);

            $firstHandler = new BurgerCookHandler(
                new BurgerBuilder(
                    $config,
                    new ItemsIterator(),
                    new ItemsIterator()
                )
            );

            $firstHandler
                ->setNext(
                    new HotdogCookHandler(
                        new HotdogBuilder(
                            $config,
                            new ItemsIterator(),
                            new ItemsIterator()
                        )
                    )
                )
                ->setNext(
                    new SandwichCookHandler(
                        new SandwichBuilder(
                            $config,
                            new ItemsIterator(),
                            new ItemsIterator()
                        )
                    )
                );


            $useCase = new BuildOrderUseCase(
                new CommonOrderManager(
                    new BuildOrderClient(
                        $firstHandler
                    ),
                    new CommonPaymentGateway(),
                    $publisher
                )
            );

            $useCase($this->getRequest());

        } catch (\Throwable $e) {
            $response = [
                'message' => $e->getMessage(),
            ];

            print_r(json_encode(
                $response,
                JSON_UNESCAPED_UNICODE
            ));
        }
    }

    private function getRequest(): BuildOrderRequest
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $burgers = [];

        if (isset($data['burgers'])) {
            foreach ($data['burgers'] as $burger) {

                $extras = [];

                foreach ($burger['extras'] as $extra) {
                    $extras[$extra['name']] = $extra['count'];
                }

                $burgers[$burger['name']] = [
                    'count' => $burger['count'],
                    'extras' => $extras
                ];
            }
        }

        $sandwiches = [];

        if (isset($data['sandwiches'])) {
            foreach ($data['sandwiches'] as $sandwich) {
                $extras = [];

                foreach ($sandwich['extras'] as $extra) {
                    $extras[$extra['name']] = $extra['count'];
                }

                $sandwiches[$sandwich['name']] = [
                    'count' => $sandwich['count'],
                    'extras' => $extras
                ];
            }
        }

        $hotdogs = [];

        if (isset($data['hotdogs'])) {
            foreach ($data['hotdogs'] as $hotdog) {
                $extras = [];

                foreach ($hotdog['extras'] as $extra) {
                    $extras[$extra['name']] = $extra['count'];
                }

                $hotdogs[$hotdog['name']] = [
                    'count' => $hotdog['count'],
                    'extras' => $extras
                ];
            }
        }

        if (
            count($burgers) === 0
            && count($sandwiches) === 0
            && count($hotdogs) === 0
        ) {
            throw new Exception('Не переданы позиции заказа');
        }

        return new BuildOrderRequest(
            $burgers,
            $sandwiches,
            $hotdogs
        );
    }
}