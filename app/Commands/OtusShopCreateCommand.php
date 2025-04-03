<?php

namespace App\Commands;

use App\Exceptions\CommandException;
use App\Indexes\OtusShopIndex;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class OtusShopCreateCommand extends Command
{
    protected static string $name = "shop:create";
    protected static string $description = "Создание индекса";

    /**
     * @throws CommandException
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function handle() {
        $otusShop = new OtusShopIndex();

        if ($otusShop->exists()) {
            throw new CommandException("Индекс $otusShop->name уже существует");
        }

        $result = $otusShop->create();
        if ($result) {
            echo "Успешно создан индекс $otusShop->name\n";
        } else {
            echo "Индекс $otusShop->name не был создан\n";
        }
    }
}