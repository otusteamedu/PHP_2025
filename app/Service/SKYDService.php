<?php

declare(strict_types=1);

namespace App\Service;

use App\Config\AppConfig;
use App\Config\ServerConfig;
use App\Hikvision\HikvisionClient;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;

final class SKYDService
{
    public function __construct(
        private readonly HikvisionClient $client,
        private readonly UserRepository $userRepo,
        private readonly VisitRepository $visitRepo,
        private readonly AttendanceAnalyzer $analyzer,
        private readonly AppConfig $appConfig
    ) {}

    /**
     * Синхронизировать всех пользователей с устройств
     */
    public function syncUsers(): array
    {
        $devices = require __DIR__ . '/../Config/devices.php'; // или через контейнер

        $inDevice  = $devices['in'];
        $outDevice = $devices['out'];

        $usersIn  = $this->client->getAllUsers($inDevice);
        $usersOut = $this->client->getAllUsers($outDevice);

        // Объединяем и сохраняем
        $allUsers = array_merge($usersIn, $usersOut);
        $this->userRepo->saveUsers($allUsers);

        // Мягкое удаление тех, кого больше нет на устройствах
        $activeCodes = array_column($allUsers, 'code');
        $this->userRepo->softDeleteMissingUsers($activeCodes);

        return $allUsers;
    }

    /**
     * Синхронизировать посещения за период
     */
    public function syncVisits(string $startDate, string $endDate): array
    {
        $devices = require __DIR__ . '/../Config/devices.php';

        $inDevice  = $devices['in'];
        $outDevice = $devices['out'];

        // Загружаем сырые события
        $rawIn  = $this->client->getAllEvents($inDevice, $startDate, $endDate);
        $rawOut = $this->client->getAllEvents($outDevice, $startDate, $endDate);

        // Сохраняем в БД
        $this->visitRepo->saveVisits($rawIn,  'Вход');
        $this->visitRepo->saveVisits($rawOut, 'Выход');

        // Объединяем входы и выходы
        $merged = $this->mergeVisits($rawIn, $rawOut);

        return $merged;
    }

    /**
     * Получить готовый отчёт (анализ)
     */
    public function getReport(string $startDate, string $endDate): array
    {
        $merged = $this->syncVisits($startDate, $endDate);
        $users  = $this->userRepo->getAllUsers();

        return $this->analyzer->analyze($merged, $users);
    }

    /**
     * Упрощённая версия mergeVisitsUser
     */
    private function mergeVisits(array $rawIn, array $rawOut): array
    {
        $result = [];

        // Группируем по code + день
        $data = [];

        foreach ($rawIn as $item) {
            $code = $item['code'];
            $day  = substr($item['date'], 0, 10);
            $data[$code][$day]['in'][] = strtotime($item['date']);
        }

        foreach ($rawOut as $item) {
            $code = $item['code'];
            $day  = substr($item['date'], 0, 10);
            $data[$code][$day]['out'][] = strtotime($item['date']);
        }

        foreach ($data as $code => $days) {
            foreach ($days as $day => $times) {
                $inTimes  = $times['in']  ?? [];
                $outTimes = $times['out'] ?? [];

                sort($inTimes);
                sort($outTimes);

                foreach ($inTimes as $inTime) {
                    $matchedOut = null;
                    foreach ($outTimes as $idx => $outTime) {
                        if ($outTime >= $inTime) {
                            $matchedOut = $outTime;
                            unset($outTimes[$idx]);
                            break;
                        }
                    }

                    $result[] = [
                        'code'    => $code,
                        'fio'     => '', // заполнится в analyze
                        'dateIn'  => date('Y-m-d H:i:s', $inTime),
                        'dateOut' => $matchedOut ? date('Y-m-d H:i:s', $matchedOut) : null,
                    ];
                }
            }
        }

        return $result;
    }
}