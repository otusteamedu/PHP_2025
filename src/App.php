<?php

namespace App;

use App\Http\ExceptionHandler;
use App\Http\Request;
use App\Http\Response;
use App\Validation\EmailValidation;
use App\Validation\FormRequest;
use Throwable;

class App
{
    private Request $request;
    private Response $response;
    private ExceptionHandler $exceptionHandler;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->exceptionHandler = new ExceptionHandler();
    }

    public function run(): Response
    {
        try {
            $validatedRequest = RequestPipeline::send($this->request)
                ->through([
                    FormRequest::class,
                    EmailValidation::class,
                ])
                ->thenReturn();

            $this->response->setData($validatedRequest->get('emails'));
        } catch (Throwable $exception) {
            $this->response = $this->exceptionHandler->render($exception);
        }

        return $this->response;
    }
}