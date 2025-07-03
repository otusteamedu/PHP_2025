<?php

namespace Elisad5791\Phpapp;

use PDO;
use ReflectionClass;
use DateTimeImmutable;
use LogicException;

class ProductMapper
{    
    public function __construct(private PDO $db, private IdentityMap $map)
    {}
    
    private function mapRowToProduct(array $row): Product
    {
        if ($this->map->has(Product::class, $row['id'])) {
            return $this->map->get(Product::class, $row['id']);
        }

        $product = new Product($row['title'], $row['price']);
        $reflection = new ReflectionClass($product);
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($product, (int)$row['id']);
        
        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($product, new DateTimeImmutable($row['created_at']));
        
        $this->map->set($product);
        return $product;
    }
    
    public function find(int $id): ?Product
    {
        if ($this->map->has(Product::class, $id)) {
            return $this->map->get(Product::class, $id);
        }

        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? $this->mapRowToProduct($row) : null;
    }
    
    public function save(Product $product): void
    {
        if ($product->getId() === null) {
            $stmt = $this->db->prepare('INSERT INTO products (title, price, created_at) VALUES (?, ?, ?)');
            $stmt->execute([$product->getTitle(), $product->getPrice(), $product->getCreatedAt()->format('Y-m-d H:i:s')]);
            
            $reflection = new ReflectionClass($product);
            $idProperty = $reflection->getProperty('id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($product, $this->db->lastInsertId());

            $this->map->set($product);
        } else {
            $stmt = $this->db->prepare('UPDATE products SET title = ?, price = ? WHERE id = ?');
            $stmt->execute([$product->getTitle(), $product->getPrice(), $product->getId()]);

            $this->map->set($product);
        }
    }
    
    public function delete(product $product): void
    {
        if ($product->getId() === null) {
            throw new LogicException('Cannot delete unsaved product');
        }
        
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$product->getId()]);

        $this->map->remove(Product::class, $product->getId());
    }

    public function getAll(): ProductCollection
    {
        $stmt = $this->db->prepare('SELECT * FROM products');
        $stmt->execute([]);
        
        $collection = new ProductCollection();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product = $this->mapRowToProduct($row);
            $collection->add($product);
        }

        return $collection;
    }

    public function getByIds(array $ids): ProductCollection
    {
        if (empty($ids)) {
            return new ProductCollection();
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        
        $collection = new ProductCollection();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product = $this->mapRowToProduct($row);
            $collection->add($product);
        }
        
        return $collection;
    }
}