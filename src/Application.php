<?php
declare(strict_types=1);

namespace App;

use App\Command\SearchCommand;
use App\Command\ShopIndexBuilderCommand;
use App\ElasticSearch\ClientFactory;
use App\Repository\BookRepository;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        parent::__construct();
        $this->addCommands($this->createCommands());
    }

    /**
     * @return array
     * @throws AuthenticationException
     */
    public function createCommands(): array
    {
        $client = ClientFactory::create();

        return [
            new SearchCommand(new BookRepository($client)),
            new ShopIndexBuilderCommand($client),
        ];
    }
}
