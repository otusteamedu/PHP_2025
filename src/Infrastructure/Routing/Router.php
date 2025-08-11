<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Infrastructure\Routing;

use Kamalo\BurgersShop\Infrastructure\Controller\BuildOrderController;

class Router
{
    private array $routes = [];
    public function __construct()
    {
        $this->addRoute('build-order', function () {
            return new BuildOrderController();
        });
    }

    public function addRoute(string $path, callable $handler): void
    {
        $this->routes[$path] = $handler;
    }

    public function handleRequest(): void
    {

        $path = trim(
            parse_url(
                $this->getRequestPath(),
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

    private function getRequestPath(): string{
        return $_SERVER['REQUEST_URI'];
    }
}