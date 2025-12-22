<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Blarkinov\RabbitMq\App\App;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

/**
 * отправить данные из "формы" в очередь:
 * 
 * 
 * $_SERVER['REQUEST_URI']='/bank_statement';
 * $_SERVER['REQUEST_METHOD']='POST';
 * $_POST['dateFrom']='2024-07-14';
 * $_POST['dateTo']='2024-11-17';
 * $_POST['transactionType']=BankStatement::TRANSACTION_TYPE_DEPOSIT;
 */

/**
 * получить данные из очереди:
 * 
 * 
 * $_SERVER['REQUEST_URI']='/bank_statement';
 * $_SERVER['REQUEST_METHOD']='GET';
 */

(new App)->run();
