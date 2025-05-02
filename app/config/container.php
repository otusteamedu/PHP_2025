<?php

$builder = new \DI\ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAttributes(true);

return $builder->build();