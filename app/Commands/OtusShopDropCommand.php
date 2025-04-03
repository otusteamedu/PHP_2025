<?php

namespace App\Commands;

use App\Exceptions\CommandException;
use App\Indexes\OtusShopIndex;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class OtusShopDropCommand extends Command
{
    protected static string $name = "shop:drop";
    protected static string $description = "Удаление индекса";

    /**
     * @return void
     * @throws CommandException
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function handle() {
        $otusShop = new OtusShopIndex();

        if ($otusShop->exists() === false) {
            throw new CommandException("Индекса $otusShop->name не существует");
        }

        $result = $otusShop->drop();

        if ($result) {
            echo "Успешно удален индекс $otusShop->name\n";
        } else {
            echo "Индекс $otusShop->name не был удален!\n";
        }
    }
}