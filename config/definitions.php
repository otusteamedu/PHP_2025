<?php

use App\Proxy\QualityControlProxy;
use function DI\create;

return [
    QualityControlProxy::class => create()->constructor(['min_ingredients' => 2]),
];