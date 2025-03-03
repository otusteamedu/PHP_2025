<?php declare(strict_types=1);

namespace App;

use App\Exceptions\ValidationException;
use App\Http\Response;
use App\Validations\StringVerify;

class App
{
    private Response $response;
    private StringVerify $validator;

    public function __construct()
    {
        $this->response = new Response();
        $this->validator = new StringVerify();
    }

    /**
     * @return Response
     */
    public function run(): Response
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->response->setStatusCode(405);
                $this->response->setData([
                    'message' => 'Method not allowed',
                ]);

                return $this->response;
            }

            $validatedString = $this->validator->validate($_POST, 'string');
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