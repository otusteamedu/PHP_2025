<?php
declare(strict_types=1);

namespace MyApp;

use MyApp\ValidatorException;
use Throwable;

class CRequester
{
    public function __construct(private CValidator $Validator, private CResponser $Responser) {}

    public function handle(array $post): string
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new ValidatorException('Требуется POST-запрос');
            }

            if (!array_key_exists('string', $post)) {
                throw new ValidatorException('Не передан параметр string');
            }

            $this->Validator->validate($post['string']);

            $this->Responser->send(200);
            return 'OK: строка корректна';
        } catch (ValidatorException $e) {
            $this->Responser->send(400);
            return 'ERROR: ' . $e->getMessage();
        } catch (Throwable $e) {
            $this->Responser->send(400);
            return 'ERROR: ' . $e->getMessage();
        }
    }
}