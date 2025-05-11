<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Services;

use PDO;
use Zibrov\OtusPhp2025\DataMappers\CategoryMapper;
use Zibrov\OtusPhp2025\Entities\Category;
use Zibrov\OtusPhp2025\Helpers\CategoryHelper;

class CategoryService implements InterfaceService
{

    private CategoryMapper $mapper;

    public function __construct(PDO $pdo)
    {
        $this->mapper = new CategoryMapper($pdo);
    }

    public function create(Category $category): Category
    {
        return $this->mapper->insert($category);
    }

    public function update(Category $category): void
    {
        $this->mapper->update($category);
    }

    public function delete(Category $category): void
    {
        $this->mapper->delete($category);
    }

    public function findById(int $id): ?Category
    {
        return $this->mapper->findById($id);
    }

    public function getAll(): array
    {
        $categoryCollection = $this->mapper->findAll();
        CategoryHelper::printCategory($categoryCollection);

        return CategoryHelper::getCategory($categoryCollection);
    }
}
