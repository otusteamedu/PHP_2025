<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\DataMappers;

use DomainException;
use PDO;
use PDOStatement;
use Zibrov\OtusPhp2025\Collections\OffersCollection;
use Zibrov\OtusPhp2025\Components\IdentityMap;
use Zibrov\OtusPhp2025\Entities\Category;
use Zibrov\OtusPhp2025\Entities\Offers;

class OffersMapper extends AbstractMapper
{

    protected PDOStatement $findByTeamStatement;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->findByTeamStatement = $this->prepareFindByTeamStatement();
    }

    public function insert(Offers $offers): Offers
    {
        $this->insertStatement->execute([
            ':category_id' => $offers->getCategoryId(),
            ':name' => $offers->getName(),
            ':color' => $offers->getColor(),
            ':price' => $offers->getPrice(),
        ]);

        $offers->setId((int)$this->pdo->lastInsertId());

        IdentityMap::add($offers);

        return $offers;
    }

    public function update(Offers $offers): void
    {
        $this->updateStatement->execute([
            ':category_id' => $offers->getCategoryId(),
            ':name' => $offers->getName(),
            ':color' => $offers->getColor(),
            ':price' => (float)$offers->getPrice(),
            ':id' => $offers->getId(),
        ]);

        IdentityMap::add($offers);
    }

    public function delete(Offers $offers): void
    {
        $this->deleteStatement->execute([
            ':id' => $offers->getId(),
        ]);

        IdentityMap::delete($offers);
    }

    public function findById(int $id): ?Offers
    {
        $offers = IdentityMap::get(Offers::class, $id);
        if ($offers) {
            return $offers;
        }

        $this->findByIdStatement->execute([
            ':id' => $id,
        ]);

        $result = $this->findByIdStatement->fetch();
        if ($result === false) {
            return null;
        }

        $offers = Offers::create($result);
        IdentityMap::add($offers);

        return $offers;
    }

    public function findAll(int $limit = 100, int $offset = 0): OffersCollection
    {
        $this->findAllStatement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $this->findAllStatement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $this->findAllStatement->execute();

        $result = $this->findAllStatement->fetchAll();
        if ($result === false) {
            throw new DomainException('Result fetch error');
        }

        $offersCollection = new OffersCollection();
        foreach ($result as $row) {
            $offers = Offers::create($row);
            IdentityMap::add($offers);
            $offersCollection->add($offers);
        }

        return $offersCollection;
    }

    public function findByCategory(Category $category): array
    {
        $this->findByTeamStatement->execute([
            ':category_id' => $category->getId(),
        ]);

        $result = $this->findByTeamStatement->fetchAll();
        if ($result === false) {
            return [];
        }

        $arOffers = [];
        foreach ($result as $row) {
            $offers = Offers::create($row);
            IdentityMap::add($offers);
            $arOffers[] = $offers;
        }

        return $arOffers;
    }

    protected function prepareFindByTeamStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getFindByCategoryStatementQuery());
    }

    protected function getInsertStatementQuery(): string
    {
        return 'INSERT INTO offers (category_id, name, color, price) 
                VALUES (:category_id, :name, :color, :price)';
    }

    protected function getUpdateStatementQuery(): string
    {
        return 'UPDATE offers 
                SET category_id = :category_id,
                    name = :name,
                    color = :color,
                    price = :price
                 WHERE id = :id';
    }

    protected function getDeleteStatementQuery(): string
    {
        return 'DELETE FROM offers WHERE id = :id';
    }

    protected function getFindByIdStatementQuery(): string
    {
        return 'SELECT * FROM offers WHERE id = :id';
    }

    protected function getFindAllStatementQuery(): string
    {
        return 'SELECT * FROM offers ORDER BY id LIMIT :limit OFFSET :offset';
    }

    protected function getFindByCategoryStatementQuery(): string
    {
        return 'SELECT * FROM offers WHERE category_id = :category_id ORDER BY id';
    }
}
