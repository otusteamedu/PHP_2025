<?php
declare(strict_types=1);

namespace App\Http\Api\v1\Task\CreateTask\Output;

use JsonSerializable;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreatedTaskResponse',
    required: ['request_id', 'status']
)]
class CreatedTaskDTO implements JsonSerializable
{
    #[OA\Property(
        property: 'request_id',
        type: 'string',
        format: 'uuid',
        example: '01958674-f8e1-7db1-9d3f-0db4c9f4d8f3'
    )]
    public string $requestId;

    #[OA\Property(
        property: 'status',
        type: 'string',
        example: 'queued'
    )]
    public string $status;

    public function __construct(
        string $requestId,
        string $status,
    ) {
        $this->requestId = $requestId;
        $this->status = $status;
    }

    public function jsonSerialize(): array
    {
        return [
            'request_id' => $this->requestId,
            'status' => $this->status,
        ];
    }
}
