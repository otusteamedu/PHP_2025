<?php

namespace App;

use App\Validator;

class App {
    public function run($emailsToCheck) {

        $validator = new Validator();
        $responses = [];

        foreach ($emailsToCheck as $email) {
            if ($validator->validateEmail($email)) {
                $responses[] = "$email is valid.";
            } else {
                $responses[] = "$email is invalid.";
            }
        }

        return implode("<br>", $responses);
        
    }
}
