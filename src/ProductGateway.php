<?php

require_once "IdentityMap.php";
require_once "Product.php";

class ProductGateway {
    private PDO $pdo;
    private IdentityMap $identityMap;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->identityMap = new IdentityMap();
    }

    public function findAll(int $batchSize = 1000): \Generator {
        $offset = 0;

        do {
            $stmt = $this->pdo->prepare("SELECT * FROM oc_product LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $batchSize, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $rowsFetched = 0;

            while ($row = $stmt->fetch()) {
                $id = $row['product_id'];

                if ($cached = $this->identityMap->get($id)) {
                    yield $cached;
                } else {
                    $product = new Product($id, $row['model'], $row['price']);
                    $this->identityMap->add($id, $product);
                    yield $product;
                }

                $rowsFetched++;
            }

            $offset += $batchSize;
        } while ($rowsFetched > 0);
    }
}
