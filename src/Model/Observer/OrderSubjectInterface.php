<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Observer;

interface OrderSubjectInterface
{
    public function attach(OrderObserverInterface $observer): void;

    public function detach(OrderObserverInterface $observer): void;

    public function notify(): void;
}