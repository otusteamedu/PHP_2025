<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Console\ConsoleApplication;
use App\Console\IO\ConsoleOutput;
use App\Elasticsearch\ElasticsearchClient;
use App\Elasticsearch\BookIndexService;

class ESCommand implements CommandInterface
{
    public BookIndexService $service;

    public function __construct()
    {
        $params = ConsoleApplication::getParams();

        $client = new ElasticsearchClient(
            host: $params['es_host'],
            username: $params['es_user'],
            password: $params['es_password'],
            verifySSL: $params['es_verify_ssl'],
        );

        $this->service = new BookIndexService($client->create());
    }

    public function getName(): string
    {
        return '';
    }

    public function getDescription(): string
    {
        return '';
    }

    public function execute(array $input, ConsoleOutput $output): string
    {
        return '';
    }
}
