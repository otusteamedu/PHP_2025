<?php

namespace Validation\Email;

class ValidatorEmail extends Validator {

    public function isValid(Array $emails): array {
        $invalid = [];
        foreach($emails as $email) {
            $domain = substr(strrchr($email, "@"),1);
            $isPatternEmailMatch = preg_match(self::VALID_EMAIL_PATTERN, $email);
            $isMxDomain = !empty($domain) && checkdnsrr($domain, "MX");

            if(!$isPatternEmailMatch || !$isMxDomain) {
                $invalid[] = $email;
            }
        }

        return $invalid;

    }

}