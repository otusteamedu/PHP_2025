<?php

namespace Infrastructure\Rest\Get;

use Infrastructure\Rest\Common;
use Application\UseCase\GetAllNews\GetAllNewsUseCase;
use Infrastructure\Factory\CommonNewsFactory;
use Infrastructure\Repository\FileNewsRepository;
use Infrastructure\Http\GetAllNewsController;


class GetAllNews {

    public function return_answer($endpoint,$data) {

        $answer = (
            new GetAllNewsController(
                (
                    new GetAllNewsUseCase(
                        new CommonNewsFactory,
                        new FileNewsRepository
                    )
                )
            )
        )(); 

        if(is_array($answer))

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


 