<?php

namespace App\Console\Commands;

use App\Interfaces\RiderRepositoryInterface;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class UpdateLatLng extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:updatelatlng';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var RiderRepositoryInterface
     */
    private RiderRepositoryInterface $rider_repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RiderRepositoryInterface $rider_repository)
    {
        parent::__construct();
        $this->rider_repository = $rider_repository;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $queue_name = "update_rider_lat_long_queue";
        $channel->queue_declare($queue_name, false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            $response_data = json_decode($msg->body, true);
            if (
                isset($response_data['uuid'])
                && isset($response_data['lat'])
                && isset($response_data['long'])
                && !empty($response_data['uuid'])
                && !empty($response_data['lat'])
                && !empty($response_data['long'])
            ) {
                $rider = $this->rider_repository->getData(
                    [
                        ['uuid', '=', $response_data['uuid']]
                    ]
                )->first();
                if ($rider) {
                    $this->rider_repository->updateRider(
                        $rider,
                        [
                            'lat' => $response_data['lat'],
                            'long' => $response_data['long']
                        ]
                    );
                    if(!$rider->ride_available_status) {
                        $this->rider_repository->addRiderLocationLogs([
                            'rider_id' => $rider->id,
                            'lat' => $response_data['lat'],
                            'long' => $response_data['long']
                        ]);
                    }
                }
            }
            echo ' [x] Received ', $msg->body, "\n";
        };

        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);
        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
