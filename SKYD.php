<?php

ini_set('error_reporting', E_ERROR);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;


$username = "test";
$password = "12345678";

class SKYD
{
	private $urlAPI = "/ISAPI/AccessControl/AcsEvent?format=json";
	private $userUrlApi = "/ISAPI/AccessControl/UserInfo/Search?format=json";
    private $validUser;
    private $validPassword;
	private $inServerConf = ["host"=>"192.168.11.207", "login"=>"admin", "password"=>"12345678!"];
	private $outServerConf = ["host"=>"192.168.11.205", "login"=>"admin", "password"=>"12345678!"];
	private $testServerConf = ["host"=>"192.168.11.206", "login"=>"admin", "password"=>"12345678!"];
	private $lunchStart = '12:00:00'; // Начало обеда (например, 12:00)
	private	$lunchEnd = '13:00:00';   // Окончание обеда (например, 13:00)
	private $startEntries = '08:00:00';
	private $endExists = '17:00:00';
	
	private $dbHost = '127.0.0.1';
    private $dbName = 'skyd_db';
    private $dbUser = 'root';
    private $dbPass = '';

    private $tableName = 'skyd_visits';
    private $usersTableName = "users_skyd";
    private $timePrecision = 2;// на сколько сглаживать входы выходы - 0 отключить, 2- две минуты и тд
    private $graceMinutes = 1; // на сколько минут можно опаздать

    public function __construct($user, $password)
    {
		date_default_timezone_set('Asia/Aqtau');
        $this->validUser = $user;
        $this->validPassword = $password;

     //   $this->checkAuth();
		
		$this->initDb();
        $this->createTableIfNotExists();
    }

