<?php
namespace App;

class EmailTester
{
    private $verifier;
    
    public function __construct()
    {
        $this->verifier = new EmailVerifier();
    }
    
    public function testEmails($emails = [])
    {
        $defaultEmails = [
            "none@mai.ru",
            "cabinet#net.ru",
            "faraon@mail.kz",
            "mail@mail.ml"
        ];
        
        $emails = empty($emails) ? $defaultEmails : $emails;
        $results = [];
        
        foreach ($emails as $email) {
            $isValid = $this->verifier->verify($email);
            $results[$email] = $isValid;
        }
        
        return $results;
    }
    
    public function printResults($results)
    {
        foreach ($results as $email => $isValid) {
            $status = $isValid ? 'верифицирован успешно' : 'не верифицирован';
            echo "$email - $status" . PHP_EOL;
        }
    }
}