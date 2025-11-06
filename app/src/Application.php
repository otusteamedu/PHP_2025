<?php declare(strict_types=1);

namespace App;

use App\Http\Exceptions\BadRequestHttpException;
use App\Http\Exceptions\MethodNotAllowedHttpException;
use App\Http\Request;
use App\Http\Response;
use App\Services\EmailValidationService;

/**
 * Class Application
 * @package App
 */
class Application
{
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var Response
     */
    private Response $response;
    /**
     * @var EmailValidationService
     */
    private EmailValidationService $service;

    /**
     * @param bool $validateDNS
     * @param bool $enableIDN whether validation process should take into account IDN (internationalized domain names).
     */
    public function __construct(bool $validateDNS = false, bool $enableIDN = false)
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->service = new EmailValidationService($validateDNS, $enableIDN);
    }

    /**
     * @return void
     * @throws BadRequestHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function run(): void
    {
        if (!$this->request->isPost()) {
            throw new MethodNotAllowedHttpException('Only POST method allowed');
        }

        $requestRawBody = $this->request->getRawBody();
        if (!$requestRawBody) {
            throw new BadRequestHttpException('Request body must not be empty');
        }

        $decodedBody = json_decode($requestRawBody, true);
        if (!$decodedBody) {
            throw new BadRequestHttpException('Incorrect request body');
        }

        $emails = $decodedBody['emails'] ?? null;
        if (!is_array($emails)) {
            throw new BadRequestHttpException('Incorrect request body format: json must contain an array of emails');
        }

        if (empty($emails)) {
            throw new BadRequestHttpException('Array of emails is empty');
        }

        $result = $this->service->validate($emails);

        echo $this->response->send(200, $result);
    }
}
