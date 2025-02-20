<?php

namespace App;

use App\Connection\RedisConnection;
use App\Error\ValidationException;
use App\Validation\Validation;
use Exception;
use Throwable;

class App
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * @return void
     */
    public function run(): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];

            $response = [
                'success' => true,
            ];

            if ($method === 'POST') {
                $data = $this->initPost(Validation::validate());
            } else {
                $data['message'] = 'OK...';
            }

            $response = array_merge($response, $data);
        } catch (ValidationException $e) {
            http_response_code(400);
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

    /**
     * @return void
     * @throws Exception
     */
    protected function connect(): void
    {
        RedisConnection::connect();
    }

    /**
     * @param array $data
     * @return array
     */
    protected function initPost(array $data): array
    {
        return [
            'message' => "'string' value: " . $data['string'],
        ];
    }
}