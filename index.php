<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\User;

$offset = 0;
$limit = 100;
do {
    $users = User::all($limit, $offset);
    foreach ($users as $user) {
        echo $user->getName() . " - " . $user->getEmail() . "\n";
    }
    $offset += $limit;
} while (count($users) === $limit);

$user = User::find(1);
if ($user) {
    echo $user->getName() . " - " . $user->getEmail();
}

