<?php

declare(strict_types=1);

namespace Dkeruntu\OtusHomeWorkFourCheckBracket;

class CheckBracket
{

    public function ChekcParity(string $sString) : array
    {
        $arResult["bError"] = false;
        $arResult["bErrorDescription"] = "";


        $iSmileQuantity =  mb_substr_count($sString,")");
        $iSadnessQuantity =  mb_substr_count($sString,"(");

        if($iSmileQuantity === $iSadnessQuantity){

            $arResult["bError"] = false;
            $arResult["iCharQuantity"] = $iSmileQuantity;


        }else{
            $arResult["bError"] = true;
            $arResult["bErrorDescription"] = "Колличество '(' и ')' разное";
        }

        return $arResult;

    }

    public function CheckValidation(string $sString, int $iCharQuantity) : array {

        $arResult["bError"] = false;

        for ($iForPosition=0; $iForPosition < $iCharQuantity; $iForPosition++) { 

            if ($this->ChekcParity($sString)["iCharQuantity"] === 0){
                
                break;
            }


            $arChars = mb_str_split($sString);
            $iSadnessPos = 0;
            $iSmileRatio = 0;
            $iForeachPosition = 0;

            foreach ($arChars as $iForeachPosition => $sShar) {

                if ($sShar == "("){
                    $iSadnessPos = $iForeachPosition;
                    $iSmileRatio = 2;
                }elseif($iSmileRatio > 1 && $sShar == ")"){
                    $sString = mb_substr($sString, 0, $iSadnessPos) . mb_substr($sString, $iSadnessPos + $iSmileRatio);
                    break;
                }elseif($iSmileRatio != 0 ){
                    $iSmileRatio++;
                }

                if($iForeachPosition === count($arChars) - 1){
                    $arResult["bError"] = true;
                    $arResult["bErrorDescription"] = "Пара для  '(' не найдена. Последняя строка в работе " . mb_substr($sString,$iSadnessPos);
                }
            };
        }



        return $arResult;

    }



}


