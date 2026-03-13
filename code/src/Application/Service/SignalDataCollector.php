<?php

namespace Application\Service;

use Infrastructure\Model\Signal;

class SignalDataCollector {
    public static function collect(array $signals, bool $isAutomated, string $signalName): ?array {
        if (!isset($signals[$signalName])) {
            return null;
        }
        
        $signal = $signals[$signalName];
        
        return [
            'unitName' => Signal::getUnitName($signal['signal_name']),
            'title' => self::getSignalTitle($signal),
            'signal_id' => $signal['signal_id'],
            'type' => [
                'name' => $signal['signal_name'],
                'title' => $signal['signal_custom_name'] ?: $signal['signal_label']
            ],
            'currentValueCounter' => (string)round($signal['signal_value_float'], 3),
            'lastDateCurrentValue' => self::formatLastDate($signal),
            'totalLastMonth' => self::calculateMonthlyTotal($signal, $isAutomated),
        ];
    }
    
    private static function getSignalTitle(array $signal): string {
        if (isset(Signal::SIGNAL_DICT[$signal['signal_label']])) {
            return Signal::SIGNAL_DICT[$signal['signal_label']];
        }
        
        return $signal['signal_custom_name'] ?: $signal['signal_label'];
    }
    
    private static function formatLastDate(array $signal): string {
        $timestamp = max($signal['signal_update_dt'], $signal['signal_update_dt_attempt']);
        return date('d.m.Y', $timestamp);
    }
    
    private static function calculateMonthlyTotal(array $signal, bool $isAutomated): string {
        [$startDate, $endDate] = self::getMonthlyDateRange($signal['signal_update_dt'], $isAutomated);
        
        $endSignal = Signal::getByTime($signal['signal_id'], $endDate);
        $startSignal = Signal::getByTime($signal['signal_id'], $startDate);
        
        $total = $endSignal['signal_value'] - $startSignal['signal_value'];
        $total = max(0, $total);
        
        return (string)round($total, 3);
    }
    
    private static function getMonthlyDateRange(int $timestamp, bool $isAutomated): array {
        $currentDay = (int)date('d', $timestamp);
        
        if (!$isAutomated) {
            if ($currentDay < 25) {
                $endDate = strtotime(date('Y-m-25 04:00:00', strtotime('-1 month', $timestamp)));
                $startDate = strtotime(date('Y-m-25 04:00:00', strtotime('-2 month', $timestamp)));
            } else {
                $endDate = strtotime(date('Y-m-25 04:00:00', $timestamp));
                $startDate = strtotime(date('Y-m-25 04:00:00', strtotime('-1 month', $timestamp)));
            }
        } else {
            if ($currentDay == 1) {
                $endDate = strtotime('last day of last month', $timestamp);
                $startDate = strtotime('first day of last month', $timestamp);
            } else {
                $endDate = $timestamp;
                $startDate = strtotime('first day of this month', $timestamp);
            }
        }
        
        return [$startDate, $endDate];
    }
}