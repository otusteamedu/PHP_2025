<?php declare(strict_types=1);

namespace App;

use App\Exceptions\ValidationException;
use App\Http\Requests\Request;
use App\Http\Response;
use App\Validations\EmailValidation;

class App
{
    private Request $request;
    private Response $response;
    private EmailValidation $validator;

    public function __construct(Request $request, Response $response, EmailValidation $validator)
    {
        $this->request = $request;
        $this->response = $response;
        $this->validator = $validator;
    }

    /**
     * @return Response
     */
    public function run(): Response
    {
        try {
            $validated = $this->validator->validate($this->request->getPost(), 'emails');

            $this->response->setData($validated);
        } catch (ValidationException $e) {
            $this->handleValidationException($e);
        }

        return $this->response;
    }

    /**
     * @param ValidationException $e
     * @return void
     */
    private function handleValidationException(ValidationException $e): void
    {
        $this->response->setStatusCode($e->getCode());
        $this->response->setData([
            'errors' => $e->getErrors(),
        ]);
    }
}