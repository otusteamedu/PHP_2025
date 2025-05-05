<?php

namespace Infrastructure\Rest\Get;

use Infrastructure\Rest\Common;
use Application\UseCase\GetNews\GetNewsUseCase;
use Infrastructure\Factory\CommonNewsFactory;
use Infrastructure\Repository\FileNewsRepository;
use Infrastructure\Http\GetNewsController;


class GetNews {

    public function return_answer($endpoint,$data) {

        if(empty($endpoint[1]))
            Common::send_response([
                'status' => 'failed',
                'message' => "Id is empty",
            ], 400);

        $id_array = preg_split('/\,/', $endpoint[1], -1, PREG_SPLIT_NO_EMPTY);

        $answer = (
            new GetNewsController(
                (
                    new GetNewsUseCase(
                        new CommonNewsFactory,
                        new FileNewsRepository
                    )
                )
            )
        )($id_array); 

        if(is_array($answer)) {

            if($file_url = Common::save_html($answer,"/storage/")) 
                return Common::send_response([
                    'status' => 'success',
                    'message' => "File is save http://{$_SERVER['HTTP_HOST']}{$file_url}"
                ], 200);
            else 
                return Common::send_response([
                    'status' => 'failed',
                    'message' => "File saving error",
                ], 400);

        }

            return Common::send_response([
                'status' => 'success',
                'message' => $answer
            ], 200);

        Common::send_response([
            'status' => 'failed',
            'message' => $answer,
        ], 400);
        
    }

}


 