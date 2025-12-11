<?php
class EmailVerifier{

    private CONST EMAIL_REGEX = '/^([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/';

    public function Verify(string $email) : bool {
        
        $domain = $this->verifyRightName($email);
        if(!$domain) return false;
        return $this->verifyMxRecord($domain);
    }

    /**
     * Проверка синтаксиса соответсвует ли написание email
     * @param mixed $email
     */
    private function verifyRightName($email)
    {
        if (preg_match(self::EMAIL_REGEX, $email, $matches)) {
            // $matches[2] содержит доменную часть
            return $matches[2]; 
        }
        return false;
    }


    private function verifyMxRecord(string $domain)
    {
          $mx = dns_get_record($domain, DNS_MX); 
          return !empty($mx) ? true: false; 
    }


    public function Test() {


        $emails = [
            "none@mai.ru",
            "cabinet#net.ru",
            "faraon@mail.kz",
            "mail@mail.ml"
        ];

        foreach($emails as $email)
          print_r("{$email} - ".($this->Verify($email)? 'верифицирован успешно': 'не верифицирован').PHP_EOL);
        
    }

}

(new EmailVerifier())->Test();