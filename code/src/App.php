<?php

declare(strict_types=1);

namespace Dinargab\Homework4;

use Dinargab\Homework4\Service\Validator;
use InvalidArgumentException;

class App
{

    public const string POST_KEY = "string";

    public static function init(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            (new Response())->setStatusCode(405)->send();
            return;
        }

        $inputString = $_POST[self::POST_KEY];

        try {
            if ((new Validator())->validateString($inputString)) {
                (new Response())
                    ->setStatusCode(200)
                    ->addHeader("Content-Type", "application/json")
                    ->setContent(json_encode(['message' => "Everything is OK!"]))
                    ->send();
            } else {
                (new Response())
                    ->setStatusCode(400)
                    ->addHeader("Content-Type", "application/json")
                    ->setContent(json_encode(['message' => "String is imbalanced"]))
                    ->send();
            }
        } catch (InvalidArgumentException $exception) {
            (new Response())
                ->setStatusCode(400)
                ->addHeader("Content-Type", "application/json")
                ->setContent(json_encode(['message' => $exception->getMessage()]))
                ->send();
        }
    }
}
