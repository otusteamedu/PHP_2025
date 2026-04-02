<?

declare(strict_types=1);

namespace Kamalo\EventsService\Infrastucture\Routing;

use Kamalo\EventsService\Infrastucture\Controller\AddEventController;
use Kamalo\EventsService\Application\UseCase\AddEvent\AddEventUseCase;
use Kamalo\EventsService\Infrastucture\Controller\ClearEventsController;
use Kamalo\EventsService\Application\UseCase\ClearEvents\ClearEventsUseCase;
use Kamalo\EventsService\Infrastucture\Controller\GetSuitableEventController;
use Kamalo\EventsService\Application\UseCase\GetSuitableEvent\GetSuitableEventUseCase;
use Kamalo\EventsService\Infrastucture\Repository\MemcachedEventRepository;
use Kamalo\EventsService\Infrastucture\Repository\RedisEventRepository;
use Kamalo\EventsService\Application\Factory\EventFactory;
use Kamalo\EventsService\Infrastucture\Enviropment\Config;
use Kamalo\EventsService\Domain\Repository\EventRepositoryInterface;

class Router
{
    private array $routes = [];
    private ?EventRepositoryInterface $repository;

    public function __construct()
    {
        $host =(new Config())->get('database', 'host');
        $this->repository = null;

        if($host === 'redis'){
            $this->repository = new RedisEventRepository();
        } else {
            $this->repository = new MemcachedEventRepository();
        } 
       

        $this->addRoute('events/add', function () {
            return new AddEventController(
                new AddEventUseCase(
                    $this->repository,
                    new EventFactory()
                )
            );
        });

        $this->addRoute('events/clear', function () {
            return new ClearEventsController(
                new ClearEventsUseCase(
                    $this->repository
                )
            );
        });

        $this->addRoute('events/get', function () {
            return new GetSuitableEventController(
                new GetSuitableEventUseCase(
                    $this->repository
                )
            );
        });
    }

    public function addRoute(string $path, callable $handler): void
    {
        $this->routes[$path] = $handler;
    }

    public function handleRequest(string $path): void
    {

        $path = trim(
            parse_url(
                $path,
                PHP_URL_PATH
            ),
            '/'
        );

       
        if (isset($this->routes[$path])) {
           
            $controller = $this->routes[$path]();
            $controller();

        } else {
            echo json_encode(
                ['message' => 'Метод не найден'],
                JSON_UNESCAPED_UNICODE
            );
        }
    }
}