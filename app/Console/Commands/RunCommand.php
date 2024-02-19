<?php

namespace App\Console\Commands;

use Binance\Websocket\Spot;
use Illuminate\Console\Command;
use React\EventLoop\Loop;
use React\Socket\Connector;

class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run application asynchronous processes';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $loop = Loop::get();

        $reactConnector = new Connector($loop);
        $connector = new \Ratchet\Client\Connector($loop, $reactConnector);
        $client = new Spot(['wsConnector' => $connector]);

        $callbacks = [
            'message' => function($conn, $msg){
                $this->line('message received');
                $this->line($msg);
            },
            'pong' => function($conn) {
                $this->line("received pong from server");
            },
            'ping' => function($conn) {
                $this->line("received ping from server");
            },
            'close' => function($conn) {
                $this->line("receive closed.");
            }
        ];

        $client->miniTicker($callbacks, 'btcusdt');
        // $client->

        $loop->addPeriodicTimer(2, function () use ($client) {
            $client->ping();
            $this->line("ping sent ");
        });

        $loop->run();
    }
}
