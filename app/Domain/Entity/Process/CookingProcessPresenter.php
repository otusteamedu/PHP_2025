<?php
declare(strict_types=1);

namespace App\Domain\Entity\Process;

class CookingProcessPresenter
{

    public function messageBeforeCreateProduct(CookingProcess $cookingProcess): void
    {
        echo 'Подготовка к созданию продукта "' . $cookingProcess->getTitle() . '"<br>';
    }

    public function messageAfterCreateProduct(CookingProcess $cookingProcess): void
    {
        $status = $cookingProcess->getStatus();

        if ($status === CookingProcess::PREPARED) {
            echo 'Продукт ещё готовится <br>';
        } else if ($status === CookingProcess::READY) {
            echo 'Продукт готов <br>';
        } else if ($status === CookingProcess::BAD) {
            echo 'Продукт не соответствует нормам, передается на утилизацию <br>';
        }
    }
}