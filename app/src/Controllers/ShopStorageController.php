<?php declare(strict_types=1);

namespace App\Controllers;

use App\Console\Exceptions\Exception;
use App\Services\ShopStorageService;
use App\Services\ShopStorageServiceInterface;
use Throwable;

/**
 * Class ShopStorageController
 * @package App\Controllers
 */
class ShopStorageController
{
    /**
     * @var ShopStorageServiceInterface
     */
    private ShopStorageServiceInterface $service;

    /**
     *
     */
    public function __construct()
    {
        $this->service = new ShopStorageService();
    }

    /**
     * @param string $filePath
     * @return void
     * @throws Exception
     */
    public function actionInit(string $filePath): void
    {
        try {
            $this->service->create();
            $this->service->seed($this->getDataForSeed($filePath));
            echo 'Storage successfully initialized.' . PHP_EOL;
        } catch (Throwable $e) {
            throw new Exception('Error creating storage: ' . $e->getMessage());
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionCreate(): void
    {
        try {
            $this->service->create();
            echo 'Storage successfully created.' . PHP_EOL;
        } catch (Throwable $e) {
            throw new Exception('Error creating storage: ' . $e->getMessage());
        }
    }

    /**
     * @param string $filePath
     * @return void
     * @throws Exception
     */
    public function actionSeed(string $filePath): void
    {
        try {
            $this->service->seed($this->getDataForSeed($filePath));
            echo 'Data successfully added to storage.' . PHP_EOL;
        } catch (Throwable $e) {
            throw new Exception('Error seeding: ' . $e->getMessage());
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionDelete(): void
    {
        try {
            $this->service->delete();
            echo 'Storage successfully deleted.' . PHP_EOL;
        } catch (Throwable $e) {
            throw new Exception('Error deleting storage: ' . $e->getMessage());
        }
    }

    /**
     * @param string $filePath
     * @return string
     * @throws Exception
     */
    private function getDataForSeed(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new Exception('Data file not exists');
        }

        return file_get_contents($filePath);
    }
}
