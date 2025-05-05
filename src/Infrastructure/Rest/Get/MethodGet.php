<?php

namespace Infrastructure\Rest\Get;
use Infrastructure\Rest\Common;

class MethodGet {

    private $endpoint;
    private $data;

    public function __construct($endpoint,$data) {
        $this->endpoint = $endpoint;
        $this->data = $data;
    }

    public function endpoint() {
        
        $class_name = (isset($this->endpoint[0])) ? '\\Infrastructure\\Rest\\Get\\'.$this->endpoint[0] : "";

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


 