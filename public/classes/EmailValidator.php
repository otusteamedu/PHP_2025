<?php

namespace classes;

class EmailValidator
{
    public function validateEmailFormat(string $email):bool
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    public function checkEmailByDnsMX(string $email):bool
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