<?php

namespace Infrastructure\Model;

class Building {

    public static function getByApartmentId(int $apartmentId) : array {
        // код метода
        return [];
    }

    public static function getIsReportValuesForOverduedCounters(int $buildingId, int $complexIdd, bool $isAutomated): bool {
        // код метода
        return true;
    }

    public static function hasBuildingAutomatedAccounting(int $buildingId): bool {
        // код метода
        return true;
    }
}