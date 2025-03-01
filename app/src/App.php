<?php
declare(strict_types=1);

namespace App;

use App\Http\Request;
use App\Http\Response;
use App\Validation\Validation;
use Exception;
use Throwable;

class App
{
    private Request $request;
    private Response $response;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    public function run(): Response
    {
        try {
            $postParam = 'string';
            if (!$this->request->isPost()) {
                throw new Exception('Request method must be POST.');
            }
            $paramToValidate = $this->request->post($postParam);
            if (Validation::isEmptyString($paramToValidate)) {
                throw new Exception('Empty string parameter.');
            }

            if (Validation::isValidBrackets($paramToValidate)) {
                throw new Exception(sprintf('Parameter "%s" is invalid.', $postParam));
            }

            return $this->response->send('String is valid.', 200);
        } catch (Throwable $e) {
            return $this->response->send($e->getMessage(), 400);
        }
    }

}