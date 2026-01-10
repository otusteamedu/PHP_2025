<?php declare(strict_types=1);

namespace App\Chain;

use App\Core\FoodProductInterface;

abstract class AbstractHandler implements HandlerInterface
{
    protected ?HandlerInterface $next = null;

    public function setNext(HandlerInterface $handler): HandlerInterface
    {
        $this->next = $handler;
        return $handler;
    }

    public function handle(FoodProductInterface $product): void
    {
        if ($this->next) {
            $this->next->handle($product);
        }
    }
}
