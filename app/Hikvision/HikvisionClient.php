<?php

declare(strict_types=1);

namespace App\Hikvision;

use App\Config\ServerConfig;
use Exception;

final class HikvisionClient
{
    private const MAX_RESULTS = 100;

    /**
     * Универсальный метод для отправки POST-запроса к Hikvision
     */
    private function sendRequest(string $url, array $payload, ServerConfig $config): string
    {
        $jsonPayload = json_encode($payload, JSON_THROW_ON_ERROR);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonPayload),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Digest-авторизация
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, $config->getAuth());

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: " . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Hikvision API error. HTTP code: {$httpCode}");
        }

        return $response;
    }

    /**
     * Получить пользователей с устройства
     */
    public function getUsers(ServerConfig $config, int $searchResultPosition = 0, int $maxResults = self::MAX_RESULTS): array
    {
        $url = $config->getFullUserUrl();

        $payload = [
            "UserInfoSearchCond" => [
                "searchID" => md5($config->host . $searchResultPosition . $maxResults),
                "searchResultPosition" => $searchResultPosition,
                "maxResults" => $maxResults,
            ]
        ];

        $response = $this->sendRequest($url, $payload, $config);
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['UserInfoSearch']['UserInfo'])) {
            return [];
        }

        return $this->parseUsers($data['UserInfoSearch']['UserInfo']);
    }

    /**
     * Получить события (входы/выходы) за период
     */
    public function getEvents(
        ServerConfig $config,
        string $startDate,
        string $endDate,
        int $searchResultPosition = 0,
        int $maxResults = self::MAX_RESULTS
    ): array {
        $url = $config->getFullEventUrl();

        $payload = [
            "AcsEventCond" => [
                "searchID" => md5($startDate . $endDate . $searchResultPosition),
                "searchResultPosition" => $searchResultPosition,
                "maxResults" => $maxResults,
                "major" => 0,
                "minor" => 0,
                "startTime" => $this->formatDate($startDate),
                "endTime" => $this->formatDate($endDate),
                "timeReverseOrder" => true
            ]
        ];

        $response = $this->sendRequest($url, $payload, $config);
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['AcsEvent']['InfoList'])) {
            return [];
        }

        return $this->parseEvents($data['AcsEvent']['InfoList']);
    }

    /**
     * Загрузить ВСЕ пользователей (с пагинацией)
     */
    public function getAllUsers(ServerConfig $config): array
    {
        $allUsers = [];
        $position = 0;

        do {
            $users = $this->getUsers($config, $position);
            $allUsers = array_merge($allUsers, $users);

            if (count($users) < self::MAX_RESULTS) {
                break;
            }

            $position += self::MAX_RESULTS;
        } while (true);

        return $allUsers;
    }

    /**
     * Загрузить ВСЕ события за период (с пагинацией)
     */
    public function getAllEvents(ServerConfig $config, string $startDate, string $endDate): array
    {
        $allEvents = [];
        $position = 0;

        do {
            $events = $this->getEvents($config, $startDate, $endDate, $position);
            $allEvents = array_merge($allEvents, $events);

            if (count($events) < self::MAX_RESULTS) {
                break;
            }

            $position += self::MAX_RESULTS;
        } while (true);

        return $allEvents;
    }

    // ====================== Парсеры ======================

    private function parseUsers(array $userList): array
    {
        $result = [];

        foreach ($userList as $item) {
            if (empty($item['employeeNo'])) {
                continue;
            }

            $result[] = [
                'code'     => (string)$item['employeeNo'],
                'fio'      => trim($item['name'] ?? ''),
                'gender'   => $item['gender'] ?? null,
                'groupId'  => $item['groupId'] ?? null,
                'faceURL'  => $item['faceURL'] ?? null,
                'numOfCard'=> $item['numOfCard'] ?? 0,
            ];
        }

        return $result;
    }

    private function parseEvents(array $eventList): array
    {
        $result = [];

        foreach ($eventList as $item) {
            if (empty($item['employeeNoString'])) {
                continue;
            }

            $result[] = [
                'date'    => $this->convertIsoToReadableDate($item['time']),
                'fio'     => trim($item['name'] ?? ''),
                'verify'  => $item['currentVerifyMode'] ?? '',
                'code'    => (string)$item['employeeNoString'],
            ];
        }

        return $result;
    }

    private function formatDate(string $date): string
    {
        $dt = new \DateTime($date, new \DateTimeZone('Asia/Aqtau'));
        return $dt->format('Y-m-d\TH:i:sP');
    }

    private function convertIsoToReadableDate(string $isoDate): string
    {
        try {
            return (new \DateTime($isoDate))->format('d.m.Y H:i:s');
        } catch (\Exception) {
            return 'Некорректная дата';
        }
    }
}