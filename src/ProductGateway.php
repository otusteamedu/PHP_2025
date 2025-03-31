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

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM oc_product");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $products = [];
        foreach ($stmt as $row) {
            $id = $row['product_id'];

            if ($cached = $this->identityMap->get($id)) {
                $products[] = $cached;
            } else {
                $product = new Product($id, $row['model'], $row['price']);
                $this->identityMap->add($id, $product);
                $products[] = $product;
            }
        }
        return $products;
    }
}
