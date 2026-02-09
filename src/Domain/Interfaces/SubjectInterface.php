<?php

namespace Restaurant\Domain\Interfaces;

interface SubjectInterface
{
    public function attach(ObserverInterface $observer): void;

    public function detach(ObserverInterface $observer): void;

    public function notify(mixed $data = null): void;
}
