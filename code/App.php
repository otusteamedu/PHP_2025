<?php

declare(strict_types=1);

namespace Dkeruntu\OtusHomeWorkFourCheckBracket;

require_once __DIR__ . '/HttpWorker.php';
require_once __DIR__ . '/CheckBracket.php';

use Dkeruntu\OtusHomeWorkFourCheckBracket\CheckBracket;
use Dkeruntu\OtusHomeWorkFourCheckBracket\HttpWorker;

class App
{
    private CheckBracket $obCheckBracket;
    private HttpWorker $obHttpWorker;

    public function __construct()
    {
        $this->obCheckBracket = new CheckBracket();
        $this->obHttpWorker = new HttpWorker();
    }

    public function run(): string
    {
        $arContainerInfo = $this->obHttpWorker->getContainerInfo();

        $arResultInputString = $this->obHttpWorker->getInputString();

        if (!$arResultInputString["bError"]){
            $sInputString = $arResultInputString["sReturnString"];

            $arValidationResult = $arResultInputString;
            
            $arParityResult = $this->obCheckBracket->ChekcParity($sInputString);


            if ($arParityResult["bError"]){
                $arValidationResult = $arParityResult;
            }else{
                $arValidationResult = $this->obCheckBracket->CheckValidation($sInputString,$arParityResult["iCharQuantity"]);
            };

        }else{
            $sInputString = null;
            $arValidationResult = $arResultInputString;

        }


        return $this->renderResult($arContainerInfo, $sInputString, $arValidationResult);
    }

    private function renderResult(array $arContainerInfo, ?string $sInputString, array $arValidationResult)
    {
        ob_start();
        include __DIR__ . '/Templates.php';
        return ob_get_clean();
    }
}