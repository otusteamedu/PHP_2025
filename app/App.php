<?php

namespace app;

use App\Http\Request;
use App\Http\Response;
use App\Validation\FormRequest;

use App\Exceptions\EmptyPostException;
use App\Exceptions\UnbalancedStringException;

class App
{
    private const string SUCCESS = "Строка ':string' сбалансирована по открывающим/закрывающим скобкам.";

    private Request $request;
    private Response $response;
    private FormRequest $validator;
    private string $message;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->validator = new FormRequest();
        $this->message = '';
    }

    /**
     * @return Response
     */
    public function run(): Response
    {
        try {
            $validated = $this->validator->validate($this->request->getPost(), 'string');
            $this->message = str_replace(':string', $validated, self::SUCCESS);
            $this->response->setMessage($this->message);
        } catch (EmptyPostException|UnbalancedStringException $exception) {
            http_response_code(400);
            $this->response->setMessage($exception->getMessage());
        }

        return $this->response;
    }
}