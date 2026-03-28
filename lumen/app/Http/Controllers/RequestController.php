<?php
namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RequestController extends Controller
{
    public function create(Request $request)
    {
        $id = Str::uuid()->toString();
        $payload = $request->all();

        RequestModel::create([
            'id' => $id,
            'payload' => json_encode($payload),
            'status' => 'pending',
            'result' => null,
        ]);

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('requests_queue', false, true, false, false);

        $msg = new AMQPMessage(json_encode([
            'id' => $id,
            'payload' => $payload,
        ]), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        $channel->basic_publish($msg, '', 'requests_queue');

        $channel->close();
        $connection->close();

        return response()->json(['id' => $id], 201);
    }

    public function status($id)
    {
        $req = RequestModel::find($id);
        if (!$req) {
            return response()->json(['error' => 'Request not found'], 404);
        }

        return response()->json([
            'id' => $req->id,
            'status' => $req->status,
            'result' => $req->result,
        ]);
    }
}
