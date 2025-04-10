<?php
declare(strict_types=1);


namespace App\Infrastructure\Repository;

use App\App;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use Predis\Client;

class EventRepository implements EventRepositoryInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'scheme' =>  App::$app->getConfigValue('scheme'),
            'host' => App::$app->getConfigValue('host'),
            'port' => App::$app->getConfigValue('port'),
        ]);
    }


    public function add(Event $event): void
    {
        var_dump($this->client->info());
        die;
        $this->client->executeCommand([]);
        // TODO: Implement add() method.
    }
}