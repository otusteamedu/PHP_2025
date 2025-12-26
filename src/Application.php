<?php
declare(strict_types=1);

namespace App;

use App\Controller\EventController;
use App\Factory\EventRepositoryFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\EventMatchingService;

class Application
{
    /**
     * @return Response
     */
    public function run(): Response
    {
        $config = require __DIR__ . '/../config/storage.php';
        $repository = EventRepositoryFactory::create($config);

        $controller = new EventController(
            $repository,
            new EventMatchingService($repository),
            new Request()
        );

        return $controller->handle();
    }
}
