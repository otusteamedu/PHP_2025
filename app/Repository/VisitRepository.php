<?php

declare(strict_types=1);

namespace App\Repository;

use App\Config\DatabaseConfig;
use PDO;

final class VisitRepository
{
    private PDO $pdo;

    public function __construct(DatabaseConfig $config)
    {
        $this->pdo = new PDO(
            $config->getDsn(),
            $config->username,
            $config->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public function saveVisits(array $visits, string $deviceName): void
    {
        if (empty($visits)) {
            return;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO skyd_visits 
            (user_id, user_name, event_type, event_time, device_name)
            VALUES (:user_id, :user_name, :event_type, :event_time, :device_name)
        ");

        foreach ($visits as $visit) {
            $eventTime = $visit['date'] ?? null;
            if (!$eventTime) {
                continue;
            }

            // Проверка на дубликат
            $check = $this->pdo->prepare("
                SELECT COUNT(*) FROM skyd_visits 
                WHERE user_id = :user_id 
                  AND event_time = :event_time 
                  AND device_name = :device_name
            ");
            $check->execute([
                ':user_id' => $visit['code'],
                ':event_time' => $eventTime,
                ':device_name' => $deviceName
            ]);

            if ($check->fetchColumn() > 0) {
                continue;
            }

            $stmt->execute([
                ':user_id'    => $visit['code'],
                ':user_name'  => $visit['fio'] ?? null,
                ':event_type' => $visit['verify'] ?? null,
                ':event_time' => $eventTime,
                ':device_name'=> $deviceName
            ]);
        }
    }

    public function getVisitsByDevice(string $deviceName, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT user_id as code, user_name as fio, event_type as verify, 
                   event_time as date, device_name as device
            FROM skyd_visits 
            WHERE device_name = :device 
              AND event_time BETWEEN :start AND :end
            ORDER BY event_time ASC
        ");

        $stmt->execute([
            ':device' => $deviceName,
            ':start'  => $startDate,
            ':end'    => $endDate
        ]);

        return $stmt->fetchAll();
    }
}