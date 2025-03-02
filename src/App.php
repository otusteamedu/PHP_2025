<?php declare(strict_types=1);

namespace App;

use App\Exceptions\ValidationException;
use App\Http\Requests\Request;
use App\Http\Response;
use App\Validations\StringVerify;

class App
{
    private Request $request;
    private Response $response;
    private StringVerify $validator;

    public function __construct(Request $request, Response $response, StringVerify $validator)
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
            $validatedString = $this->validator->validate($this->request->getPost(), 'string');
            $this->response->setData([
                'message' => $this->createSuccessMessage($validatedString),
            ]);
        } catch (ValidationException $e) {
            $this->handleValidationException($e);
        }

        return $this->response;
    }

    /**
     * @param string $validatedString
     * @return string
     */
    private function createSuccessMessage(string $validatedString): string
    {
        return str_replace(':string', $validatedString, 'OK');
    }

    /**
     * @param ValidationException $e
     * @return void
     */
    private function handleValidationException(ValidationException $e): void
    {
        $this->response->setStatusCode($e->getCode());
        $this->response->setData([
            'errors' => [$e->getMessage()],
        ]);
    }
}