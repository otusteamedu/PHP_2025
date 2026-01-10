<?php declare(strict_types=1);

namespace App\Tasks\Models;

/**
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     title="Task",
 *     description="Task model",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="Unique task identifier"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Task status",
 *         enum={"pending", "processing", "completed", "failed"}
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         description="Task data"
 *     ),
 *     @OA\Property(
 *         property="result",
 *         type="object",
 *         description="Task result"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
 *     )
 * )
 */
class Task
{
	public const STATUS_PENDING = 'pending';
	public const STATUS_PROCESSING = 'processing';
	public const STATUS_COMPLETED = 'completed';
	public const STATUS_FAILED = 'failed';

	public string $id;
	public string $status;
	public array $data;
	public ?array $result;
	public string $created_at;
	public string $updated_at;

	public function __construct(array $data = [])
	{
		$this->id = $data['id'] ?? uniqid('task_', true);
		$this->status = $data['status'] ?? self::STATUS_PENDING;
		$this->data = $data['data'] ?? [];
		$this->result = $data['result'] ?? null;
		$this->created_at = $data['created_at'] ?? date('c');
		$this->updated_at = $data['updated_at'] ?? date('c');
	}

	public function toArray(): array
	{
		return [
			'id' => $this->id,
			'status' => $this->status,
			'data' => $this->data,
			'result' => $this->result,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at
		];
	}
}