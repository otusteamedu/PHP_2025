<?php

namespace Infrastructure\Rest\Post;
use Infrastructure\Rest\Common;
use Application\UseCase\AddNews\SubmitNewsUseCase;
use Infrastructure\Factory\CommonNewsFactory;
use Infrastructure\Repository\FileNewsRepository;


class SubmitNews {

    public function return_answer($endpoint,$data) {

        $answer = (
            new \Infrastructure\Http\SubmitNewsController(
                (
                    new SubmitNewsUseCase(
                        new CommonNewsFactory,
                        new FileNewsRepository
                    )
                )
            )
        )($data["url"]);

        if(is_numeric($answer))

            return Common::send_response([
                'status' => 'success',
                'message' => "News id: ".$answer,
            ], 200);

        Common::send_response([
            'status' => 'failed',
            'message' => $answer,
        ], 400);
        
    }

}


 