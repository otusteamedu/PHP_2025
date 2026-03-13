<?php

namespace Application\Service;

use Infrastructure\Model\Signal;

/**
 * Отвечает за получение и упорядочивание счетчиков
 */
class CounterProcessor {
    public function __construct(
        private string $token,
        private bool $isAutomated,
        private bool $reportValuesForOverduedCounters
    ) {}
    
    public function process(array $counters): array {
        $orderedCounters = CounterSorter::sort($counters);
        $rows = [];
        
        foreach ($orderedCounters as $counter) {
            $rows = array_merge(
                $rows, 
                $this->processCounter($counter)
            );
        }
        
        return $rows;
    }
    
    private function processCounter(array $counter): array {
        $baseRow = $this->createBaseRow($counter);
        $signals = Signal::getByDeviceId($counter['device_id']);
        
        $handler = CounterHandlerFactory::create($counter['device_model_name']);
        return $handler->handle($counter, $signals, $baseRow, $this->isAutomated);
    }
    
    private function createBaseRow(array $counter): array {
        $row = new CounterRowBuilder($counter, $this->token);
        return $row->build($this->reportValuesForOverduedCounters);
    }
}