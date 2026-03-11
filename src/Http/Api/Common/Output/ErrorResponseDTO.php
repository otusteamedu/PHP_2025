<?php
declare(strict_types=1);

namespace App\Http\Api\Common\Output;

use JsonSerializable;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorResponse',
    required: ['message']
)]
class ErrorResponseDTO implements JsonSerializable
{
    #[OA\Property(
        property: 'message',
        type: 'string',
        example: 'request_id must be a valid UUID'
    )]
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function jsonSerialize(): array
    {
        return ['message' => $this->message];
    }
}
