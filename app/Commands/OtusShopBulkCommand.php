<?php

namespace App\Commands;

use App\Exceptions\CommandException;
use App\Indexes\OtusShopIndex;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class OtusShopBulkCommand extends Command
{
    protected static string $name = "shop:bulk";
    protected static string $description = "Заполнение данными индекса";

    /**
     * @return void
     * @throws CommandException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function handle() {
        $otusShop = new OtusShopIndex();

        $jsonContent = getFileContents('/jsons/books.json');

        if (empty($jsonContent)) {
            throw new CommandException('Файл для заполнения не найден');
        }

        $result = $otusShop->bulk($jsonContent);

        if ($result['errors'] === false) {
            echo "Индекс $otusShop->name был заполнен данными\n";
        } else {
            echo "В заполнении данными $otusShop->name была ошибка!\n";
        }
    }
}