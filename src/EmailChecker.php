<?php
namespace App;
class EmailChecker
{
    private const EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@((?!.*\.\.)[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/';
    
    public function checkSyntax($email)
    {
        if (preg_match(self::EMAIL_REGEX, $email, $matches)) {
            return $matches[1];
        }
        return false;
    }
    
    public function checkDns($domain)
    {
        $mx = dns_get_record($domain, DNS_MX);
        return !empty($mx);
    }
}