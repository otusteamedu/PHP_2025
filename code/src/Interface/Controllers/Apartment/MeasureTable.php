<?php

namespace Interface\Controllers\Apartment;

use Shared\Infrastructure\API;
use Infrastructure\Model\Apartment;
use Infrastructure\Model\Building;
use Application\Service\CounterGrouper;
use Application\Service\CounterProcessor;
use Infrastructure\Model\Device;

class MeasureTable {
    private ?array $apartment = null;
    
    public function __construct(
        private int $userdata_id, 
        private string $token = '', 
        private int $show_archive = 0, 
        private int $group = 0
    ) {}
    
    public function validate(): void {
        if (empty($this->userdata_id)) {
            API::throwInvalidRequestParams();
        }
        
        $this->apartment = Apartment::getByUserId($this->userdata_id);
        
        if (empty($this->apartment)) {
            API::throwInvalidRequestParams('Метод должен вызываться от имени жителя');
        }
    }
    
    public function get(): array {
        $this->ensureValidated();
        
        $counters = $this->getCounters();
        $buildingData = $this->getBuildingData();
        
        $counterProcessor = new CounterProcessor(
            $this->token,
            $buildingData['is_automated'],
            $buildingData['is_report_values_for_overdued_counters']
        );
        
        $rows = $counterProcessor->process($counters);
        
        return $this->group ? $this->groupRows($rows) : $rows;
    }
    
    private function ensureValidated(): void {
        if ($this->apartment === null) {
            throw new \RuntimeException('Must call validate() before get()');
        }
    }
    
    private function getCounters(): array {
        return Device::getCountersFromApartment(
            $this->apartment['apartment_id'], 
            true, 
            $this->show_archive
        );
    }
    
    private function getBuildingData(): array {
        $building = Building::getByApartmentId($this->apartment['apartment_id']);
        $is_automated = Building::hasBuildingAutomatedAccounting($building['building_id']);
        
        return [
            'building' => $building,
            'is_automated' => $is_automated,
            'is_report_values_for_overdued_counters' => Building::getIsReportValuesForOverduedCounters(
                $building['building_id'], 
                $building['complex_id'], 
                $is_automated
            )
        ];
    }
    
    private function groupRows(array $rows): array {
        return (new CounterGrouper())->group($rows);
    }
}