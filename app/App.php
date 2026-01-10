<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025;

use \Exception;
use \JsonException;
use Zibrov\OtusPhp2025\Http\Exceptions\HttpException;
use Zibrov\OtusPhp2025\Http\Exceptions\MethodNotAllowed;
use Zibrov\OtusPhp2025\Http\Request;
use Zibrov\OtusPhp2025\Http\Response;
use Zibrov\OtusPhp2025\Validators\ValidationException;
use Zibrov\OtusPhp2025\Validators\ValidationString;

class App
{

    private Request $request;
    private Response $response;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    /**
     * @throws JsonException
     */
    public function run(): Response
    {
        try {
            if (!$this->request->isPostMethod()) {
                throw new MethodNotAllowed('POST method not allowed');
            }

            if ($value = $this->request->getPostValueByKey('string')) {
                if (ValidationString::checkingBrackets($value)) {
                    return $this->response->send(200, [
                        'message' => 'String passed validation',
                    ]);
                }

                throw new ValidationException('String failed validation');
            }

            throw new ValidationException('String is empty or does not exist');

        } catch (HttpException $e) {
            return $this->response->send($e->getHttpCode(), [
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return $this->response->send(500, [
                'message' => 'Internal server error ' . $e->getMessage(),
            ]);
        }
    }
}