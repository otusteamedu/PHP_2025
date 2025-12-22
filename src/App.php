<?php
declare(strict_types=1);

namespace Dinargab\Homework19;

use Dinargab\Homework19\Application\Report\UseCase\GenerateReportRequest;
use Dinargab\Homework19\Infrastructure\Console\RecieverController;
use Dinargab\Homework19\Infrastructure\Http\RequestController;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class App
{
    private ContainerInterface $container;

    private RequestController $controller;

    private RecieverController $receiver;

    public function __construct(
    )
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');
        $this->container->compile();
    }

    public function run()
    {
        if ($this->container->has(RequestController::class)) {
            $this->controller = $this->container->get(RequestController::class);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request = new GenerateReportRequest($_POST['dateFrom'], $_POST['dateTo'], $_POST['email']);
            ($this->controller)($request);
        } else {
            $this->controller->showForm();
        }
    }

    public function runConsole()
    {
        if ($this->container->has(RecieverController::class)) {
            $this->receiver = $this->container->get(RecieverController::class);
        }
        $this->receiver->run();
    }

}