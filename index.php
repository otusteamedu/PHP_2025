<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

header('Content-Type: text/plain; charset=utf-8');

try {

    $user = (new User())
        ->setUsername('Vladimir')
        ->setEmail('vovka@mail.ru')
        ->setIsActive(true);
    $user->save();

    echo "=== Created user ===\n";
    print_r($user);

    echo "\n=== All users ===\n";
    print_r(User::all());

    echo "\n=== Found user ===\n";
    $foundUser = User::find($user->getId());
    print_r($foundUser);

    echo "\n=== Cache test ===\n";
    $start = microtime(true);
    User::all();
    echo "First all() call: " . (microtime(true) - $start) . "s\n";

    $start = microtime(true);
    User::all();
    echo "Second all() call (cached): " . (microtime(true) - $start) . "s\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}