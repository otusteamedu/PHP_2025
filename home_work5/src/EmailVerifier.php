<?php
namespace App;
use App\EmailChecker;

class EmailVerifier
{
    private $checker;
    
    public function __construct()
    {
        $this->checker = new EmailChecker();
    }
    
    public function verify($email)
    {
        // Проверяем правильность email
        $domain = $this->checker->checkSyntax($email);
        if (!$domain) {
            return false;
        }
        
        // Проверяем MX запись
        return $this->checker->checkDns($domain);
    }
}