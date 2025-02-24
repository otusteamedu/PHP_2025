<?php

namespace App;

use App\Exceptions\ValidationException;
use App\Request\Request;
use Exception;
use Throwable;

class App
{
    public function run()
    {
        try {
            $request = new Request($_SERVER['REQUEST_METHOD'], $_POST);
            $data = $request->init();
            $response = [
                'success' => true,
                'data' => $data,
            ];
        } catch (ValidationException $e) {
            http_response_code(400);
            $message = $e->getMessage();
            $response['success'] = false;
        } catch (Exception $e) {
            http_response_code($e->getCode() ?? 400);
            $message = $e->getMessage();
            $response['success'] = false;
        } catch (Throwable $e) {
            http_response_code(500);
            $response['success'] = false;
            $message = "Something wrong";
        } finally {
            header('Content-Type: application/json; charset=utf-8');

            if (empty($message) === false) {
                $response['message'] = $message;
            }

            echo json_encode($response);
        }
    }
}