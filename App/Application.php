<?php

declare(strict_types=1);

namespace App;

use App\Service\EmailVerificationService;

class Application
{
    public function run(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emails = \explode(";", trim($_POST['emails']));
            $emailVerificationService = new EmailVerificationService();
            $resEmails = $emailVerificationService($emails);

            foreach ($resEmails as $email => $emailRes) {
                echo "{$email} - {$emailRes['isValidByChars']}, {$emailRes['isValidByMX']}<br>";
            }
        }
    }
}
