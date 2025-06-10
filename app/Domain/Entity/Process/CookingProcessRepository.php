<?php
declare(strict_types=1);

namespace App\Domain\Entity\Process;

use App\Domain\Patterns\Proxy\CookingProcessProxy;

class CookingProcessRepository extends CookingProcessPresenter
{

    public function createNewProduct(string $title): CookingProcess
    {
        return new CookingProcessProxy($title);
    }
}