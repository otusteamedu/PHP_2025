<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\DataMappers;

use DomainException;
use PDO;
use Zibrov\OtusPhp2025\Collections\CategoryCollection;
use Zibrov\OtusPhp2025\Components\IdentityMap;
use Zibrov\OtusPhp2025\Entities\Category;

class CategoryMapper extends AbstractMapper
{

    public function insert(Category $category): Category
    {
        $this->insertStatement->execute([
            ':name' => $category->getName(),
            ':code' => $category->getCode(),
        ]);

        $category->setId((int)$this->pdo->lastInsertId());

        IdentityMap::add($category);

        return $category;
    }

    public function update(Category $category): void
    {
        $this->updateStatement->execute([
            ':id' => $category->getId(),
            ':name' => $category->getName(),
            ':code' => $category->getCode(),
        ]);

        IdentityMap::add($category);
    }

    public function delete(Category $category): void
    {
        $this->deleteStatement->execute([
            ':id' => $category->getId(),
        ]);

        IdentityMap::delete($category);
    }

    public function findById(int $id): ?Category
    {
        $category = IdentityMap::get(Category::class, $id);
        if ($category) {
            return $category;
        }

        $this->findByIdStatement->execute([
            ':id' => $id,
        ]);

        $result = $this->findByIdStatement->fetch();
        if ($result === false) {
            return null;
        }

        $category = Category::create($result);
        IdentityMap::add($category);

        return $category;
    }

    public function findAll(int $limit = 100, int $offset = 0): CategoryCollection
    {
        $this->findAllStatement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $this->findAllStatement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $this->findAllStatement->execute();

        $result = $this->findAllStatement->fetchAll();
        if ($result === false) {
            throw new DomainException('Result fetch error');
        }

        $categoriesCollection = new CategoryCollection();
        foreach ($result as $row) {
            $category = Category::create($row);
            IdentityMap::add($category);
            $categoriesCollection->add($category);
        }

        return $categoriesCollection;
    }

    protected function getInsertStatementQuery(): string
    {
        return 'INSERT INTO category (name, code) VALUES (:name, :code)';
    }

    protected function getUpdateStatementQuery(): string
    {
        return 'UPDATE category SET name = :name, code = :code WHERE id = :id';
    }

    protected function getDeleteStatementQuery(): string
    {
        return 'DELETE FROM category WHERE id = :id';
    }

    protected function getFindByIdStatementQuery(): string
    {
        return 'SELECT * FROM category WHERE id = :id';
    }

    protected function getFindAllStatementQuery(): string
    {
        return 'SELECT * FROM category ORDER BY id LIMIT :limit OFFSET :offset';
    }
}
