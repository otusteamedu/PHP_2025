<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Notification;

use Ak\Hw\Domain\Common\Observer\ObserverInterface;
use Ak\Hw\Domain\Common\Observer\ObservableInterface;

interface NotificationObserverInterface extends ObserverInterface
{
    public function update(ObservableInterface $observable): void;
}
