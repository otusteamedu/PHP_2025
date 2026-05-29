<?php

declare(strict_types=1);

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/SearchService.php';
require_once __DIR__ . '/RequestValidator.php';
require_once __DIR__ . '/ResponseBuilder.php';
require_once __DIR__ . '/SearchResponse.php';

function main(array $event, $context): array
{
    $validator = new RequestValidator();
    $config = Config::fromEnvironment();
    $service = new SearchService($config);
    $builder = new ResponseBuilder();

    $error = $validator->validate($event);
    if ($error !== null) {
        return $builder->error(400, $error);
    }

    $body = json_decode($event['body'] ?? '', true);
    $question = $body['question'] ?? '';
    $chatId = $body['chat_id'] ?? null;

    $result = $service->search($question, $chatId);

    if ($result->isSuccess()) {
        return $builder->success($result);
    } else {
        return $builder->error($result->errorCode, $result->errorMessage);
    }
}
