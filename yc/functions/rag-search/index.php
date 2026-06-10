<?php

declare(strict_types=1);

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/SearchService.php';
require_once __DIR__ . '/RequestValidator.php';
require_once __DIR__ . '/ResponseBuilder.php';
require_once __DIR__ . '/SearchResponse.php';

function main(array $event, $context): array
{
    $builder = new ResponseBuilder();

    try {
        $config = Config::fromEnvironment();
        $validator = new RequestValidator($config);
        $service = new SearchService($config);

        $result = $validator->validate($event);
        if (!$result->isSuccess()) {
            return $builder->error($result->errorCode, $result->error);
        }

        $question = $result->decodedBody['question'] ?? '';

        $searchResult = $service->search($question);

        if ($searchResult->isSuccess()) {
            return $builder->success($searchResult);
        } else {
            return $builder->error($searchResult->errorCode, $searchResult->errorMessage);
        }
    } catch (SearchApiException $e) {
        error_log('SearchApiException: ' . $e->getMessage());
        return $builder->error($e->statusCode, $e->getMessage());
    } catch (\Throwable $e) {
        error_log('Unhandled exception: ' . $e->getMessage());
        return $builder->error(500, 'Внутренняя ошибка сервера');
    }
}
