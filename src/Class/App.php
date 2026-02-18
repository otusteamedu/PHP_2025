<?

declare(strict_types=1);

namespace Kamalo\Balancer\Class;

class App
{
    private RequestHandler $handler;
    public function __construct()
    {
        $this->handler = new RequestHandler(
            new Validator()
        );
    }

    public function start()
    {
        $this->handler->handle();
    }
}