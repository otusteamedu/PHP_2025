<?php

declare(strict_types=1);

namespace Dkeruntu\OtusHomeWorkFiveCheckMail;

require_once __DIR__ . '/CheckMail.php';

use Dkeruntu\OtusHomeWorkFiveCheckMail\CheckMail;

class App
{

    private CheckMail $obCheckMail;

    public function __construct()
    {
        $this->obCheckMail = new CheckMail();
    }

    private const AR_CHECK_METHODS = [
        'CheckEmpty',
        'ChekcStreln', 
        'CheckDogCount',
        'ChekcValid',
        'CheckDomain',
        'CheckMX'
    ];

    public function run(): string
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

        $arError["bError"] = false;

        $sInput = htmlspecialchars($_POST['user_input'] ?? '');
        $arOutput['sInput']  = $sInput;


        foreach (SELF::AR_CHECK_METHODS as $sMethod) {
            if (!$arError['bError']) {

                $arResult = $this->obCheckMail->$sMethod($sInput);
                if ($arResult['bError']) {
                    break; 
                }
            }
        }


        if($arResult['bError']){
            $arOutput['sResult'] = "Ошибка: " .  $arResult["bErrorDiscription"];
        }else{
            $arOutput['sResult']  = "Адрес корректен";
        }
            

        }else{
            $arResult = NULL;
        }

        
        return $this->renderResult($arOutput);

    }


    private function renderResult($arOutput)
    {
        ob_start();
        include __DIR__ . '/Templates.php';
        return ob_get_clean();
    }
}




?>

