<?php

namespace App;

class Validator {

    public function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, "@"), 1);

        if (!checkdnsrr($domain, "MX")) {
            return false;
        }

        return true;
    }

}