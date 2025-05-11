<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025;

use Exception;
use PDO;
use RuntimeException;
use Throwable;
use Zibrov\OtusPhp2025\Database\AbstractConfig;
use Zibrov\OtusPhp2025\Database\PdoBuilder;
use Zibrov\OtusPhp2025\Entities\Category;
use Zibrov\OtusPhp2025\Entities\Offers;
use Zibrov\OtusPhp2025\Helpers\CategoryHelper;
use Zibrov\OtusPhp2025\Helpers\OffersHelper;
use Zibrov\OtusPhp2025\Services\CategoryService;
use Zibrov\OtusPhp2025\Services\OffersService;

class Application
{
    public static Application $app;
    private AbstractConfig $config;
    private PDO $pdo;
    private CategoryService $categoryService;
    private OffersService $offersService;

    public function __construct(AbstractConfig $config)
    {
        self::$app = $this;

        $this->config = $config;
        $this->pdo = PdoBuilder::create($config);

        $this->categoryService = new CategoryService($this->pdo);
        $this->offersService = new OffersService($this->pdo);
    }

    public function createTable(): void
    {
        $filename = $_SERVER['DOCUMENT_ROOT'] . $this->config::FILE_NAME_CREATE_TABLE;
        if (file_exists($filename)) {
            try {
                $ddl = file_get_contents($filename);
                $this->pdo->exec($ddl);
            } catch (Exception $e) {
                echo 'ERROR create table: ' . $e->getMessage();
            }
        } else {
            throw new RuntimeException('There is no file[' . $this->config::FILE_NAME_CREATE_TABLE . '] for create table');
        }
    }

    public function deleteTable(): void
    {
        $filename = $_SERVER['DOCUMENT_ROOT'] . $this->config::FILE_NAME_DELETE_TABLE;
        if (file_exists($filename)) {
            try {
                $ddl = file_get_contents($filename);
                $this->pdo->exec($ddl);
            } catch (Exception $e) {
                echo 'ERROR delete table: ' . $e->getMessage();
            }
        } else {
            throw new RuntimeException('There is no file[' . $this->config::FILE_NAME_DELETE_TABLE . '] for delete table');
        }
    }

    public function createCategory(array $arItem): void
    {
        try {
            $category = $this->categoryService->create(Category::create($arItem));
            $this->log('Category "' . $category->getName() . '" successfully added, ID = ' . $category->getId() . PHP_EOL);
        } catch (Exception $e) {
            echo 'ERROR adding a category: ' . $e->getMessage();
        }
    }

    public function createOffers(array $arItem): void
    {
        try {
            $offers = $this->offersService->create(Offers::create($arItem));
            $this->log('Offers "' . $offers->getName() . '" successfully added, ID = ' . $offers->getId());
        } catch (Exception $e) {
            echo 'ERROR adding a offers: ' . $e->getMessage();
        }
    }

    public function getAllCategory(): array
    {
        try {
            return $this->categoryService->getAll();
        } catch (Exception $e) {
            echo 'ERROR get all category: ' . $e->getMessage();
            return [];
        }
    }

    public function getAllOffers(): array
    {
        try {
            return $this->offersService->getAll();
        } catch (Exception $e) {
            echo 'ERROR get all offers: ' . $e->getMessage();
            return [];
        }
    }

    public function updatedCategory(array $arItem): void
    {
        if ($updatedCategory = $this->categoryService->findById($arItem['id'])) {
            $this->log(PHP_EOL . 'Changing the category id=' . $arItem['id'] . ' name="' . $arItem['old_name'] . '"');
            $updatedCategory->setName($arItem['name']);
            $this->categoryService->update($updatedCategory);
            $this->log('Category id=' . $updatedCategory->getId() . ' name="' . $updatedCategory->getName() . '" successfully modified');
            CategoryHelper::printCategory([$updatedCategory]);
        } else {
            $this->log('Category id=' . $updatedCategory->getId() . ' not found.');
        }
    }

    public function updatedOffers(array $arItem): void
    {
        if ($updatedOffers = $this->offersService->findById($arItem['id'])) {
            $this->log(PHP_EOL . 'Changing the offers id=' . $arItem['id'] . ' name="' . $arItem['name'] . '" price=' . $arItem['old_price']);
            $updatedOffers->setPrice((float)$arItem['price']);
            $this->offersService->update($updatedOffers);
            $this->log('Offers id=' . $updatedOffers->getId() . ' name="' . $updatedOffers->getName() . '" price=' . $updatedOffers->getPrice() . ' successfully modified');
            OffersHelper::printOffers([$updatedOffers]);
        } else {
            $this->log('Offers id=' . $updatedOffers->getId() . ' not found.');
        }
    }

    public function deleteCategory(int $id): void
    {
        if ($deleteCategory = $this->categoryService->findById($id)) {
            $this->categoryService->delete($deleteCategory);
            $this->log('Category id=' . $deleteCategory->getId() . ' name="' . $deleteCategory->getName() . '" deleting a category');
        } else {
            $this->log('Category id=' . $id . ' not found.');
        }
    }

    public function deleteOffers(int $id): void
    {
        if ($deleteOffers = $this->offersService->findById($id)) {
            $this->offersService->delete($deleteOffers);
            $this->log('Offers id=' . $deleteOffers->getId() . ' name="' . $deleteOffers->getName() . '" deleting a offers');
        } else {
            $this->log('Offers id=' . $id . ' not found.');
        }
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    public function log(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
