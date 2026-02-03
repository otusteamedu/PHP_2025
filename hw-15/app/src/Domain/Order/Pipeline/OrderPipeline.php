<?php

declare(strict_types=1);

namespace App\Domain\Order\Pipeline;

use App\Domain\Cooking\Factory\CookerFactory;
use App\Domain\EventDispatcher;

readonly class OrderPipeline
{
    public function __construct(
        private CookerFactory $cookerFactory,
        private EventDispatcher $dispatcher,
    ) {
    }

    public function build(): OrderHandler
    {
        $accept = new AcceptOrderHandler($this->cookerFactory, $this->dispatcher);
        $cook = new CookOrderHandler($this->cookerFactory, $this->dispatcher);
        $ready = new ReadyOrderHandler($this->cookerFactory, $this->dispatcher);

        $accept->setNext($cook)
            ->setNext($ready);

        return $accept;
    }
}
