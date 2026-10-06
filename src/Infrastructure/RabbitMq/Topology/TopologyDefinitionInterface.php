<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Topology;

use PhpAmqpLib\Channel\AMQPChannel;

interface TopologyDefinitionInterface
{
    public function declare(AMQPChannel $channel): void;
}
