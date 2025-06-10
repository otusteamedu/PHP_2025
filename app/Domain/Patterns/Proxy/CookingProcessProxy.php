<?php
declare(strict_types=1);

namespace App\Domain\Patterns\Proxy;

use App\Domain\Entity\Process\CookingProcess;

class CookingProcessProxy extends CookingProcess
{

    public ?string $status = null;

    public function __construct(string $title)
    {
        parent::__construct($title);
    }

    public function getStatus(): string
    {
        if ($this->status === null) {

            // Имитация получения данных из БД о состояния готовности продукта
            sleep(3);
            $arStatus = [
                CookingProcess::READY,
                CookingProcess::PREPARED,
                CookingProcess::BAD,
            ];

            $this->status = $arStatus[array_rand($arStatus)];
        }

        return $this->status;
    }
}
