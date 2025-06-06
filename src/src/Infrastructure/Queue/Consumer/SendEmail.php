<?php


class SendEmail {

    public function __invoke($email) {

        sleep(5);
        echo "Письмот отправлено на {$email} в ".date("Y:m:d H.i.s")."\n";

    }

}


