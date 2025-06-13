<?php

namespace src\Infrastructure\Api\V1\Get;
use src\Infrastructure\Api\V1\Common;

class MethodGet {

    private $endpoint;
    private $data;

    public function __construct($endpoint,$data) {
        $this->endpoint = $endpoint;
        $this->data = $data;
    }

    public function endpoint() {
        
        $class_name = (isset($this->endpoint[0])) ? '\\src\\Infrastructure\\Api\\V1\\Get\\'.$this->endpoint[0] : "";

        if (class_exists($class_name)) {

            $ans = new $class_name;
            $ans->return_answer($this->endpoint,$this->data);
                
        }

        else {
            Common::send_response([
				'status' => 'failed',
				'message' => 'Method Not Allowed',
			], 400);
        }

    }

}


 