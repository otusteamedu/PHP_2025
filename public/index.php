<?php

declare(strict_types=1);

use Blarkinov\Hw1500\Application\Composite\Order;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Burger\Burger;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Burger\BurgerCustom;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Hotdog\Hotdog;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Hotdog\HotdogCustom;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Sandwich\Sandwich;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Sandwich\SandwichCustom;
use Blarkinov\Hw1500\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/framework.php';

global $config;

// --------------------------------------------
// test requests

function create()
{
    $_SERVER['REQUEST_URI'] = '/api/orders/create';

    $food = [Burger::class, BurgerCustom::class, Sandwich::class, SandwichCustom::class, Hotdog::class, HotdogCustom::class];

    for ($i = 0; $i < random_int(1, 8); $i++) {      //custom amount of food in an order
        $_POST['order'][] = $food[random_int(0, count($food) - 1)];
    }
}

function change()
{
    $_SERVER['REQUEST_URI'] = '/api/orders/change';

    $_POST['id'] = random_int(1, 60);               // custom range of orders
    $_POST['status'] = random_int(0, 1) ? Order::STATUS_COOKING : Order::STATUS_COMPLETE;
}

create();
// change();
// --------------------------------------------

$kernel = new Kernel($config);
$kernel->run();