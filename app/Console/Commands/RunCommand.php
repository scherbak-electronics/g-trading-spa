<?php

namespace App\Console\Commands;

use App\Models\AppLogic;
use Binance\Websocket\Spot;
use Illuminate\Console\Command;
use React\EventLoop\Loop;
use React\Socket\Connector;

class RunCommand extends Command
{
    protected $signature = 'app:run';
    protected $description = 'Run application asynchronous processes';

    public function __construct(protected AppLogic $appLogic)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $loop = Loop::get();

        $reactConnector = new Connector($loop);
        $connector = new \Ratchet\Client\Connector($loop, $reactConnector);
        $client = new Spot(['wsConnector' => $connector]);

        $callbacks = [
            'message' => function($conn, $msg){
                $msgArray = json_decode($msg, true);
                $itemsCount = count($msgArray);
                $result = $this->appLogic->handleSocketMessage($msgArray);
                $this->output->write("\033c");
                $this->info("items count: " . $itemsCount);
                foreach ($result as $param => $value) {
                    $this->info($param . ": " . $value);
                }
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
        $client->ticker($callbacks);
        $loop->run();
    }
}
