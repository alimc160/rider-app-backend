<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class UpdateLatLngRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:updatelatlngrequest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $queue_name = 'update_rider_lat_long_queue';
        $channel->queue_declare($queue_name, false, false, false, false);
        $msg = new AMQPMessage('update your Location');
        $channel->basic_publish($msg, '', $queue_name);
        echo " [x] Sent 'update your Location'\n";
        $channel->close();
        $connection->close();
    }
}
