<?php

declare(strict_types=1);

namespace Dkeruntu\OtusHomeWorkFiveCheckMail;

class CheckMail
{

    public function CheckEmpty ($sString) : array {

        if (empty(trim($sString))) {

            $arResult["bError"] = true;
            $arResult["bErrorDiscription"] = "Ввод пустой";

        }else{
            $arResult["bError"] = false;
        }

        return $arResult;
        
    }

    public function ChekcStreln($sString): array {

        $iStreln =  mb_strlen($sString);

        if ($iStreln >= 254){

            $arResult["bError"] = true;
            $arResult["bErrorDiscription"] = "Длинна почты больше допустимой: " . $iStreln;

        }else{

            $arResult["bError"] = false;

        }

        return $arResult;
    }

    public function CheckDogCount ($sString) : array {


        $iDogCount =  mb_substr_count($sString,"@");

        if ($iDogCount === 0){

            $arResult["bError"] = true;
            $arResult["bErrorDiscription"] = "Нет собак";

        }elseif($iDogCount > 1){

            $arResult["bError"] = true;
            $arResult["bErrorDiscription"] = "Cобак больше чем нужно: " . $iDogCount;

        }else{
            $arResult["bError"] = false;
        }

        return $arResult;
    }

    public function ChekcValid($sString): array {

        if (!filter_var($sString, FILTER_VALIDATE_EMAIL)) {
            
            $arResult["bError"] = true;
            $arResult["bErrorDiscription"] = "Ввод не соответсвует формату email";

        } else {

            $arResult["bError"] = false;

        }

        return $arResult;
    }


    public function CheckDomain($sString): array {

        $sDomen = explode('@', $sString)[1]; // Извлекаем домен

        if (!checkdnsrr($sDomen, 'A')) {
            $arResult["bError"] = true;
            $arResult["bErrorDiscription"] = "Домена не существует";
        } else {
            $arResult["bError"] = false;
        }
        return $arResult;
    }


    public function CheckMX($sString): array {

        $sDomen = explode('@', $sString)[1]; // Извлекаем домен

        if (!checkdnsrr($sDomen, 'MX')) {
            $arResult["bError"] = true;
            $arResult["bErrorDiscription"] = "На домене отсутвует почтовая запись";
        } else {
            $arResult["bError"] = false;
        }
        return $arResult;
    }


    public function Check($sString): array {

            $arResult["bError"] = false;

        return $arResult;
    }

}


