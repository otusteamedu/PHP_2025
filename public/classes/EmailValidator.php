<?php

namespace classes;

class EmailValidator
{

    public function validateEmailList(array $arEmails):void
    {
        if (!is_array($arEmails)) throw new \RuntimeException('$arEmails is not an array');
        if (empty($arEmails)) throw new \RuntimeException('There is no list of emails');


        $arResult = [];
        foreach ($arEmails as $email) {
            $arResult[$email]['IS_FORMAT_VALID'] = $this->validateEmailFormat($email);
            $arResult[$email]['ARE_DNS_MX_EXIST'] = $this->checkEmailByDnsMX($email);
        }

        echo '<pre>';
        var_dump($arResult);
        echo '</pre>';
    }

    private function validateEmailFormat(string $email):bool
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    private function checkEmailByDnsMX(string $email):bool
    {
        $domain = substr(strrchr($email, "@"), 1);

        $isValid = true;
        $res = getmxrr($domain, $mx_records, $mx_weight);
        if (false == $res || 0 == count($mx_records) || (1 == count($mx_records) && ($mx_records[0] == null  || $mx_records[0] == "0.0.0.0" ) ) ){
            $isValid = false;
        }

        return $isValid;
    }
}