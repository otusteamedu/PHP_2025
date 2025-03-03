<?php declare(strict_types=1);

namespace App;

use App\Exceptions\ValidationException;
use App\Http\Response;
use App\Validations\EmailValidation;

class App
{
    private Response $response;
    private EmailValidation $validator;

    public function __construct()
    {
        $this->response = new Response();
        $this->validator = new EmailValidation();
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
            $validated = $this->validator->validate($_POST, 'emails');

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