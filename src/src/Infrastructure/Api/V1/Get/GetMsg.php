<?php

namespace src\Infrastructure\Api\V1\Get;
use src\Infrastructure\Api\V1\Common;

class GetMsg {

    public function return_answer($endpoint,$data) {

        if(empty($endpoint[1])) {

            $redis = new \Redis();
            $redis->connect('redis', 6379);
            $keys = $redis->keys('*');  
            $data = [];
            foreach ($keys as $key) {
                $data[$key]=$redis->get($key);
            }
            return Common::send_response([
                'status' => 'success',
                'message' => $data
            ], 200);

        } 

        else {

            $redis = new \Redis();
            $redis->connect('redis', 6379);
            $value = $redis->get($endpoint[1]); // Получаем значение по ключу
            if($value) {
                return Common::send_response([
                    'status' => 'success',
                    'message' => "Сообщение {$endpoint[1]} в обработке"
                ], 202);
            }
            else {
                return Common::send_response([
                    'status' => 'success',
                    'message' => "Сообщение {$endpoint[1]} обработано"
                ], 200);
            }

        }
        
    }

}


 