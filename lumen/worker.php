<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Настраиваем Eloquent Capsule вручную для MySQL
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'lumen_queue',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

// Делаем Capsule доступным глобально и запускаем Eloquent
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Определяем модель (поставь namespace если надо)
class Request extends Illuminate\Database\Eloquent\Model {
    protected $table = 'requests';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'payload', 'status', 'result'];
}

// Подключаемся к RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('requests_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
    $data = json_decode($msg->body, true);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo "No id in message\n";
        $msg->ack();
        return;
    }

    $req = Request::find($id);
    if (!$req) {
        echo "Request not found: $id\n";
        $msg->ack();
        return;
    }

    $req->status = 'processing';
    $req->save();

    // Эмуляция работы
    sleep(5);

    $req->status = 'done';
    $req->result = "Processed payload at " . date('Y-m-d H:i:s');
    $req->save();

    echo " [x] Done processing $id\n";

    $msg->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('requests_queue', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
