<?php

declare(strict_types=1);

namespace Dkeruntu\OtusHomeWorkFourCheckBracket;

class HttpWorker
{
    public function getInputString(): ?array
    {
        $sReturnString = $_GET['string'] ?? $_POST['string'] ?? null;

        if (($sReturnString === null) || ($sReturnString === '')){
            $arRes["bError"] = true;
            $arRes["bErrorDescription"] = "На вход не поступило параметра string";
        }else{
            $arRes["bError"] = false;
            $arRes["sReturnString"] = $sReturnString;
        }

        return $arRes;

    }

    public function getRequestMethod(): string
    {
        return isset($_GET['string']) ? 'GET' : (isset($_POST['string']) ? 'POST' : 'NONE');
    }

    public function getContainerInfo(): array
    {
        return [
            'balancer' => $_SERVER['HTTP_X_BALANCER_CONTAINER'] ?? 'Not set',
            'backend' => $_SERVER['HTTP_X_BACKEND_CONTAINER'] ?? 'Not set'
        ];
    }
}