<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Common\Observer;

interface ObserverInterface
{
    public function update(ObservableInterface $observable): void;
}
