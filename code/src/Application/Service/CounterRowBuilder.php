<?php

namespace Application\Service;

use Infrastructure\Model\Counter;
use Infrastructure\Model\Tags;
use Shared\Infrastructure\ValueHelper;
use DateTimeInterface;

/** 
 * Строит строки для каждого счетчика 
 */
class CounterRowBuilder {
    public function __construct(
        private array $counter,
        private string $token
    ) {}
    
    public function build(bool $reportValuesForOverduedCounters): array {
        $verificationInfo = $this->getVerificationInfo();
        
        $row = [
            'device_id' => $this->counter['device_id'],
            'serialNumber' => $this->extractSerialNumber(),
            'counterStatus' => true,
            'deviceName' => $this->counter['device_model_name_rus'],
            'unitName' => '',
            'title' => '',
            'signal_id' => '0',
            'currentValueCounter' => '0',
            'lastDateCurrentValue' => '01.01.1970',
            'totalLastMonth' => '0',
            'model' => $this->counter['device_model_name'],
            'tags' => Tags::getTags('device', $this->counter['device_model_name']),
            'is_report_values_for_overdued_counters' => $reportValuesForOverduedCounters,
            'check_deadline_status' => $verificationInfo['deadline_status'],
            'last_check_date' => $verificationInfo['last_check_date']?->format('d.m.Y'),
            'next_check_date' => $verificationInfo['next_check_date']?->format('d.m.Y'),
            'notifications' => $verificationInfo['notification'],
        ];
        
        return $row;
    }
    
    private function getVerificationInfo(): array {
        [$checkInterval, $lastCheckDate] = Counter::getCounterCheckDateAndInterval(
            $this->counter['device_serialnumber'], 
            $this->token
        );
        
        $nextCheckDate = Counter::getNextCheckDate($lastCheckDate, $checkInterval);
        $deadlineStatus = Counter::getCheckDeadlineStatus($nextCheckDate);
        
        return [
            'last_check_date' => $lastCheckDate,
            'next_check_date' => $nextCheckDate,
            'deadline_status' => $deadlineStatus,
            'notification' => $this->createNotification($deadlineStatus, $nextCheckDate),
        ];
    }
    
    private function createNotification(string $deadlineStatus, ?DateTimeInterface $nextCheckDate): ?array {
        $notification = Counter::NOTIFICATION_TEXTS[$deadlineStatus] ?? null;
        
        if (!$notification) {
            return null;
        }
        
        return [
            'verification' => (object)[
                'title' => $notification['title'],
                'description' => str_replace(
                    '%next_check_date%', 
                    $nextCheckDate?->format('d.m.Y'), 
                    $notification['description']
                )
            ]
        ];
    }
    
    private function extractSerialNumber(): string {
        $serialNumber = ValueHelper::firstNotEmpty(
            $this->counter['device_original_serialnumber'],
            $this->counter['device_ext_guid'],
            $this->counter['ext_guid'],
            $this->counter['device_serialnumber']
        );
        
        return $this->normalizeSerialNumber($serialNumber);
    }
    
    private function normalizeSerialNumber(string $serial): string {
        $parts = explode(':', $serial);
        
        if (count($parts) >= 2) {
            return $parts[1];
        }
        
        return $serial;
    }
}