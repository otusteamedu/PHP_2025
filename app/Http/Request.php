<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Http;

use \JsonException;
use Zibrov\OtusPhp2025\Http\Exceptions\BadRequestException;

class Request
{

    private const METHOD_POST = 'POST';

    public function isPostMethod(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === self::METHOD_POST;
    }

    /**
     * @throws BadRequestException
     * @throws JsonException
     */
    public function getValuesByKey(string $key): array
    {
        if (!($requestContentJsonString = file_get_contents('php://input'))) {
            throw new BadRequestException('Request is empty');
        }

        if (!($requestContentJsonArray = json_decode($requestContentJsonString, true, 512, JSON_THROW_ON_ERROR))) {
            throw new BadRequestException('Invalid request text body');
        }

        $arValues = $requestContentJsonArray[$key];
        if (empty($arValues)) {
            throw new BadRequestException('Empty value');
        }

        return is_array($arValues) ? $arValues : [$arValues];
    }
}
