<?php

namespace Application;

use Infrastructure\Exceptions\ValidationException;
use Infrastructure\Tasks\InitControllerTask;
use Application\Http\Request;
use Application\Http\Response;
use Exception;
use Throwable;

class Kernel
{
    /**
     * @return void
     */
    public function run(): void {
        try {
            $request = new Request(
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                $_POST,
                apache_request_headers()
            );

            $response = (new InitControllerTask())->run($request);
        } catch (ValidationException $e) {
            $response = new Response([], 400, $e->getMessage());
        } catch (Exception $e) {
            $response = new Response([], $e->getCode() ?? 400, $e->getMessage());
        } catch (Throwable $e) {
            $response = new Response([], 500, "Something wrong");
        } finally {
            $response->init();
        }
    }
}