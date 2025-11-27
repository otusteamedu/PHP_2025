<?php
namespace App;

class Process
{
    public static function processEmails($filename)
    {
        $emails = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $results = [];

        foreach ($emails as $email) {
            $results[$email] = Validation::validateEmail($email);
        }
        
        return $results;
    }
}
?>