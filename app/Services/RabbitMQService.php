<?php
namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{

    public function sendRabbitMQ($exchange='default',$queue='default',$message='test'){
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, false, false, false);
//        $msg_text = "['array_key' => 'value from php']";
        $msg = new AMQPMessage($message);
        $abc = $channel->basic_publish($msg, '', $queue);
//        echo " [x] Sent 'ques test'\n";

        $channel->close();
        $connection->close();
        return true;
    }

    public function getLatLongMessage($queue = 'default')
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {

            echo ' [x] Received ', $msg->body, "\n";
        };

        $channel_response = $channel->basic_consume('hello', '', false, true, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
        return $channel_response;
    }

    public function getLatLngRequest($data = '')
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare('lat_lng', 'fanout', false, false, false);

        if (empty($data)) {
            $data = "info: Hello World!";
        }
        $msg = new AMQPMessage($data);

        $channel->basic_publish($msg, 'lat_lng');
        $channel->close();
        $connection->close();
    }

}
































?>
