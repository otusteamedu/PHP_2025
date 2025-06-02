<?php

namespace App\Classes;



class TelegramService
{

    public function sendNotification(string $message)
    {
        try {
            $text = urlencode($message);
            $url = "https://api.telegram.org/bot{$_ENV['TELEGRAM_BOT_TOKEN']}/sendMessage?chat_id={$_ENV['TELEGRAM_CHAT_NAME']}&text={$text}";

            $ch = curl_init();
            $optArray = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $optArray);
            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

}