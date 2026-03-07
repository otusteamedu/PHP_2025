<?php

namespace Controllers\Apartment;

use API;
use Apartment;
use Building;
use Counter;
use Device;
use Signal;

class MeasureTable {

    /**
     * @param int $userdataId Идентификатор пользователя
     * @param string $token Токен пользователя
     * @param int $showArchive Признак отвечающий за то, добавлять ли показания архивных счётчиков в ответ
     * @param int $group Признак группировки показаний по типам показаний
     *
     * @throws \ApiException
     */
    public static function get(int $userdataId, $token = '', int $showArchive = 0, int $group = 0): array {

        if (empty($userdataId)) {

            API::throwInvalidRequestParams();
        }

        $apartment = Apartment::getByUserId($userdataId);

        if (empty($apartment)) {

            API::throwInvalidRequestParams('Метод должен вызываться от имени жителя');
        }

        $deviceModelSignalName = [
            'counter_cold' => 'm3',
            'counter_hot' => 'm3',
            'svk-15-3-2-md' => 'm3',
            'counter-gas' => 'm3gas',
            'counter_heat' => 'gcal',
            'skaut-cold-water-counter' => 'm3',
            'skaut-hot-water-counter' => 'm3',
            'skaut-heat-counter' => 'gcal',
            'skaut-electricity-counter' => 'kvh-t1'
        ];

        $counters = Device::getCountersFromApartment($apartment['apartment_id'], true, $showArchive);
        $building = Building::getByApartmentId($apartment['apartment_id']);
        $isAutomated = hasBuildingAutomatedAccounting($building['building_id']);
        $isReportValuesForOverduedCounters = Building::getIsReportValuesForOverduedCounters($building['building_id'], $building['complex_id'], $isAutomated);

        $orderDesc = ['counter_cold', 'skaut-cold-water-counter', 'counter_hot', 'skaut-hot-water-counter', 'counter_heat', 'skaut-heat-counter'];

        foreach (array_reverse($orderDesc) as $item) {
            foreach ($counters as $key => $counter) {
                if ($counter['device_model_name'] == $item) {
                    unset($counters[$key]);
                    \array_unshift($counters, $counter);
                }
            }
        }

        $rows = [];
        foreach ($counters as $counter) {
            $signals = Signal::getByDeviceId($counter['device_id']);

            list($checkInterval, $lastCheckDate) = Counter::getCounterCheckDateAndInterval($counter['device_serialnumber'], $token);
            $nextCheckDate = Counter::getNextCheckDate($lastCheckDate, $checkInterval);
            $checkDeadlineStatus = Counter::getCheckDeadlineStatus($nextCheckDate);

            $row = [
                'device_id' => $counter['device_id'],
                'serialNumber' => Device::EMPTY_SERIAL_NUMBER,
                'counterStatus' => true,
                'deviceName' => $counter['device_model_name_rus'],
                'unitName'  => '',
                'title' => '',
                'signal_id' => '0',
                'currentValueCounter'  => '0',
                'lastDateCurrentValue' => '01.01.1970',
                'totalLastMonth' => '0',
                'model' => $counter['device_model_name'],
                'tags'  => get_tags('device', $counter['device_model_name']),
                'is_report_values_for_overdued_counters' => $isReportValuesForOverduedCounters,
                'check_deadline_status' => $checkDeadlineStatus,
                'last_check_date' => $lastCheckDate?->format('d.m.Y'),
                'next_check_date' => $nextCheckDate?->format('d.m.Y'),
                'notifications' => null,
            ];

            $notificationVerification = Counter::NOTIFICATION_TEXTS[$checkDeadlineStatus];
            if (!empty($notificationVerification)) {
                $row["notifications"]["verification"] =  (object)[
                    "title" => $notificationVerification['title'],
                    "description" => str_replace("%next_check_date%", $nextCheckDate?->format('d.m.Y'), $notificationVerification['description'])
                ];
            }

            $row['serialNumber'] = firstNotEmpty(
                $counter['device_original_serialnumber'],
                $counter['device_ext_guid'],
                $counter['ext_guid'],
                $counter['device_serialnumber']
            );

            $tmpRow = explode(':', $row['serialNumber']);
            if (count_adapter($tmpRow) == 2) {
                $row['serialNumber'] = $tmpRow[1];
            }
            elseif (count_adapter($tmpRow) == 3) {
                $row['serialNumber'] = $tmpRow[1];
            }

            $collectSignalData = static function ($signalName) use ($signals, $isAutomated) {
                $signalRow = [];

                if (!isset($signals[$signalName])) {

                    return false;
                }

                $signal = $signals[$signalName];
                $signalRow['unitName'] = Signal::getUnitName($signal['signal_name']);
                $signalRow['title'] = array_key_exists($signal['signal_label'], Signal::SIGNAL_DICT)
                    ? Signal::SIGNAL_DICT[$signal['signal_label']]
                    : ($signal['signal_custom_name'] ?: $signal['signal_label']);
                $signalRow['signal_id'] = $signal['signal_id'];
                $signalRow['type'] = [
                    'name'  => $signal['signal_name'],
                    'title' => ($signal['signal_custom_name'] != '') ? $signal['signal_custom_name'] : $signal['signal_label']
                ];
                $signalRow['currentValueCounter']  = (string)round($signal['signal_value_float'], 3);
                $signalRow['lastDateCurrentValue'] = date('d.m.Y', $signal['signal_update_dt'] > $signal['signal_update_dt_attempt']
                    ? $signal['signal_update_dt']
                    : $signal['signal_update_dt_attempt']);

                $currentDay = date('d');
                if (!$isAutomated) {
                    if ($currentDay < 25) {
                        $lastDay  = mktime(4, 0, 0, date('n', strtotime('-1 month', $signal['signal_update_dt'])), 25, date('Y', $signal['signal_update_dt']));
                        $firstDay = mktime(4, 0, 0, date('n', strtotime('-2 month', $signal['signal_update_dt'])), 25, date('Y', $signal['signal_update_dt']));
                    }
                    else {
                        $lastDay  = mktime(4, 0, 0, date('n', $signal['signal_update_dt']), 25, date('Y', $signal['signal_update_dt']));
                        $firstDay = mktime(4, 0, 0, date('n', strtotime('-1 month', $signal['signal_update_dt'])), 25, date('Y', $signal['signal_update_dt']));
                    }
                }
                else {
                    if ($currentDay == 1) {
                        $lastDay  = strtotime('last day of last month', $signal['signal_update_dt']);
                        $firstDay = strtotime('first day of last month', $signal['signal_update_dt']);
                    }
                    else {
                        $lastDay  = $signal['signal_update_dt'];
                        $firstDay = strtotime('first day of this month', $signal['signal_update_dt']);
                    }
                }

                $lastDaySignal = Signal::getByTime($signal['signal_id'], $lastDay);
                $firstDaySignal = Signal::getByTime($signal['signal_id'], $firstDay);

                $totalLastMonth = $lastDaySignal['signal_value'] - $firstDaySignal['signal_value'];
                if ($totalLastMonth < 0) {
                    $totalLastMonth = 0;
                }

                $signalRow['totalLastMonth'] = (string)round($totalLastMonth, 3);

                return $signalRow;
            };

            if ($counter['device_model_name'] === 'counter_electricity') {
                for ($i = 1; $i < 4; $i++) {
                    $signalData = $collectSignalData("kvh-t$i");
                    if ($signalData !== false) {
                        $rowData = array_merge($row, $signalData);
                        $rows[] = $rowData;
                    }
                }
            }
            else {
                $deviceNames = [
                    'counter_cold' => 'Холодная вода',
                    'counter_hot' => 'Горячая вода',
                    'counter_heat' => 'Тепло',
                    'counter-gas' => 'Газ',
                    'skaut-cold-water-counter' => 'Холодная вода',
                    'skaut-hot-water-counter' => 'Горячая вода',
                    'skaut-heat-counter' => 'Тепло',
                    'skaut-electricity-counter' => 'Электроэнергия'
                ];
                $signalName = $deviceModelSignalName[$counter['device_model_name']] ?? $counter['device_model_name'];
                $signalData = $collectSignalData($signalName);
                if ($signalData !== false) {
                    $rowData = array_merge($row, $signalData);
                    $rowData['title'] = $deviceNames[$counter['device_model_name']] ?? $counter['device_model_name_rus'];
                    $rowData['tags'] = get_tags('device', $counter['device_model_name']);

                    $rows[] = $rowData;
                }

                if($counter['device_model_name'] === 'counter_cold' and isset($signals['pvs_m3'])) {
                    $signalData = $collectSignalData('pvs_m3');
                    if ($signalData !== false) {
                        $rowData = array_merge($row, $signalData);
                        $rowData['title'] = $signalData['title'] ?? $counter['device_model_name_rus'];
                        $rowData['tags'] = get_tags('device', $counter['device_model_name']);

                        $rows[] = $rowData;
                    }
                }
            }
        }

        if ($group == 1) {
            $counterGroups = [
                'counter_water' => [ 'title' => 'Водоснабжение', 'items' => [] ],
                'counter_gas' => [ 'title' => 'Газоснабжение', 'items' => [] ],
                'counter_heat' => [ 'title' => 'Теплоснабжение', 'items' => [] ],
                'counter_electricity' => [ 'title' => 'Электроэнергия', 'items' => [] ],
                'counter_other' => [ 'title' => 'Другие', 'items' => [] ],
            ];

            foreach ($rows as $row) {
                switch ($row['model']) {
                    case 'counter_electricity':
                    case 'skaut-electricity-counter':
                        $counterGroups['counter_electricity']['items'][] = $row;
                        break;
                    case 'counter_cold':
                    case 'counter_hot':
                    case 'skaut-cold-water-counter':
                    case 'skaut-hot-water-counter':
                        $counterGroups['counter_water']['items'][] = $row;
                        break;
                    case 'skaut-heat-counter' :
                    case 'counter_heat':
                        $counterGroups['counter_heat']['items'][] = $row;
                        break;
                    case 'counter-gas':
                        $counterGroups['counter_gas']['items'][] = $row;
                        break;
                    default:
                        $counterGroups['counter_other']['items'][] = $row;
                }

                $resultData = array_values(array_filter($counterGroups, function($group) {
                    return count_adapter($group['items']) > 0;
                }));

            }
        }
        else {
            $resultData = $rows;
        }

        return $resultData;
    }

}