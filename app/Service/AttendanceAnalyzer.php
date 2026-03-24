<?php

declare(strict_types=1);

namespace App\Service;

use App\Config\AppConfig;
use DateTime;
use DateTimeZone;

final class AttendanceAnalyzer
{
    private DateTimeZone $timezone;

    public function __construct(private readonly AppConfig $config)
    {
        $this->timezone = new DateTimeZone($config->timezone);
    }

    /**
     * Главный метод анализа
     */
    public function analyze(array $mergedVisits, array $allUsers): array
    {
        $groupedByDay = $this->groupVisitsByUserAndDay($mergedVisits);
        $visitedCodes = $this->collectVisitedCodes($mergedVisits);

        $result = [
            'late'                => [],
            'came_early'          => [],
            'left_early'          => [],
            'did_not_leave'       => [],
            'never_came'          => [],
            'late_after_lunch'    => [],
            'left_early_to_lunch' => [],
            'all_with_status'     => [],   // для Word/Excel отчётов
        ];

        // 1. Анализ тех, кто приходил
        foreach ($groupedByDay as $userKey => $days) {
            foreach ($days as $date => $events) {
                $entry = $this->analyzeOneDay($events, $date);
                $result['all_with_status'][$userKey][$date] = $entry;

                if ($entry['is_late']) {
                    $result['late'][] = $entry;
                }
                if ($entry['is_came_early']) {
                    $result['came_early'][] = $entry;
                }
                if ($entry['is_left_early']) {
                    $result['left_early'][] = $entry;
                }
                if ($entry['did_not_leave']) {
                    $result['did_not_leave'][] = $entry;
                }
                if ($entry['is_late_after_lunch']) {
                    $result['late_after_lunch'][] = $entry;
                }
                if ($entry['left_early_to_lunch']) {
                    $result['left_early_to_lunch'][] = $entry;
                }
            }
        }

        // 2. Кто вообще не приходил
        $result['never_came'] = $this->findNeverCameUsers($allUsers, $visitedCodes);

        return $result;
    }

    private function groupVisitsByUserAndDay(array $visits): array
    {
        $grouped = [];
        foreach ($visits as $v) {
            $key = $v['code'] ?: $v['fio'];
            $day = (new DateTime($v['dateIn'], $this->timezone))->format('Y-m-d');

            $grouped[$key][$day][] = [
                'dateIn'  => $v['dateIn'],
                'dateOut' => $v['dateOut'] ?? null,
            ];
        }
        return $grouped;
    }

    private function collectVisitedCodes(array $visits): array
    {
        $codes = [];
        foreach ($visits as $v) {
            if (!empty($v['code'])) {
                $codes[$v['code']] = true;
            }
        }
        return $codes;
    }

    /**
     * Анализирует один день одного человека
     */
    private function analyzeOneDay(array $events, string $date): array
    {
        // Сортируем по времени
        usort($events, fn($a, $b) => strtotime($a['dateIn']) <=> strtotime($b['dateIn']));

        $first = $events[0];
        $second = $events[1] ?? null;

        $timeIn = new DateTime($first['dateIn'], $this->timezone);
        $workStart = new DateTime($this->config->workStart, $this->timezone);
        $workStart->setDate((int)$timeIn->format('Y'), (int)$timeIn->format('m'), (int)$timeIn->format('d'));

        $lunchStart = new DateTime($this->config->lunchStart, $this->timezone);
        $lunchStart->setDate((int)$timeIn->format('Y'), (int)$timeIn->format('m'), (int)$timeIn->format('d'));

        // === Утренний вход ===
        $isLate = $timeIn > $workStart;
        $isCameEarly = $timeIn < $workStart;

        // === Обед и выход ===
        $isLeftEarlyToLunch = false;
        $isLateAfterLunch = false;
        $didNotLeave = false;

        if ($first['dateOut']) {
            $timeOut = new DateTime($first['dateOut'], $this->timezone);
            if ($timeOut < $lunchStart) {
                $isLeftEarlyToLunch = true;
            }
        } else {
            $didNotLeave = true;
        }

        if ($second && $second['dateIn']) {
            $timeIn2 = new DateTime($second['dateIn'], $this->timezone);
            $lunchEnd = new DateTime($this->config->lunchEnd, $this->timezone);
            $lunchEnd->setDate((int)$timeIn->format('Y'), (int)$timeIn->format('m'), (int)$timeIn->format('d'));

            if ($timeIn2 > $lunchEnd->modify("+{$this->config->graceMinutes} minutes")) {
                $isLateAfterLunch = true;
            }
        }

        return [
            'fio'                => $first['fio'] ?? '',
            'code'               => $first['code'] ?? '',
            'date'               => $date,
            'dateIn'             => $first['dateIn'],
            'dateOut'            => $first['dateOut'] ?? null,
            'is_late'            => $isLate,
            'is_came_early'      => $isCameEarly,
            'is_left_early'      => $second && $second['dateOut'] && new DateTime($second['dateOut']) < new DateTime($this->config->workEnd),
            'did_not_leave'      => $didNotLeave,
            'is_late_after_lunch'=> $isLateAfterLunch,
            'left_early_to_lunch'=> $isLeftEarlyToLunch,
            'status_html'        => $this->buildStatusHtml($first, $second),
        ];
    }

    private function buildStatusHtml(array $first, ?array $second): string
    {
        $html = '<ul>';
        $html .= '<li>Вход: ' . ($first['statusIn'] ?? 'Вовремя') . '</li>';
        $html .= '<li>Выход: ' . ($first['statusOut'] ?? 'Не вышел') . '</li>';
        if ($second) {
            $html .= '<li>Обед: ' . ($second['statusLunch'] ?? '—') . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    private function findNeverCameUsers(array $allUsers, array $visitedCodes): array
    {
        $never = [];
        foreach ($allUsers as $user) {
            $code = $user['code'] ?? '';
            if ($code && !isset($visitedCodes[$code])) {
                $never[] = $user;
            }
        }
        return $never;
    }
}