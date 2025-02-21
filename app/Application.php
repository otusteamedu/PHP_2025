<?php

namespace App;

use App\Http\Exceptions\BadRequestHttpException;
use App\Http\Exceptions\MethodNotAllowedHttpException;
use App\Http\Exceptions\ServerErrorHttpException;
use App\Http\Request;
use App\Http\Response;
use App\Validators\BracketsValidator;
use App\Validators\ValidationException;
use Throwable;

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
     *
     */
    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    /**
     * @return void
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function run(): void
    {
        if (!$this->request->isPost()) {
            throw new MethodNotAllowedHttpException('Only POST method allowed');
        }

        $string = $this->request->post('string', '');

        $bracketsValidator = new BracketsValidator();

        try {
            $bracketsValidator->validate($string);
            echo $this->response->send(200, [
                'name' => 'OK',
                'status' => 200,
                'message' => 'String is correct!',
            ]);
        } catch (ValidationException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }
    }
}
