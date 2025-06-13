<?php

namespace src\Infrastructure\Api\V1\Post;
use Src\Infrastructure\Queue\Producer\Producer;
use src\Infrastructure\Api\V1\Common;


class AddMsg {

    public function return_answer($endpoint,$data) {
        $data["id"] = uniqid();
        $msg_id = (new Producer)(json_encode($data));

        return Common::send_response([
            'status' => 'success',
            'message' => "Сообщение № {$msg_id} добавлено в очередь",
        ], 201);
        
    }

}


 