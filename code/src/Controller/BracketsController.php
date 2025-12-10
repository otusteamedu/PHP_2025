<?php

declare(strict_types=1);

namespace src\Controller;

use Exception;
use InvalidArgumentException;
use src\Service\BracketValidator;
use src\Http\Response;

class BracketsController
{
    public function __construct(
        readonly private BracketValidator $validator
    ) {}

    public function handle(): Response
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return new Response(json_encode([
                'status' => 'error',
                'msg'    => 'Только POST запросы'
            ]), 405);
        }

        $input = $_POST['string'] ?? '';

        try {
            $this->validator->validate($input);

            return new Response(json_encode([
                'status' => 'ok',
                'msg'    => 'Строка валидна',
                'server' => gethostname()
            ]), 200);

        } catch (InvalidArgumentException $e) {
            return new Response(json_encode([
                'status' => 'error',
                'msg'    => $e->getMessage()
            ]), 400);
        } catch (Exception $e) {
            return new Response(json_encode([
                'status' => 'error',
                'msg'    => $e->getMessage()
            ]), 500);
        }
    }
}
