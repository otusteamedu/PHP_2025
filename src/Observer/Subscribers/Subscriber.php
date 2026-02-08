<?php

namespace Shop\Observer\Subscribers;

interface Subscriber
{
    public function execute(): void;
}