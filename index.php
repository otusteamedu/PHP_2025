<?php

require_once "src/config.php";
require_once "src/HallGateway.php";

$gateway = new HallGateway($pdo);

$halls = $gateway->findAll();

foreach ($halls as $hall) {
    echo sprintf(
        "%d: %s (Cinema ID: %d, Capacity: %d)\n",
        $hall->getId(),
        $hall->getName(),
        $hall->getCinemaId(),
        $hall->getCapacity()
    );
}
