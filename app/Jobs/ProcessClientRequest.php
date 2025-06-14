<?php declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessClientRequest implements ShouldQueue
{
    use Queueable;

    public string $requestId;
    public array $data;

    public function __construct(string $requestId, array $data)
    {
        $this->requestId = $requestId;
        $this->data = $data;
    }

    public function handle(): void
    {
        cache()->put("request_status_{$this->requestId}", "validating");

        sleep(5);

        cache()->put("request_status_{$this->requestId}", "processing");

        sleep(5);

        cache()->put("request_status_{$this->requestId}", "processed");
    }
}
