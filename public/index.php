<?php declare(strict_types=1);

use IamIn\Otusp2025PhpPackage\DateInterval as DateIntervalIamIn;

require __DIR__ . '/../vendor/autoload.php';

try {
    // Создание интервала
    $intervalString = DateIntervalIamIn::composePeriod(2, DateIntervalIamIn::PERIOD_MONTH);
    echo $intervalString . PHP_EOL; // Output: P2M

    // Преобразование интервала в объект DateInterval
    $intervalObject = DateIntervalIamIn::periodToInterval('P2M');
    print_r($intervalObject); // Разбиение интервала на количество и тип периода

    [$count, $period] = DateIntervalIamIn::periodToCountAndPeriodString('P2M');
    echo "Count: $count, Period: $period" . PHP_EOL; // Output: Count: 2, Period: M

    // Преобразование интервала в читаемую строку
    $readableInterval = DateIntervalIamIn::intervalToString('P2M');
    echo $readableInterval . PHP_EOL; // Output: 2 месяца

    // Ошибка: Строка интервала не может быть пустой
    $readableInterval = DateIntervalIamIn::intervalToString('');
} catch (\Throwable $e) {
    echo 'Ошибка: ' . $e->getMessage();
}