    // Проверка авторизации
    private function checkAuth()
    {
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER'] !== $this->validUser ||
            $_SERVER['PHP_AUTH_PW'] !== $this->validPassword) {

            header('WWW-Authenticate: Basic realm="Protected Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Доступ запрещён';
            exit;
        }
    }
	
private function initDb()
    {
        try {
            // Используем отдельную БД skyd_db — если её нет, попытаемся создать
            $dsn = "mysql:host={$this->dbHost};charset=utf8mb4";
            $pdo = new PDO($dsn, $this->dbUser, $this->dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);


            // Попробуем создать БД, если не существует
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");

            // Подключаемся уже к ней
            $dsnDb = "mysql:host={$this->dbHost};dbname={$this->dbName};charset=utf8mb4";
            $this->pdo = new PDO($dsnDb, $this->dbUser, $this->dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // Обработка ошибки
            die("DB connection error: " . $e->getMessage());
        }
    }

    private function createTableIfNotExists()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$this->tableName}` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `user_id` VARCHAR(100) DEFAULT NULL,
            `user_name` VARCHAR(255) DEFAULT NULL,
            `event_type` VARCHAR(100) DEFAULT NULL,
            `event_time` DATETIME NOT NULL,
            `device_name` VARCHAR(255) DEFAULT NULL,
            `raw_json` TEXT,
            INDEX (`user_id`),
            INDEX (`event_time`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $this->pdo->exec($sql);

        // Таблица пользователей
        $usersTable = "
        CREATE TABLE IF NOT EXISTS `{$this->usersTableName}` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `code` VARCHAR(100) NOT NULL,
            `fio` VARCHAR(255) DEFAULT NULL,
            `gender` VARCHAR(50) DEFAULT NULL,
            `group_id` INT DEFAULT NULL,
            `card` VARCHAR(100) DEFAULT NULL,
            `photo` VARCHAR(255) DEFAULT NULL,
            `status` VARCHAR(500) DEFAULT NULL,
            UNIQUE KEY `uniq_code` (`code`),
            INDEX (`fio`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
        $this->pdo->exec($usersTable);
    }


    // Читает события из БД (по дате)
    private function getVisitsFromDB(string $start, string $end, string $device): array
    {
        $sql = "SELECT user_id, user_name, event_type, event_time, device_name, raw_json FROM {$this->tableName} WHERE device_name= :device AND event_time BETWEEN :start AND :end ORDER BY event_time ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':device' => $device,':start' => $start, ':end' => $end]);
        $rows = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'date' => $r['event_time'],
                'fio' => $r['user_name'],
                'verify' => $r['event_type'],
                'code' => $r['user_id'],
                'device' => $r['device_name'],
                'raw_json' => $r['raw_json'],
            ];
        }
        return $result;
    }	
	
	
	
	private function prepareVisitDate($date = null)
{
    if ($date) {
        // Если дата задана, приводим к правильному формату
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateTime) {
            // Попробуем другие форматы
            $dateTime = new DateTime($date);
        }
        
        // Устанавливаем случайное время между 7:52 и 7:59
        $randomMinutes = rand(52, 59);
        $dateTime->setTime(7, $randomMinutes, 0);
        
        return $dateTime->format('Y-m-d H:i:s');
    } else {
        // Текущий день с рандомным временем
        $currentDate = new DateTime();
        $randomMinutes = rand(52, 59);
        $currentDate->setTime(7, $randomMinutes, 0);
        
        return $currentDate->format('Y-m-d H:i:s');
    }
}


public function saveVisitsForClients(array $clientCodes, $device = null, $date = null)
{
	$device = $device ?? $this->inServerConf;
    if (empty($clientCodes)) {
        return false;
    }
    
    $visits = [];
    
    foreach ($clientCodes as $code) {
        try {
            // Ищем клиента в таблице users_skyd
            $sql = "SELECT * FROM {$this->usersTableName} WHERE code = :code LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':code' => $code]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$client) {
                // Клиент не найден, пропускаем
                continue;
            }
            
            // Подготавливаем данные для визита
            $visitData = [
                'code' => $client['code'],
                'fio' => $client['fio'],
                'verify' => 'enter', // или другой тип события по умолчанию
                'date' => $this->prepareVisitDate($date)
            ];
            
            $visits[] = $visitData;
            
        } catch (Exception $e) {
            // Логируем ошибку, но продолжаем обработку других клиентов
            error_log("Ошибка при обработке клиента с кодом {$code}: " . $e->getMessage());
            continue;
        }
    }
    
    // Вызываем существующую функцию для сохранения визитов
    $this->saveVisitsToDB($visits, '192.168.11.205');
    
    return $visits;
}
	
	
	
	private function saveVisitsToDB(array $visits, $device)
    {
        if (empty($visits)) return;
        $sql = "INSERT INTO {$this->tableName} (user_id, user_name, event_type, event_time, device_name)
                VALUES (:user_id, :user_name, :event_type, :event_time, :device_name)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($visits as $v) {

            try{
            // Приведение
            $userId = $v['code'] ?? null;
            $userName = $v['fio'] ?? null;
            $eventType = $v['verify'] ?? null;
            $eventTime = $v['date'] ? date("Y-m-d H:i:s",strtotime($v['date'])) : null;

            if(!$eventTime) continue;
            // Проверяем на дубликат (точно такой же user_id + event_time + device)
            $chkSql = "SELECT COUNT(*) FROM {$this->tableName} WHERE user_id = :user_id AND event_time = :event_time AND device_name = :device_name";
            $chk = $this->pdo->prepare($chkSql);
            $chk->execute([':user_id' => $userId, ':event_time' => $eventTime, ':device_name' => $device]);
            $count = (int)$chk->fetchColumn();
            if ($count > 0) continue;


            $stmt->execute([
                ':user_id' => $userId,
                ':user_name' => $userName,
                ':event_type' => $eventType,
                ':event_time' => $eventTime,
                ':device_name' => $device
            ]);

        }catch (Exception $e)
            {

            }
        }
    }


    private function exportToExcel(
        array $allVisits,
        array $late,
        array $cameEarly,
        array $leftEarly,
        array $didNotLeave,
        array $neverCame,
        array $simpleVisits,
        array $lateAfterLunch,      // опоздали после обеда
        array $leftEarlyToLunch,    // ушли рано на обед
        string $date,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): void {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheetAll = $spreadsheet->getActiveSheet();
        $sheetAll->setTitle('Все посещения');
        $this->fillVisitSheet($sheetAll, $allVisits, 'Все посещения', $dateFrom, $dateTo);

        $sheetLate = $spreadsheet->createSheet();
        $sheetLate->setTitle('Опоздавшие');
        $this->fillVisitSheet($sheetLate, $late, 'Опоздавшие', $dateFrom, $dateTo);

        $sheetEarlyIn = $spreadsheet->createSheet();
        $sheetEarlyIn->setTitle('Пришли рано');
        $this->fillVisitSheet($sheetEarlyIn, $cameEarly, 'Пришли рано', $dateFrom, $dateTo);

        $sheetEarlyOut = $spreadsheet->createSheet();
        $sheetEarlyOut->setTitle('Ушли рано');
        $this->fillVisitSheet($sheetEarlyOut, $leftEarly, 'Ушли рано', $dateFrom, $dateTo);

        $sheetNoOut = $spreadsheet->createSheet();
        $sheetNoOut->setTitle('Не вышли');
        $this->fillVisitSheet($sheetNoOut, $didNotLeave, 'Не вышли', $dateFrom, $dateTo);

        // ← НОВЫЙ ЛИСТ: Опоздали после обеда
        $sheetLateAfterLunch = $spreadsheet->createSheet();
        $sheetLateAfterLunch->setTitle('Опоздали после обеда');
        $this->fillVisitSheet($sheetLateAfterLunch, $lateAfterLunch, 'Опоздали после обеда', $dateFrom, $dateTo);

        // ← НОВЫЙ ЛИСТ: Ушли рано на обед
        $sheetLeftEarlyToLunch = $spreadsheet->createSheet();
        $sheetLeftEarlyToLunch->setTitle('Ушли рано на обед');
        $this->fillVisitSheet($sheetLeftEarlyToLunch, $leftEarlyToLunch, 'Ушли рано на обед', $dateFrom, $dateTo);

        // ← Лист "Не приходили" (уже был, но оставим для полноты)
        $sheetNeverCame = $spreadsheet->createSheet();
        $sheetNeverCame->setTitle('Не приходили');
        $this->fillNeverCameSheet($sheetNeverCame, $neverCame, $dateFrom, $dateTo);

        $sheetSimple = $spreadsheet->createSheet();
        $sheetSimple->setTitle('Просто посещения');
        $this->fillSimpleSheet($sheetSimple, $simpleVisits, $dateFrom, $dateTo);

        $spreadsheet->setActiveSheetIndex(0);

        // Отдача
        $filename = "skyd_report_{$date}_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    private function fillVisitSheet(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        array $visits,
        string $sectionTitle,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): void {
        // === Строка 1: "Система СКУД" ===
        $sheet->setCellValue('A1', 'Система СКУД');
        $sheet->mergeCells('A1:E1');

        $dateFromUnix = strtotime($dateFrom);
        $dateToUnix = strtotime($dateTo);

        $diffSeconds = abs($dateToUnix - $dateFromUnix);
        $days = floor($diffSeconds / (60 * 60 * 24));

        // === Строка 2: "Отчёт с ... по ..." ===
        $dateRange = $dateFrom && $dateTo
            ? "Отчёт с {$dateFrom} по {$dateTo}"
            : 'Отчёт';
        $sheet->setCellValue('A2', $dateRange);
        $sheet->mergeCells('A2:E2');

        // === Строка 3: Название раздела (например, "Опоздавшие") ===
        $sheet->setCellValue('A3', $sectionTitle);
        $sheet->mergeCells('A3:E3');

        // Стиль шапки (все три строки)
        $sheet->getStyle('A1:E3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Цвет фона для заголовка категории (опционально)
        $sheet->getStyle('A3:E3')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF5DEB3'], // светло-бежевый или любой акцент
            ],
        ]);

        // === Строка 4: Заголовки таблицы ===
        $headers = ['№', 'ФИО', 'Время входа', 'Время выхода', 'Примечание'];
        $colIndex = 1;
        foreach ($headers as $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++);
            $sheet->setCellValue($col . '4', $header);
        }

        // Стиль заголовков таблицы
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9E1F2'],
            ],
        ];
        $sheet->getStyle('A4:E4')->applyFromArray($headerStyle);

        // === ДАННЫЕ: начиная с 5-й строки ===
        $dataStartRow = 5;

        if (empty($visits)) {
            $sheet->setCellValue("A{$dataStartRow}", 'Нет данных');
            $lastDataRow = $dataStartRow;
        } else {
            $grouped = [];
            foreach ($visits as $v) {
                $fio = $v['fio'] ?? '';
                $grouped[$fio][] = $v;
            }

            $row = $dataStartRow;
            $number = 1;
            $groupRanges = [];

            foreach ($grouped as $fio => $entries) {
                $startRow = $row;
                $endRow = $row + count($entries) - 1;

                foreach ($entries as $idx => $v) {

                    $dateIn = isset($v['dateIn']) && !empty($v['dateIn']) ? $v['dateIn'] : '';
                    $dateOut = isset($v['dateOut']) && !empty($v['dateOut']) ? $v['dateOut'] : '';

                    $dateInRow = $days < 1 && $dateIn !== '' ? date("H:i:s", strtotime($dateIn)) : $dateIn;
                    $dateOutRow = $days < 1 && $dateOut !== '' ? date("H:i:s", strtotime($dateOut)) : $dateOut;

                    $sheet->setCellValue("A{$row}", $idx === 0 ? $number++ : '');
                    $sheet->setCellValue("B{$row}", $idx === 0 ? $fio : '');
                    $sheet->setCellValue("C{$row}", $dateInRow ?? '');
                    $sheet->setCellValue("D{$row}", $dateOutRow ?? '');
                    $sheet->setCellValue("E{$row}", $v['status'] ?? '');
                    $row++;
                }

                $groupRanges[] = ['start' => $startRow, 'end' => $endRow];
            }

            $lastDataRow = $row - 1;

            // Стили для групп
            $colors = ['FFF0F0F0', 'FFE6F2FF', 'FFF0FFF0', 'FFFFF8E1'];
            $colorIndex = 0;
            foreach ($groupRanges as $range) {
                $r = "A{$range['start']}:E{$range['end']}";
                $color = $colors[$colorIndex++ % count($colors)];
                $sheet->getStyle($r)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($color);

                $sheet->getStyle($r)->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF888888'],
                        ],
                    ],
                ]);
            }
        }

        // === Автоширина ===
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    private function fillSimpleSheet(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        array $simpleVisits,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): void {
        $sheet->setCellValue('A1', 'Система СКУД');
        $sheet->mergeCells('A1:F1');

        $dateRange = $dateFrom && $dateTo
            ? "Отчёт с {$dateFrom} по {$dateTo}"
            : 'Отчёт';
        $sheet->setCellValue('A2', $dateRange);
        $sheet->mergeCells('A2:F2');

        $sheet->setCellValue('A3', 'Просто посещения');
        $sheet->mergeCells('A3:F3');

        $sheet->getStyle('A1:F3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A3:F3')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF5DEB3'],
            ],
        ]);

        // Заголовки таблицы — строка 4
        $headers = ['№', 'ФИО', 'ID', 'Время (первое)', 'Тип события', 'Устройство'];
        $colIndex = 1;
        foreach ($headers as $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++);
            $sheet->setCellValue($col . '4', $header);
        }

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9E1F2'],
            ],
        ];
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);

        // Данные — с 5-й строки
        $row = 5;
        if (empty($simpleVisits)) {
            $sheet->setCellValue('A5', 'Нет данных');
        } else {
            $num = 1;
            foreach ($simpleVisits as $v) {
                $sheet->setCellValue("A{$row}", $num++);
                $sheet->setCellValue("B{$row}", $v['fio'] ?? '');
                $sheet->setCellValue("C{$row}", $v['code'] ?? '');
                $sheet->setCellValue("D{$row}", $v['date'] ?? '');
                $sheet->setCellValue("E{$row}", $v['verify'] ?? '');
                $sheet->setCellValue("F{$row}", $v['device'] ?? '');
                $row++;
            }
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
public	function generateWordDocument($data)
{
    // Создаем новый документ
    $phpWord = new PhpWord();

    // Добавляем секцию
    $section = $phpWord->addSection();

    // Создаем таблицу
    $table = $section->addTable([
        'borderSize' => 6,
        'borderColor' => '000000',
        'cellMargin' => 50,
    ]);

    // Заголовки таблицы
    $headers = ['№', 'ФИО', 'Должность', 'Дата', 'Время входа', 'Время выхода', 'Обед', 'Статус'];
    $table->addRow();
    foreach ($headers as $header) {
        $table->addCell(2000)->addText($header, ['bold' => true]);
    }

    // Функция для форматирования времени
    $formatTime = function ($time) {
        if (empty($time)) {
            return 'Не указано';
        }

        // Проверяем формат даты
        $dateTime = \DateTime::createFromFormat('d.m.Y H:i:s', $time);
        if (!$dateTime) {
            return 'Неверный формат';
        }

        $hours = $dateTime->format('H');
        $minutes = $dateTime->format('i');

        // Возвращаем массив с часами и минутами
        return [
            'hours' => $hours,
            'minutes' => $minutes,
        ];
    };

    // Обработка данных
    foreach ($data as $index => $item) {
        $rowNumber = $index + 1; // Номер строки

        // Дефолтные значения
        $fio = $item['fio'] ?? 'Не указано';
        $dateIn = $item['dateIn'] ?? '';
        $dateOut = $item['dateOut'] ?? '';
        $lunchIn = $item['lunchIn'] ?? '';
        $lunchOut = $item['lunchOut'] ?? '';
        $status = $item['status'] ?? '';

        // Добавляем строку в таблицу
        $table->addRow();

        // №
        $table->addCell(500)->addText($rowNumber);

        // ФИО
        $table->addCell(2000)->addText($fio);

        // Должность (пустая колонка)
        $table->addCell(2000)->addText('Не указана');

        // Дата
        $table->addCell(2000)->addText(date('d.m.Y', strtotime($dateIn)));

        // Время входа
        $formattedTimeIn = $formatTime($dateIn);
        $cellIn = $table->addCell(2000);
        if ($formattedTimeIn !== 'Не указано') {
            $textrunIn = $cellIn->addTextRun();
            $textrunIn->addText($formattedTimeIn['hours'], ['size' => 14]); // Часы
            $textrunIn->addText($formattedTimeIn['minutes'], ['size' => 10, 'superScript' => true]); // Минуты
        } else {
            $cellIn->addText('Не указано');
        }

        // Время выхода
        $formattedTimeOut = $formatTime($dateOut);
        $cellOut = $table->addCell(2000);
        if ($formattedTimeOut !== 'Не указано') {
            $textrunOut = $cellOut->addTextRun();
            $textrunOut->addText($formattedTimeOut['hours'], ['size' => 14]); // Часы
            $textrunOut->addText($formattedTimeOut['minutes'], ['size' => 10, 'superScript' => true]); // Минуты
        } else {
            $cellOut->addText('Не указано');
        }

        // Обед (спаренные ячейки)
        $cellLunch = $table->addCell(4000, ['gridSpan' => 2]); // Объединяем две ячейки
        $formattedLunchOut = $formatTime($lunchOut);
        $formattedLunchIn = $formatTime($lunchIn);

        if ($formattedLunchOut !== 'Не указано') {
            $cellLunch->addText("Выход: ");
            $textrunLunchOut = $cellLunch->addTextRun();
            $textrunLunchOut->addText($formattedLunchOut['hours'], ['size' => 14]);
            $textrunLunchOut->addText($formattedLunchOut['minutes'], ['size' => 10, 'superScript' => true]);
        } else {
            $cellLunch->addText("Выход: Не указано");
        }

        if ($formattedLunchIn !== 'Не указано') {
            $cellLunch->addText("\nВход: ");
            $textrunLunchIn = $cellLunch->addTextRun();
            $textrunLunchIn->addText($formattedLunchIn['hours'], ['size' => 14]);
            $textrunLunchIn->addText($formattedLunchIn['minutes'], ['size' => 10, 'superScript' => true]);
        } else {
            $cellLunch->addText("\nВход: Не указано");
        }

        // Статус
        $table->addCell(3000)->addText($status);
    }

    // Сохраняем документ
    $fileName = 'report.docx';
    $phpWord->save($fileName, 'Word2007');

    echo "Документ успешно создан: {$fileName}";
}

    // Ваша логика работы
    public function run()
    {
		
		

	    $startDate = '2025-02-01 00:00:00';
        $endDate = '2025-02-28 23:59:59';
		
		$startDate = $_REQUEST['dateStart'] ?? $startDate;
		$endDate = $_REQUEST['dateEnd'] ?? $endDate;
		
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 23:59:59", strtotime($endDate));
		
		
	   $innerParseData = $this->getLoadAllData($this->inServerConf,$startDate, $endDate , 20);
	   $outerParseData = $this->getLoadAllData($this->outServerConf,$startDate, $endDate , 20);

  
	  $result = $this->processEntriesAndExits($innerParseData, $outerParseData);
		
	  $this->sendResponse($result, "данные получены");
		
    }
	
	
	private function sendResponse(?array $data,$message = "" ,$success = true, $statusCode = 200) {
		
		
			// Пример использования:
	$responseBody = [
			'success' => $success,
			'message' => $message,
			'data' => $data
		];	
		
    // Устанавливаем HTTP-код ответа
    http_response_code($statusCode);
    // Устанавливаем заголовок Content-Type для JSON
    header('Content-Type: application/json; charset=utf-8');
    // Преобразуем данные в JSON
    echo json_encode($responseBody, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    // Завершаем выполнение скрипта
    exit;
}


function calculateStartEndLunchTime($entries) {
    $processedEntries = [];

    foreach ($entries as $dayCode => $element) {
        foreach ($element as $entry) {
            // Преобразуем даты в объекты DateTime для удобства работы
            $dateIn = new DateTime($entry['dateIn']);
            $dateOut = $entry['dateOut'] ? new DateTime($entry['dateOut']) : null;
            $lunchIn = $entry['lunchIn'] ? new DateTime($entry['lunchIn']) : null;
            $lunchOut = $entry['lunchOut'] ? new DateTime($entry['lunchOut']) : null;

            // Временные рамки (время без даты)
            $startTime = new DateTime($this->startEntries);
            $endTime = new DateTime($this->endExists);
            $lunchStart = new DateTime($this->lunchStart);
            $lunchEnd = new DateTime($this->lunchEnd);

            // Приводим временные рамки к дате входа ($dateIn)
            $startTime->setDate((int)$dateIn->format('Y'), (int)$dateIn->format('m'), (int)$dateIn->format('d'));
            $endTime->setDate((int)$dateIn->format('Y'), (int)$dateIn->format('m'), (int)$dateIn->format('d'));
            $lunchStart->setDate((int)$dateIn->format('Y'), (int)$dateIn->format('m'), (int)$dateIn->format('d'));
            $lunchEnd->setDate((int)$dateIn->format('Y'), (int)$dateIn->format('m'), (int)$dateIn->format('d'));

            // Если $dateOut относится к следующему дню, корректируем $endTime
            if ($dateOut && $dateOut->format('Y-m-d') !== $dateIn->format('Y-m-d')) {
                $endTime->modify('+1 day'); // Сдвигаем $endTime на следующий день
            }

            // Проверка dateIn
            if ($dateIn < $startTime) {
                $diff = $startTime->diff($dateIn);
                $entry['statusIn'] = "Вошел раньше на " . $diff->format('%h час %i минут');
                $entry['statuses'][] = [
                    "code" => "early_morning",
                    "time" => $diff->format('%H:%I'),
                    "message" => "Вошел раньше на " . $diff->format('%h час %i минут')
                ];
            } elseif ($dateIn > $startTime) {
                $diff = $dateIn->diff($startTime);
                $entry['statusIn'] = "Опоздал на " . $diff->format('%h час %i минут');
                $entry['statuses'][] = [
                    "code" => "late_morning",
                    "time" => $diff->format('%H:%I'),
                    "message" => "Опоздал на " . $diff->format('%h час %i минут')
                ];
            } else {
                $entry['statusIn'] = "Вошел вовремя";
                $entry['statuses'][] = [
                    "code" => "came_in_on_time",
                    "time" => "00:00",
                    "message" => "Вошел вовремя"
                ];
            }

            // Проверка dateOut
            if ($dateOut) {
                if ($dateOut > $endTime) {
                    $diff = $dateOut->diff($endTime);
                    $entry['statusOut'] = "Вышел позже на " . $diff->format('%h час %i минут');
                    $entry['statuses'][] = [
                        "code" => "left_late",
                        "time" => $diff->format('%H:%I'),
                        "message" => "Вышел позже на " . $diff->format('%h час %i минут')
                    ];
                } elseif ($dateOut < $endTime) {
                    $diff = $endTime->diff($dateOut);
                    $entry['statusOut'] = "Ушел раньше на " . $diff->format('%h час %i минут');
                    $entry['statuses'][] = [
                        "code" => "left_early",
                        "time" => $diff->format('%H:%I'),
                        "message" => "Ушел раньше на " . $diff->format('%h час %i минут')
                    ];
                } else {
                    $entry['statusOut'] = "Вышел вовремя";
                    $entry['statuses'][] = [
                        "code" => "came_out_on_time",
                        "time" => "00:00",
                        "message" => "Вышел вовремя"
                    ];
                }
            } else {
                $entry['statusOut'] = "Не вышел";
                $entry['statuses'][] = [
                    "code" => "not_came_out",
                    "time" => "00:00",
                    "message" => "Не вышел"
                ];
            }

            // Проверка lunchIn
            if ($lunchIn) {
                if ($lunchIn < $lunchStart) {
                    $diff = $lunchStart->diff($lunchIn);
                    $entry['statusLunchIn'] = "На обед вышел раньше на " . $diff->format('%i минут');
                    $entry['statuses'][] = [
                        "code" => "left_early_lunch",
                        "time" => $diff->format('%H:%I'),
                        "message" => "На обед вышел раньше на " . $diff->format('%i минут')
                    ];
                } elseif ($lunchIn > $lunchStart) {
                    $diff = $lunchIn->diff($lunchStart);
                    $entry['statusLunchIn'] = "На обед опоздал на " . $diff->format('%i минут');
                    $entry['statuses'][] = [
                        "code" => "left_late_lunch",
                        "time" => $diff->format('%H:%I'),
                        "message" => "На обед опоздал на " . $diff->format('%i минут')
                    ];
                } else {
                    $entry['statusLunchIn'] = "На обед вышел вовремя";
                    $entry['statuses'][] = [
                        "code" => "left_on_time_lunch",
                        "time" => "00:00",
                        "message" => "На обед вышел вовремя"
                    ];
                }
            } else {
                $entry['statusLunchIn'] = "Не был на обеде";
                $entry['statuses'][] = [
                    "code" => "not_left_lunch",
                    "time" => "00:00",
                    "message" => "Не был на обеде"
                ];
            }

            // Проверка lunchOut
            if ($lunchOut) {
                if ($lunchOut > $lunchEnd) {
                    $diff = $lunchOut->diff($lunchEnd);
                    $entry['statusLunchOut'] = "С обеда пришел позже на " . $diff->format('%i минут');
                    $entry['statuses'][] = [
                        "code" => "returned_late_lunch",
                        "time" => $diff->format('%H:%I'),
                        "message" => "С обеда пришел позже на " . $diff->format('%i минут')
                    ];
                } elseif ($lunchOut < $lunchEnd) {
                    $diff = $lunchEnd->diff($lunchOut);
                    $entry['statusLunchOut'] = "С обеда вернулся раньше на " . $diff->format('%i минут');
                    $entry['statuses'][] = [
                        "code" => "returned_early_lunch",
                        "time" => $diff->format('%H:%I'),
                        "message" => "С обеда вернулся раньше на " . $diff->format('%i минут')
                    ];
                } else {
                    $entry['statusLunchOut'] = "С обеда ушел вовремя";
                    $entry['statuses'][] = [
                        "code" => "returned_on_time_lunch",
                        "time" => "00:00",
                        "message" => "С обеда ушел вовремя"
                    ];
                }
            } else {
                $entry['statusLunchOut'] = "Не был на обеде";
                $entry['statuses'][] = [
                    "code" => "not_lunch",
                    "time" => "00:00",
                    "message" => "Не был на обеде"
                ];
            }

            // Формирование итогового статуса
            $entry['status'] = "<ul>" .
                (isset($entry['statusIn']) ? "<li>Вход: {$entry['statusIn']}</li>" : "") .
                (isset($entry['statusOut']) ? "<li>Выход: {$entry['statusOut']}</li>" : "") .
                (isset($entry['statusLunchIn']) ? "<li>Обед начало: {$entry['statusLunchIn']}</li>" : "") .
                (isset($entry['statusLunchOut']) ? "<li>Обед окончание: {$entry['statusLunchOut']}</li>" : "") .
                "</ul>";

            $processedEntries[$dayCode][] = $entry;
        }
    }

    return $processedEntries;
}
	
	private function processEntriesAndExits($entries, $exits) {
    // Вспомогательная функция для преобразования даты в DateTime
    function parseDate($dateString) {
        return DateTime::createFromFormat('d.m.Y H:i:s', $dateString);
    }

    // Группируем входы и выходы по дням и кодам пользователей
    $allEntries = [];
    $allExits = [];
    $LunchEntries = [];
    $LunchExits = [];

    // Обрабатываем входы
    foreach ($entries as $entry) {
        $date = parseDate($entry['date']);
        $dayKey = $date->format('Ymd'); // Ключ для группировки по дням
        $code = $entry['code'];
        $time = strtotime($date->format('H:i:s'));

        // Сохраняем все входы
        if (!isset($allEntries[$code][$dayKey])) {
            $allEntries[$code][$dayKey] = [];
        }
        $allEntries[$code][$dayKey][] = $entry;

        // Проверяем, попадает ли время входа в интервал обеда
        if ($time >= strtotime($this->lunchStart) && $time <= strtotime($this->lunchEnd)) {
            $LunchExits[$code][$dayKey] = $entry;
        }
    }

    // Обрабатываем выходы
    foreach ($exits as $exit) {
        $date = parseDate($exit['date']);
        $dayKey = $date->format('Ymd'); // Ключ для группировки по дням
        $code = $exit['code'];
        $time = strtotime($date->format('H:i:s'));

        // Сохраняем все выходы
        if (!isset($allExits[$code][$dayKey])) {
            $allExits[$code][$dayKey] = [];
        }
        $allExits[$code][$dayKey][] = $exit;

        // Проверяем, попадает ли время выхода в интервал обеда
        if ($time >= strtotime($this->lunchStart) && $time <= strtotime($this->lunchEnd)) {
            $LunchEntries[$code][$dayKey] = $exit;
        }
    }

    // Объединяем данные и группируем по дням
    $result = [];
    foreach ($allEntries as $code => $days) {
        foreach ($days as $dayKey => $entriesForDay) {
            // Сортируем входы и выходы по времени
            usort($entriesForDay, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            $exitsForDay = $allExits[$code][$dayKey] ?? [];
            usort($exitsForDay, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            // Выбираем первые три входа и выхода
            $firstEntry = $entriesForDay[0]['date'] ?? '';
            $secondEntry = $entriesForDay[1]['date'] ?? '';
            $thirdEntry = $entriesForDay[2]['date'] ?? '';

            $firstExit = $exitsForDay[0]['date'] ?? '';
            $secondExit = $exitsForDay[1]['date'] ?? '';
            $thirdExit = $exitsForDay[2]['date'] ?? '';

            // Добавляем данные в массив, сгруппированный по дням
            $result["d_" . $dayKey][] = [
                'fio' => $entriesForDay[0]['fio'] ?? '',
                'dateIn' => $firstEntry,
                'dateOut' => $firstExit,
                'secondEntry' => $secondEntry,
                'secondExit' => $secondExit,
                'thirdEntry' => $thirdEntry,
                'thirdExit' => $thirdExit,
                'lunchIn' => $LunchEntries[$code][$dayKey]['date'] ?? '',
                'lunchOut' => $LunchExits[$code][$dayKey]['date'] ?? '',
                'code' => $code ?? ''
            ];
        }
    }

    return $result;
}


	private function parseUser($data)
	{
		$response = [];
		$data = json_decode($data, true);
		
		$UserInfoSearch = $data['UserInfoSearch'];
		$totalMatches = $UserInfoSearch['totalMatches'];
		$numOfMatches = $UserInfoSearch['numOfMatches'];
		$UserInfo =$UserInfoSearch['UserInfo'];
		
		if(is_array($UserInfo))
			foreach($UserInfo as $item)
			{

				$name = $item['name'] ?? false; // Шағиахмет Олжас Сабыржанұлы
				$employeeNoString = $item['employeeNo'] ?? false; 
				$faceURL = $item['faceURL'];// faceURL
				$gender= $item['gender'];
				$groupId = $item['groupId'];
				$numOfCard = $item['numOfCard'];
				$numOfFace = $item['numOfFace'];
				$userType = $item['userType']; // normal

				
				
				if($employeeNoString)
				$response[] = array(
				'faceURL'=>$faceURL,
				'fio'=> trim($name),
				'gender'=>$gender,
				'code'=>$employeeNoString,
				'groupId'=>$groupId,
				'numOfCard'=>$numOfCard,
				'numOfFace'=>$numOfFace
				);
				
			}
			
			return $response;
		
		
	}

	
	private function parseData($data)
	{
		$response = [];
		$data = json_decode($data, true);
		
		$AcsEvent = $data['AcsEvent'];
		$totalMatches = $AcsEvent['totalMatches'];
		$numOfMatches = $AcsEvent['numOfMatches'];
		$InfoList =$AcsEvent['InfoList'];
		
		if(is_array($InfoList))
			foreach($InfoList as $item)
			{


//var_dump($item);
//continue;
				// это события минимальное
				$major = $item['major'];
				$minor = $item['minor'];
				$time = $this->convertIsoToReadableDate($item['time']); // "2025-02-24T16:57:33+05:00"
				$serialNo = $item['serialNo'] ?? false;
				$currentVerifyMode = $item['currentVerifyMode'];
				$mask = $item['mask'] ?? false; // "unknown"  "no"
				$name = $item['name'] ?? false; // Шағиахмет Олжас Сабыржанұлы
				$employeeNoString = $item['employeeNoString'] ?? false; 
				$currentVerifyMode = $item['currentVerifyMode'];// cardOrfaceOrPw

				
				
				if($employeeNoString)
				$response[] = array(
				'date'=>$time,
				'fio'=> $name,
				'verify'=>$currentVerifyMode,
				'code'=>$employeeNoString
				);
				
			}
			
			return $response;
		
		
	}
	
	// '2025-02-24 00:00:00'
	private function formatDate($date)
	{
		// Создаем объект DateTime с заданной датой
		$date = new DateTime($date, new DateTimeZone('Asia/Aqtau'));
		// Устанавливаем часовой пояс (если нужно)
		$date->setTimezone(new DateTimeZone('Asia/Aqtau'));
		// Форматируем дату в ISO 8601
		$formattedDate = $date->format('Y-m-d\TH:i:sP');
		return $formattedDate; // Вывод: "2025-02-24T00:00:00+05:00"
	}
	
	private function convertIsoToReadableDate($isoDate) {
    try {
        // Создаем объект DateTime из строки ISO 8601
        $dateTime = new DateTime($isoDate);

        // Форматируем дату в удобочитаемый формат
        return $dateTime->format('d.m.Y H:i:s');
    } catch (Exception $e) {
        // Если возникла ошибка при обработке даты
        return 'Некорректная дата';
    }
}


    public function getUsersFromServiceHikvision($confServer, $searchResultPosition, $maxResult){
		
		 $url = "https://{$confServer['host']}{$this->userUrlApi}";

    // Логин и пароль для авторизации
    $username = $confServer['login'];
    $password = $confServer['password'];

    $searchID = md5($username.$password.$searchResultPosition.$maxResult);
	$payload = ["UserInfoSearchCond"=>["searchID"=>$searchID,"maxResults"=>$maxResult,"searchResultPosition"=>$searchResultPosition]];
	 // Преобразуем тело запроса в JSON
    $jsonPayload = json_encode($payload);

    // Инициализация cURL
    $ch = curl_init();

    // Установка параметров cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключение проверки SSL (если требуется)
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Не проверять имя хоста

    // Включение Digest-авторизации
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

    // Выполнение запроса
    $response = curl_exec($ch);

    // Проверка ошибок
    if (curl_errno($ch)) {
		$this->sendResponse([], curl_error($ch), false, $httpCode);
    } else {
        // Получение HTTP-кода ответа
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200) {
            return $response;
        } else {
			$this->sendResponse($responce, "", false, $httpCode);
        }
    }

    // Закрытие cURL
    curl_close($ch);
	
	}
	
	
	public function getDataFromServiceHikvision($confServer, $startDate, $endDate, $searchResultPosition = 0 , $maxResult=100 ) {
    // URL для запроса
   // $url = "https://192.168.11.205/ISAPI/AccessControl/AcsEvent?format=json";
     $url = "https://{$confServer['host']}{$this->urlAPI}";

    // Логин и пароль для авторизации
    $username = $confServer['login'];
    $password = $confServer['password'];

    $searchID = md5($startDate.$endDate.$searchResultPosition.$maxResult);
    // Тело запроса
    $payload = [
        "AcsEventCond" => [
            "searchID" => $searchID, //"f007b2fbe9137a8fb3610c9f9ccb4ba3",
            "searchResultPosition" => $searchResultPosition,
            "maxResults" => $maxResult,
            "major" => 0,
            "minor" => 0,
            "startTime" => $this->formatDate($startDate),
            "endTime" => $this->formatDate($endDate),
            "timeReverseOrder" => true
        ]
    ];

    // Преобразуем тело запроса в JSON
    $jsonPayload = json_encode($payload);

    // Инициализация cURL
    $ch = curl_init();

    // Установка параметров cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключение проверки SSL (если требуется)
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Не проверять имя хоста

    // Включение Digest-авторизации
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

    // Выполнение запроса
    $response = curl_exec($ch);

    // Проверка ошибок
    if (curl_errno($ch)) {
		$this->sendResponse([], curl_error($ch), false, $httpCode);
    } else {
        // Получение HTTP-кода ответа
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200) {
            return $response;
        } else {
			$this->sendResponse($responce, "", false, $httpCode);
        }
    }

    // Закрытие cURL
    curl_close($ch);
}

public function getLoadAllUsers($serverConf, $maxResult = 24)
{
    $searchResultPosition = 0;
    $users = [];
    $totalMatches = 0;

    do {
        $innerData = $this->getUsersFromServiceHikvision($serverConf, $searchResultPosition, $maxResult);
        $data = json_decode($innerData, true);

        if (!isset($data['UserInfoSearch'])) {
            break;
        }
        //var_dump([$totalMatches,$numOfMatches, $searchResultPosition]);
        $UserInfoSearch = $data['UserInfoSearch'];
		if(!$totalMatches)
        $totalMatches = $UserInfoSearch['totalMatches']; // 2304
	    if(!$numOfMatches)
        $numOfMatches = $UserInfoSearch['numOfMatches']; // 24

        $users = array_merge($users, $this->parseUser($innerData));

        // Прекращаем, если получили все записи
        if ($searchResultPosition >= $totalMatches || $numOfMatches == 0) {
            break;
        }

        $searchResultPosition += $maxResult;
		//if($searchResultPosition > $totalMatches) $searchResultPosition = $totalMatches;

    } while (true);

    return $users;
}


public function getLoadAllData($serverConf,$startDate, $endDate , $maxResult = 20)
{
	 $searchResultPosition = 0;
	 $entries = [];
	   
	do {
		
		$innerData = $this->getDataFromServiceHikvision($serverConf, $startDate, $endDate, $searchResultPosition, $maxResult);
		
		$data = json_decode($innerData, true);
		
		// нужно проверить что не произошло ошибки
		
		$AcsEvent = $data['AcsEvent'];
		$totalMatches = $AcsEvent['totalMatches']; // 2304
		$numOfMatches = $AcsEvent['numOfMatches']; // 24

		// склеить в общии массив данных
		$entries = array_merge($entries , $this->parseData($innerData));
//var_dump(sizeof($entries));
		
		// результат проверки доступности записей
		$avaialableRecord = ($totalMatches - $searchResultPosition)>=0;
		
		if($avaialableRecord)
		{
			$searchResultPosition +=$maxResult;
		}
		
		// Повторять пока общее кол-во минус searchResultPosition - maxResult не будет равно нулю или меньше
	} while($avaialableRecord);

//var_dump(["total"=>$totalMatches, "result"=>$searchResultPosition]);

		
		return $entries;
}


private function groupEntriesByFio(array $data): array {
    $result = [];

    foreach ($data as $entry) {
        $fio = $entry['fio'] ?? 'Неизвестный';

        if (!isset($result[$fio])) {
            $result[$fio] = [
                'fio' => $fio,
                'entries' => []
            ];
        }

        // Добавляем только даты входа и выхода
        $result[$fio]['entries'][] = [
            'dateIn' => $entry['dateIn'] ?? '',
            'dateOut' => $entry['dateOut'] ?? ''
        ];
    }

    return array_values($result); // Перенумеровываем ключи
}


    public function mergeVisitsUser($in, $out, int $timePrecisionMinutes = 2)
    {
        // Вспомогательная функция для сглаживания временных меток
        $smoothTimestamps = function (array $timestamps, int $precisionSeconds) {
            if (empty($timestamps)) {
                return [];
            }
            sort($timestamps);
            $result = [];
            $currentGroupStart = $timestamps[0];

            foreach ($timestamps as $ts) {
                if ($ts - $currentGroupStart <= $precisionSeconds) {
                    continue; // пропускаем — в пределах окна
                } else {
                    $result[] = $currentGroupStart;
                    $currentGroupStart = $ts;
                }
            }
            $result[] = $currentGroupStart;
            return $result;
        };

        $data = [];

        // Обрабатываем заезды
        foreach ($in as $inItem) {
            $fio = $inItem['fio'] ?? 'Неизвестный';
            $code = $inItem['code'] ?? 'NO_CODE';
            $dateIn = $inItem['date'] ?? '';

            $ts = strtotime($dateIn);
            if ($ts === false || $ts <= 0) {
                continue;
            }

            if (!isset($data[$fio][$code])) {
                $data[$fio][$code] = ['in' => [], 'out' => []];
            }
            $data[$fio][$code]['in'][] = $ts;
        }

        // Обрабатываем выезды
        foreach ($out as $outItem) {
            $fio = $outItem['fio'] ?? 'Неизвестный';
            $code = $outItem['code'] ?? 'NO_CODE';
            $dateOut = $outItem['date'] ?? '';

            $ts = strtotime($dateOut);
            if ($ts === false || $ts <= 0) {
                continue;
            }

            if (!isset($data[$fio][$code])) {
                $data[$fio][$code] = ['in' => [], 'out' => []];
            }
            $data[$fio][$code]['out'][] = $ts;
        }

        $result = [];

        foreach ($data as $fio => $codes) {
            foreach ($codes as $code => $visits) {
                $inList  = $visits['in'];
                $outList = $visits['out'];

                // Применяем сглаживание, ТОЛЬКО если precision > 0
                if ($timePrecisionMinutes > 0) {
                    $precisionSeconds = $timePrecisionMinutes * 60;
                    $inList  = $smoothTimestamps($inList, $precisionSeconds);
                    $outList = $smoothTimestamps($outList, $precisionSeconds);
                } else {
                    // Без сглаживания — просто убираем дубли и сортируем, как раньше
                    $inList  = array_values(array_unique($inList));
                    $outList = array_values(array_unique($outList));
                    sort($inList);
                    sort($outList);
                }

                // Сопоставление входов и выходов
                foreach ($inList as $inTime) {
                    $matchedOut = null;
                    $bestDiff = PHP_INT_MAX;

                    foreach ($outList as $index => $outTime) {
                        if ($outTime >= $inTime && ($outTime - $inTime) <= 86400) {
                            $diff = $outTime - $inTime;
                            if ($diff < $bestDiff) {
                                $bestDiff = $diff;
                                $matchedOut = $index;
                            }
                        }
                    }

                    if ($matchedOut !== null) {
                        $outTime = $outList[$matchedOut];
                        unset($outList[$matchedOut]);
                        $outList = array_values($outList);

                        $result[] = [
                            'code'    => $code,
                            'fio'     => $fio,
                            'dateIn'  => date('Y-m-d H:i:s', $inTime),
                            'dateOut' => date('Y-m-d H:i:s', $outTime)
                        ];
                    } else {
                        $result[] = [
                            'code'    => $code,
                            'fio'     => $fio,
                            'dateIn'  => date('Y-m-d H:i:s', $inTime),
                            'dateOut' => null
                        ];
                    }
                }
            }
        }

        usort($result, function ($a, $b) {
            return strtotime($a['dateIn']) <=> strtotime($b['dateIn']);
        });

        return $result;
    }


    public function getVisits(string $start, string $end): array
    {
        $today = date('Y-m-d');
        if (date("Y-m-d",strtotime($start)) === $today) {
            // Берём напрямую с устройств (и НЕ перезаписываем БД автоматически, но можем сохранять)
            $fromDevice = $this->fetchFromDevice($date);
            if (!empty($fromDevice)) {
                // Сохраняем в БД (не нужен strict dedupe — храним "сырые" события)
                $this->saveVisitsToDB($fromDevice);
            }
            return $fromDevice;
        } else {
            // Для прошлых дат сначала ищем в БД
            $fromDb = $this->getVisitsFromDB($start, $end);
            if (!empty($fromDb)) {
                return $fromDb;
            }
            // Если в БД нет — пытаемся получить с устройств (и сохраняем):
            $fromDevice = $this->fetchFromDevice($date);
            if (!empty($fromDevice)) {
                $this->saveVisitsToDB($fromDevice);
            }
            return $fromDevice;
        }
    }

    /**
     * Анализирует посещения и возвращает отчёт по нарушениям.
     *
     * @param array $visits
     * @param string $startEntries — время начала (например, '08:00:00')
     * @param string $endExists — время окончания (например, '17:00:00')
     * @param int $graceMinutes — допустимое опоздание в минутах (по умолчанию 0)
     * @param string|null $dateFrom — дата начала отчёта (для заголовка, не влияет на логику)
     * @param string|null $dateTo — дата окончания отчёта
     * @return array
     */
    public function analyzeAttendance(
        array $visits,
        array $allUsers,
        string $startEntries = '08:00:00',
        string $endExists = '17:00:00',
        int $graceMinutes = 0,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        string $lunchStart = '13:00:00',   // ← новое
        string $lunchEnd = '14:00:00'      // ← новое
    ): array {
        // === 1. Группируем по пользователю и дню ===
        $grouped = [];
        foreach ($visits as $v) {
            $code = $v['code'] ?? '';
            $fio = $v['fio'] ?? '';
            $key = $code ?: $fio;
            if (!$key) continue;

            $dateIn = $v['dateIn'] ?? null;
            if (!$dateIn) continue;

            $dateStr = date('Y-m-d', strtotime($dateIn)); // день
            $grouped[$key][$dateStr][] = $v;
        }

        // === 2. Собираем visited (для never_came) ===
        $visitedCodes = [];
        $visitedFios = [];
        foreach ($visits as $v) {
            if (!empty($v['code'])) $visitedCodes[$v['code']] = true;
            if (!empty($v['fio'])) $visitedFios[$v['fio']] = true;
        }

        // === 3. Кто не приходил ===
        $neverCame = [];
        foreach ($allUsers as $user) {
            $code = $user['code'] ?? '';
            $fio = $user['fio'] ?? '';
            $hasVisit = (!empty($code) && isset($visitedCodes[$code])) ||
                (!empty($fio) && isset($visitedFios[$fio]));
            if (!$hasVisit) {
                $neverCame[] = [
                    'code' => $code,
                    'fio' => $fio,
                    'card' => $user['card'] ?? '',
                    'status' => $user['status'] ?? '',
                    'dateIn' => null,
                    'dateOut' => null,
                ];
            }
        }

        // === 4. Подготавливаем пороги ===
        $startDt = new \DateTime("1970-01-01 {$startEntries}");
        $startDt->add(new \DateInterval("PT{$graceMinutes}M"));
        $lateThreshold = $startDt->format('H:i:s');

        $lunchStartDt = new \DateTime("1970-01-01 {$lunchStart}");
        $lunchEndDt = new \DateTime("1970-01-01 {$lunchEnd}");
        $lunchEndDt->add(new \DateInterval("PT{$graceMinutes}M"));
        $lateAfterLunchThreshold = $lunchEndDt->format('H:i:s');

        // === 5. Инициализируем списки ===
        $late = $cameEarly = $leftEarly = $didNotLeave = [];
        $lateAfterLunch = [];    // ← опоздали после обеда
        $leftEarlyToLunch = [];  // ← ушли рано на обед

        // === 6. Анализируем по пользователю и дню ===
        foreach ($grouped as $userKey => $days) {
            foreach ($days as $date => $events) {
                // Сортируем события по времени входа
                usort($events, fn($a, $b) => strtotime($a['dateIn']) <=> strtotime($b['dateIn']));

                // Первое событие — приход на работу
                $first = $events[0];
                $timeIn1 = date('H:i:s', strtotime($first['dateIn']));

                // Анализ первого входа
                if ($timeIn1 > $lateThreshold) {
                    $late[] = $first;
                } elseif ($timeIn1 < $startEntries) {
                    $cameEarly[] = $first;
                }

                // Анализ первого выхода (на обед)
                $dateOut1 = $first['dateOut'] ?? null;
                if ($dateOut1 !== null) {
                    $timeOut1 = date('H:i:s', strtotime($dateOut1));
                    if ($timeOut1 < $lunchStart) {
                        $leftEarlyToLunch[] = $first; // ушёл до начала обеда
                    }
                } else {
                    $didNotLeave[] = $first;
                    continue; // не вышел — дальше не анализируем
                }

                // Второе событие (после обеда), если есть
                if (count($events) >= 2) {
                    $second = $events[1];
                    $timeIn2 = date('H:i:s', strtotime($second['dateIn']));
                    if ($timeIn2 > $lateAfterLunchThreshold) {
                        $lateAfterLunch[] = $second; // опоздал после обеда
                    }

                    // Анализ второго выхода (конец дня)
                    $dateOut2 = $second['dateOut'] ?? null;
                    if ($dateOut2 === null) {
                        $didNotLeave[] = $second;
                    } else {
                        $timeOut2 = date('H:i:s', strtotime($dateOut2));
                        if ($timeOut2 < $endExists) {
                            $leftEarly[] = $second;
                        }
                    }
                }
            }
        }

        return [
            'late'                 => $late,
            'came_early'           => $cameEarly,
            'left_early'           => $leftEarly,
            'did_not_leave'        => $didNotLeave,
            'never_came'           => $neverCame,
            'late_after_lunch'     => $lateAfterLunch,      // ← новое
            'left_early_to_lunch'  => $leftEarlyToLunch,    // ← новое
            'date_from'            => $dateFrom,
            'date_to'              => $dateTo,
        ];
    }



    private function fillNeverCameSheet(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        array $users,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): void {
        // Шапка
        $sheet->setCellValue('A1', 'Система СКУД');
        $dateRange = $dateFrom && $dateTo ? "Отчёт с {$dateFrom} по {$dateTo}" : 'Отчёт';
        $sheet->setCellValue('A2', $dateRange);
        $sheet->setCellValue('A3', 'Не приходили');
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');

        $sheet->getStyle('A1:D3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A3:D3')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFCCCC'], // светло-красный
            ],
        ]);

        // Заголовки
        $headers = ['№', 'ФИО', 'Код', 'Статус'];
        $colIndex = 1;
        foreach ($headers as $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++);
            $sheet->setCellValue($col . '4', $header);
        }

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9E1F2'],
            ],
        ];
        $sheet->getStyle('A4:D4')->applyFromArray($headerStyle);

        // Данные
        $row = 5;
        if (empty($users)) {
            $sheet->setCellValue('A5', 'Нет данных');
        } else {
            $num = 1;
            foreach ($users as $user) {
                $sheet->setCellValue("A{$row}", $num++);
                $sheet->setCellValue("B{$row}", $user['fio'] ?? '');
                $sheet->setCellValue("C{$row}", $user['code'] ?? '');
                $sheet->setCellValue("D{$row}", $user['status'] ?? '');
                $row++;
            }
        }

        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Загружает пользователей с устройств входа и выхода, объединяет и сохраняет в БД.
     *
     * @param int $timeout Таймаут запроса к устройствам (в секундах)
     * @return bool Успешно ли выполнено
     */
    public function syncUsersFromDevices(int $limitRowtoPage = 24): bool
    {
        // 1. Загружаем с устройств
        $innerUserData = $this->getLoadAllUsers($this->inServerConf, $limitRowtoPage);
        $outerUserData = $this->getLoadAllUsers($this->outServerConf, $limitRowtoPage);

        if ($innerUserData === null && $outerUserData === null) {
            error_log("Не удалось загрузить пользователей ни с одного устройства");
            return false;
        }

        // Обеспечиваем, что данные — массивы
        $innerUserData = is_array($innerUserData) ? $innerUserData : [];
        $outerUserData = is_array($outerUserData) ? $outerUserData : [];

        // 2. Индексируем по уникальному code
        $innerByCode = [];
        $outerByCode = [];

        foreach ($innerUserData as $user) {
            if (!empty($user['code'])) {
                $innerByCode[$user['code']] = $user;
            }
        }

        foreach ($outerUserData as $user) {
            if (!empty($user['code'])) {
                $outerByCode[$user['code']] = $user;
            }
        }

        // 3. Объединяем и проставляем статус
        $allCodes = array_unique(array_merge(array_keys($innerByCode), array_keys($outerByCode)));
        $mergedUsers = [];

        foreach ($allCodes as $code) {
            $inUser  = $innerByCode[$code] ?? null;
            $outUser = $outerByCode[$code] ?? null;

            // Берём данные из входа, если есть, иначе из выхода
            $baseUser = $inUser ?: $outUser;

            // Определяем статус
            if ($inUser && $outUser) {
                $status = 'зарегистирован на вход и выход';
            } elseif ($inUser) {
                $status = 'зарегистирован только на вход';
            } else {
                $status = 'зарегистирован только на выход';
            }

            // Формируем запись для сохранения
            $mergedUsers[] = [
                'code'      => $code,
                'fio'       => $baseUser['fio'] ?? null,
                'faceURL'   => $baseUser['faceURL'] ?? null,
                'groupId'   => $baseUser['groupId'] ?? null,
                'numOfCard' => $baseUser['numOfCard'] ?? 0,
                'gender'    => $baseUser['gender'] ?? null,
                'status'    => $status,
            ];
        }

        // 4. Сохраняем в БД
        return $this->saveUsers($mergedUsers);
    }


    /**
     * Сохраняет или обновляет несколько пользователей за один проход.
     *
     * @param array $users Массив пользователей. Каждый — ассоциативный массив с ключами: code, fio, card, photo, status
     * @return bool Успешно ли сохранено
     */
    /**
     * Сохраняет (INSERT/UPDATE) или помечает как удаленных (Soft Delete)
     * пользователей на основе списка, пришедшего с устройства.
     * * @param array $users Список пользователей с устройства.
     * @return bool Успех операции.
     */
    public function saveUsers(array $users): bool
    {
        if (empty($users)) {
            // Если пришел пустой список, мягко удаляем всех активных пользователей.
            return $this->softDeleteMissingUsers([]);
        }

        // Поля, которые будут записываться (должны совпадать со столбцами таблицы)
        $allowedFields = ['fio', 'card', 'photo', 'status', 'group_id', 'gender'];

        // 1. --- Подготовка данных для INSERT/UPDATE ---

        // Подготавливаем части SQL
        $columns = array_merge(['code'], $allowedFields);
        $columnList = '`' . implode('`, `', $columns) . '`';

        $updateParts = [];
        foreach ($allowedFields as $field) {
            $updateParts[] = "`{$field}` = VALUES(`{$field}`)";
        }
        // Также обязательно обновляем статус soft delete, если пользователь вернулся
        $updateClause = implode(', ', $updateParts) . ", `delete` = 0, `delete_at` = NULL";

        // Собираем значения для массовой вставки
        $placeholders = [];
        $params = [];
        $incomingCodes = []; // Список кодов, пришедших с устройства

        foreach ($users as $index => $user) {
            if (empty($user['code'])) {
                continue; // Пропускаем пользователей без кода
            }

            $code = $user['code'];
            $incomingCodes[] = $code; // Сохраняем код

            // Генерируем имена параметров: user0_code, user0_fio, ...
            $paramNames = [];
            foreach ($columns as $col) {
                $paramName = "user{$index}_{$col}";
                $paramNames[] = ":{$paramName}";

                // Сопоставление значений
                if ($col === 'code') {
                    $params[$paramName] = $code;
                } elseif ($col === 'card') {
                    // Предполагаем, что cardNum — это фактический номер карты
                    $cardValue = $user['cardNum'] ?? (($user['numOfCard'] ?? 0) > 0 ? '0' : null); // Логика требует уточнения источника card
                    $params[$paramName] = $cardValue;
                } elseif ($col === 'photo') {
                    $params[$paramName] = $user['faceURL'] ?? null;
                } elseif ($col === 'group_id') {
                    $params[$paramName] = $user['groupId'] ?? null;
                } elseif ($col === 'gender') {
                    $params[$paramName] = $user['gender'] ?? null;
                } else {
                    // fio, status
                    $params[$paramName] = $user[$col] ?? null;
                }
            }

            $placeholders[] = '(' . implode(', ', $paramNames) . ')';
        }

        // Если нет валидных данных для INSERT/UPDATE, но есть активные пользователи в БД,
        // переходим к удалению.
        if (empty($placeholders)) {
            return $this->softDeleteMissingUsers($incomingCodes);
        }

        // 2. --- Выполнение массовой вставки/обновления ---
        $sql = "
    INSERT INTO `{$this->usersTableName}` ({$columnList})
    VALUES " . implode(', ', $placeholders) . "
    ON DUPLICATE KEY UPDATE {$updateClause}
";

        try {
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt->execute($params)) {
                // Если INSERT/UPDATE не удался, завершаем работу
                return false;
            }
        } catch (PDOException $e) {
            error_log("SQL: " . $sql);
            error_log("Параметры: " . print_r($params, true));
            error_log("Ошибка сохранения/обновления: " . $e->getMessage());
            return false;
        }

        // 3. --- Мягкое удаление (Soft Delete) ---
        // Выполняем мягкое удаление для тех, кого нет в списке $incomingCodes
        return $this->softDeleteMissingUsers($incomingCodes);
    }

    /**
     * Выполняет мягкое удаление пользователей, отсутствующих в списке $activeCodes.
     * * @param array $activeCodes Коды пользователей, которые НЕ должны быть удалены.
     * @return bool Успех операции.
     */
    private function softDeleteMissingUsers(array $activeCodes): bool
    {
        // Получаем коды всех активных (delete=0) пользователей из БД
        $existingCodes = $this->getExistingUserCodes();

        // Вычисляем разницу: кого нужно удалить (есть в БД, но нет в $activeCodes)
        $codesToDelete = array_diff($existingCodes, $activeCodes);

        if (empty($codesToDelete)) {
            return true; // Удалять некого
        }

        // Создаем плейсхолдеры для IN-запроса (например: ?, ?, ?)
        $placeholders = implode(',', array_fill(0, count($codesToDelete), '?'));
        $deleteTime = date('Y-m-d H:i:s');

        $sql = "
        UPDATE `{$this->usersTableName}`
        SET `delete` = 1, `delete_at` = ?
        WHERE `code` IN ({$placeholders}) AND `delete` = 0
    ";

        try {
            $stmt = $this->pdo->prepare($sql);
            // Параметры для execute: дата удаления + все коды, которые нужно удалить
            $params = array_merge([$deleteTime], array_values($codesToDelete));

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Ошибка мягкого удаления: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Получает коды всех активных пользователей (delete=0) из базы данных.
     * * @return array Массив кодов (строк).
     */
    private function getExistingUserCodes(): array
    {
        try {
            $sql = "SELECT `code` FROM `{$this->usersTableName}` WHERE `delete` = 0";
            $stmt = $this->pdo->query($sql);
            // Возвращаем одномерный массив кодов
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Ошибка получения активных кодов пользователей: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Получает всех пользователей.
     *
     * @return array Массив пользователей (каждый — ассоциативный массив). В случае ошибки — пустой массив.
     */
    public function getUsers(): array
    {
        try {
            $sql = "SELECT `code`, `fio`, `card`, `photo`, `status` FROM `{$this->usersTableName}` ORDER BY `fio` ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Ошибка получения списка пользователей: " . $e->getMessage());
            return []; // никогда не возвращаем false — только массив
        }
    }

    /**
     * Загружает посещения с устройств входа и выхода за указанный период и сохраняет в БД.
     *
     * @param string $startDate Дата/время начала (формат: 'Y-m-d H:i:s')
     * @param string $endDate   Дата/время окончания (формат: 'Y-m-d H:i:s')
     * @param int $timeout Таймаут запроса к устройствам (в секундах)
     * @return bool Успешно ли выполнена синхронизация
     */
    public function syncVisitsFromDevices(string $startDate, string $endDate, int $timeout = 24): bool
    {
        // Загружаем данные с устройства входа
        $innerParseData = $this->getLoadAllData($this->inServerConf, $startDate, $endDate, $timeout);

        // Загружаем данные с устройства выхода
        $outerParseData = $this->getLoadAllData($this->outServerConf, $startDate, $endDate, $timeout);

        $success = true;

        // Сохраняем входы
        if ($innerParseData !== null) {
            $savedInner = $this->saveVisitsToDB($innerParseData, '192.168.11.205');
            if (!$savedInner) {
                error_log("Ошибка сохранения данных входа в БД");
                $success = false;
            }
        } else {
            error_log("Не удалось загрузить данные входа с устройства");
            $success = false;
        }

        // Сохраняем выходы
        if ($outerParseData !== null) {
            $savedOuter = $this->saveVisitsToDB($outerParseData, '192.168.11.206');
            if (!$savedOuter) {
                error_log("Ошибка сохранения данных выхода в БД");
                $success = false;
            }
        } else {
            error_log("Не удалось загрузить данные выхода с устройства");
            $success = false;
        }

        return $success;
    }

public function initTemplateForm()
{

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'export_excel') {

        $startDate = $_POST['dateIn'];
        $endDate = $_POST['dateOut'];
        $startDate = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

       // $all = $this->getVisits($startDate, $endDate);
        $in = $this->getVisitsFromDB($startDate, $endDate, '192.168.11.205');
        $out = $this->getVisitsFromDB($startDate, $endDate, '192.168.11.206');
        $all = $this->mergeVisitsUser($in, $out, $this->timePrecision);
        $allUsers = $this->getUsers();
        $analysis =   $this->analyzeAttendance($all,$allUsers, $this->startEntries, $this->endExists, $this->graceMinutes,$startDate, $endDate, $this->lunchStart, $this->lunchEnd);

        $this->exportToExcel(
            $all,
            $analysis['late'],
            $analysis['came_early'],
            $analysis['left_early'],
            $analysis['did_not_leave'],
            $analysis['never_came'],
            [],
            $analysis['late_after_lunch'],
            $analysis['left_early_to_lunch'],
            $startDate,
            $startDate,
            $endDate
        );
        exit;
    }
	
	if(isset($_REQUEST['novisits']))
	{
		$result = [];
		$innerUserData = $this->getLoadAllUsers($this->inServerConf, 24);
	    $outerUserData = $this->getLoadAllUsers($this->outServerConf , 24);
		$users = array_merge($innerUserData, $outerUserData);
		
		 $startDate = $_POST['dateIn'];
    $endDate = $_POST['dateOut'];
    // Добавляем время начала дня (00:00:00) к $startDate
$startDate = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));

// Добавляем время конца дня (23:59:59) к $endDate
$endDate = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

       $entries = [];
       $innerParseData = $this->getLoadAllData($this->inServerConf,$startDate, $endDate , 20);
	   $outerParseData = $this->getLoadAllData($this->outServerConf,$startDate, $endDate , 20);
	   
	   foreach($innerParseData as $inner)
	   {
		   
		   if(!in_array($inner['fio'], array_column( $users, 'fio')))
			   $result[] = $inner;
	   }
		
		
		$this->sendResponse($result, "данные получены");
		
	}
	else

        if (isset($_REQUEST['user'])) {
            // === 1. Получаем данные с устройств (или из файлов) ===
            // Раскомментируйте, когда API готово:
            // $innerUserData = $this->getLoadAllUsers($this->inServerConf, 20);
            // $outerUserData = $this->getLoadAllUsers($this->outServerConf, 20);

          $users = $this->getUsers();

          foreach ($users as &$user)
          {
              $user["numOfCard"] = 0;
              $user["numOfFace"] = 1;
          }

            // === 5. Возвращаем результат ===
            $this->sendResponse($users, "получение данных пользователя");
        } else
        if (isset($_REQUEST['allvisits'])) {
            $startDate = $_POST['dateIn'] ?? '';
            $endDate = $_POST['dateOut'] ?? '';

            if (empty($startDate) || empty($endDate)) {
                $this->sendResponse([], "Ошибка: не указаны даты");
                return;
            }

            // Нормализуем даты с временем
            $startDateTime = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
            $endDateTime = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

            // === Проверяем: есть ли уже данные в БД за этот период? ===
            $existingInner = $this->getVisitsFromDB($startDateTime, $endDateTime, '192.168.11.205');
            $existingOuter = $this->getVisitsFromDB($startDateTime, $endDateTime, '192.168.11.206');

            $innerParseData = $existingInner;
            $outerParseData = $existingOuter;

            // Если данных нет — загружаем с устройств
            if (empty($existingInner)) {
                $innerParseData = $this->getLoadAllData($this->inServerConf, $startDateTime, $endDateTime, 24);
                if (!empty($innerParseData)) {
                    $this->saveVisitsToDB($innerParseData, '192.168.11.205');
                }
            }

            if (empty($existingOuter)) {
                $outerParseData = $this->getLoadAllData($this->outServerConf, $startDateTime, $endDateTime, 24);
                if (!empty($outerParseData)) {
                    $this->saveVisitsToDB($outerParseData, '192.168.11.206');
                }
            }

            // Если после загрузки всё равно нет данных — можно вернуть пустой результат
            if (empty($innerParseData) && empty($outerParseData)) {
                $this->sendResponse([], "Нет данных за указанный период");
                return;
            }

            // Объединяем и группируем
            $data = $this->mergeVisitsUser($innerParseData, $outerParseData, $this->timePrecision);
            $data = $this->groupEntriesByFio($data);

            $this->sendResponse($data, "Данные получены");
        }
	else

    if(isset($_POST['load']))
    {
    	$isLoading = true; // Установите false, чтобы скрыть загрузку


    $startDate = $_POST['dateIn'];
    $endDate = $_POST['dateOut'];
    // Добавляем время начала дня (00:00:00) к $startDate
$startDate = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));

// Добавляем время конца дня (23:59:59) к $endDate
$endDate = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

       $entries = [];
       $innerParseData = $this->getLoadAllData($this->inServerConf,$startDate, $endDate , 24);
	   $outerParseData = $this->getLoadAllData($this->outServerConf,$startDate, $endDate , 24);
	   

     //   $innerData = $this->getDataFromServiceHikvision($this->inServerConf, $startDate, $endDate);
      //var_dump($innerData);
      //$entries = $this->parseData($innerData);
    $data = $this->processEntriesAndExits($innerParseData, $outerParseData); 



	/*$data =  [
        "d_20250313"=> [
            [
                "fio"=> "Гарбузов Максим Александрович",
                "dateIn"=> "13.03.2025 21:02:32",
                "dateOut"=> "13.03.2025 21:15:51",
                "lunchIn"=> "13.03.2025 21:15:51",
                "lunchOut"=> "13.03.2025 21:02:32",
                "code"=> 266,
                "statusIn"=> "Вошел раньше на 57 минут",
                "statusOut"=> "Ушел раньше на 19 час 44 минут",
                "statusLunchIn"=> "На обед пришел раньше на 44 минут",
                "statusLunchOut"=> "С обеда ушел раньше на 57 минут",
                "status"=> "<ul><li>Вход: Вошел раньше на 57 минут<\/li><li>Выход: Ушел раньше на 19 час 44 минут<\/li><li>Обед начало: На обед пришел раньше на 44 минут<\/li><li>Обед окончание: С обеда ушел раньше на 57 минут<\/li><\/ul>"
            ],
            [
                "fio"=> "Кукеев Кайрат Рашитович",
                "dateIn"=> "13.03.2025 17:44:02",
                "dateOut"=> "",
                "lunchIn"=> "",
                "lunchOut"=> "13.03.2025 17:44:02",
                "code"=> 184,
                "statusIn"=> "Вошел раньше на 15 минут",
                "statusOut"=> "Не вышел",
                "statusLunchIn"=> "Не был на обеде",
                "statusLunchOut"=> "С обеда ушел раньше на 15 минут",
                "status"=> "<ul><li>Вход: Вошел раньше на 15 минут<\/li><li>Выход: Не вышел<\/li><li>Обед начало: Не был на обеде<\/li><li>Обед окончание: С обеда ушел раньше на 15 минут<\/li><\/ul>"
            ],
            [
                "fio"=> "Сыздыкбаев Серик Альбосынович",
                "dateIn"=> "13.03.2025 17:43:54",
                "dateOut"=> "",
                "lunchIn"=> "",
                "lunchOut"=> "13.03.2025 17:43:54",
                "code"=> 189,
                "statusIn"=> "Вошел раньше на 16 минут",
                "statusOut"=> "Не вышел",
                "statusLunchIn"=> "Не был на обеде",
                "statusLunchOut"=> "С обеда ушел раньше на 16 минут",
                "status"=> "<ul><li>Вход: Вошел раньше на 16 минут<\/li><li>Выход: Не вышел<\/li><li>Обед начало: Не был на обеде<\/li><li>Обед окончание: С обеда ушел раньше на 16 минут<\/li><\/ul>"
            ]
        ]
];*/


     $data =  $this->calculateStartEndLunchTime($data);
	 
	 
	 if(isset($_REQUEST['reasons']))
	 {
		 $result = [];
		 $reasons = $_REQUEST['reasons'];
		 
		 
		 if(in_array('not_at_work', $reasons))
		 {
		$innerUserData = $this->getLoadAllUsers($this->inServerConf, 24);
	    //$outerUserData = $this->getLoadAllUsers($this->outServerConf , 20);
		$users =$innerUserData; //array_merge($innerUserData, $outerUserData);
		 }
		 
		 foreach($data as $keyDay => $day)
		 foreach($day as $key => $item)
		 {
			 if(!is_array($reasons)) throw new Exception("not in array filter reasons");
			 if(is_array($item['statuses']) && array_intersect($reasons, array_column($item['statuses'], 'code')))
				 $result[$keyDay][] = $item;
		 
		 }
		 
		  if(in_array('not_at_work', $reasons))
		 foreach($users as $user)
		 {
			 foreach($data as $keyDay => $day)
			 {
				 if(!in_array($user['fio'], array_column($day, 'fio')))
				 {
					$result[$keyDay][] = ["fio"=>$user['fio'], 'code'=>$user['code'], 'dateIn'=> '', 'dateOut'=> '','lunchIn'=> '', 'lunchOut'=>'', 'status'=> '', 'statuses'=> [] ]; 
				 }
			 }
			 
		 }
		 
		 
		 $data = $result;
	 }
	 
      $this->sendResponse($data, "данные получены");

    }
    include_once('SKYD_template2.php');
	//include_once('SKYD_template.php');
}
	
}

# Использование:
$handler = new SKYD($username, $password);

$data = [
    [
        "fio" => "Сыздыкбаев Серик Альбосынович",
        "dateIn" => "13.03.2025 17:43:54",
        "dateOut" => "",
        "lunchIn" => "",
        "lunchOut" => "13.03.2025 17:43:54",
        "code" => 189,
        "statusIn" => "Вошел раньше на 16 минут",
        "statusOut" => "Не вышел",
        "statusLunchIn" => "Не был на обеде",
        "statusLunchOut" => "С обеда ушел раньше на 16 минут",
        "status" => "<ul><li>Вход: Вошел раньше на 16 минут</li><li>Выход: Не вышел</li><li>Обед начало: Не был на обеде</li><li>Обед окончание: С обеда ушел раньше на 16 минут</li></ul>",
    ],
];

$key = 'FDGV-1MKFS-43FEDS';
if (isset($_GET['key']) && $_GET['key'] == $key && isset($_GET['code'])) {
    $code = $_GET['code'];
    $codes = explode(',', $code);
    
    // Исправляем оператор: isset($_GET['date']) ? $_GET['date'] : date('Y-m-d')
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    
	echo "<pre>";
    var_dump($handler->saveVisitsForClients($codes, $date));
	echo "</pre>";
}
else
$handler->initTemplateForm();

// Вызов функции
//$handler->generateWordDocument($data);


//$command = $_GET['action'] ?? 'none';

//switch($command):
//case 'data': $handler->run(); break;
//case 'form': $handler->initTemplateForm(); break;
//default: $handler->run();
//endswitch;

