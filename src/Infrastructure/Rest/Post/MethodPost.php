<?php

namespace Infrastructure\Rest\Post;
use Infrastructure\Rest\Common;

class MethodPost {

    private $endpoint;
    private $data;

    public function __construct($endpoint,$data) {
        $this->endpoint = $endpoint;
        $this->data = $data;
    }

    public function endpoint() {
        
        $class_name = '\\Infrastructure\\Rest\\Post\\'.$this->endpoint[0];

        if (class_exists($class_name)) {

            if(!empty($this->data)) {
                $ans = new $class_name;
                $ans->return_answer($this->endpoint,$this->data);
            } else {
                Common::send_response([
                    'status' => 'failed',
                    'message' => 'Post var are empty',
                ], 400);
            }
                
        }

        else {
            Common::send_response([
				'status' => 'failed',
				'message' => 'No method allow',
			], 400);
        }

    }

}


 