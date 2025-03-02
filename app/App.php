<?php

namespace App;

use App\Exceptions\ValidationException;
use App\Request\Request;
use App\Response\Response;
use Exception;
use Throwable;

class App
{
    /**
     * @return void
     */
    public function run()
    {
        try {
            $request = new Request($_SERVER['REQUEST_METHOD'], $_POST);
            $response = $request->init();
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