<?

declare(strict_types=1);

namespace Kamalo\BurgersShop;

use Kamalo\BurgersShop\Infrastructure\Routing\Router;

require '../vendor/autoload.php';

$router = new Router();

$router->handleRequest();