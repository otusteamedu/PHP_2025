<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Message\StatementRequestMessage;
use App\Service\RabbitMQService;
use App\Service\EmailService;

$queueService = new RabbitMQService();
$emailService = new EmailService();

echo " [*] Statement Queue Worker started\n";
echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (StatementRequestMessage $message) use ($emailService) {
    echo " [x] Received statement request: " . $message->getRequestId() . "\n";
    echo "     Email: " . $message->getEmail() . "\n";
    echo "     Account: " . $message->getAccountNumber() . "\n";
    echo "     Period: " . $message->getStartDate() . " to " . $message->getEndDate() . "\n";
    
    echo " [*] Processing statement...\n";
    
    $statementContent = $this->generateStatement($message);
    
    $emailService->sendNotification(
        $message->getEmail(),
        "Банковская выписка по счету " . $message->getAccountNumber(),
        "Ваша выписка за период с " . $message->getStartDate() . " по " . $message->getEndDate() . " готова.\n\n" .
        "Содержимое выписки:\n" . $statementContent . "\n\n" .
        "ID запроса: " . $message->getRequestId()
    );
    
    echo " [x] Completed processing request: " . $message->getRequestId() . "\n\n";
};

$queueService->consume($callback);

function generateStatement(StatementRequestMessage $message): string
{
    // Имитация генерации выписки
    $transactions = [];
    $balance = 10000;

    $start = strtotime($message->getStartDate());
    $end = strtotime($message->getEndDate());
    
    for ($i = 0; $i < 10; $i++) {
        $date = date('Y-m-d', rand($start, $end));
        $amount = rand(-500, 1000);
        $balance += $amount;
        
        $transactions[] = [
            'date' => $date,
            'description' => $amount > 0 ? 'Поступление' : 'Списание',
            'amount' => $amount,
            'balance' => $balance
        ];
    }
    
    $statement = "ВЫПИСКА ПО СЧЕТУ: " . $message->getAccountNumber() . "\n";
    $statement .= "ПЕРИОД: " . $message->getStartDate() . " - " . $message->getEndDate() . "\n\n";
    $statement .= "ДАТА\t\tОПЕРАЦИЯ\t\tСУММА\t\tБАЛАНС\n";
    $statement .= "------------------------------------------------------------\n";
    
    foreach ($transactions as $transaction) {
        $statement .= sprintf("%s\t%s\t\t%.2f\t\t%.2f\n",
            $transaction['date'],
            $transaction['description'],
            $transaction['amount'],
            $transaction['balance']
        );
    }
    
    return $statement;
}