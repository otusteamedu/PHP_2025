<?php declare(strict_types=1);

namespace Fastfood\Orders\Observers;

interface OrderSubjectInterface
{
    public function attach(OrderObserverInterface $observer): void;
    public function detach(OrderObserverInterface $observer): void;
    public function notify(string $event): void;
}