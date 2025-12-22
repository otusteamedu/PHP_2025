<?php

use Blarkinov\RabbitMq\Controllers\BankStatementController;

return [
    [
        'method'  => 'POST',
        'pattern' => '/bank_statement',
        'handler' => fn() => (new BankStatementController)->push(),

    ],
    [
        'method'  => 'GET',
        'pattern' => '/bank_statement',
        'handler' => fn() => (new BankStatementController)->get(),

    ],
];
