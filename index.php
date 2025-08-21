<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\User;

$users = User::all();

foreach ($users as $user) {
    echo $user->getName() . " - " . $user->getEmail() . "\n";
}

$user = User::find(1);
if ($user) {
    echo $user->getName() . " - " . $user->getEmail();
}
