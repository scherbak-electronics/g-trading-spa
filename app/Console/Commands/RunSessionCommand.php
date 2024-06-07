<?php

namespace App\Console\Commands;

use App\Console\View;
use App\Console\ViewFactory;
use App\Models\Trading\LogicFactory;
use App\Services\Exchange\ExchangeService;
use App\Services\Trading\SessionService;
use Binance\Websocket\Futures;
use Binance\Websocket\Spot;
use Illuminate\Console\Command;
use React\EventLoop\Loop;
use React\Socket\Connector;
use React\Stream\ReadableResourceStream;

class RunSessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trading-session:run {session_id : Trading session identifier. Run trading-session:list to see all user trading sessions.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run trading session by session ID';
    protected View $view;
    protected ExchangeService $exchangeService;

    public function __construct(
        protected readonly SessionService $sessionService,
        protected LogicFactory $logicFactory,
        protected ViewFactory $viewFactory
    )
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $sessionId = $this->argument('session_id');
        $session = $this->sessionService->getSession($sessionId);
        if (empty($session)) {
            $this->error('There is no session ID=' . $sessionId);
            return;
        }
        $this->view = $this->createView();
        $logic = $this->logicFactory->create(
            $session->user_id,
            $session->getMode(),
            $this->view
        );
        $logic->setSessionId($sessionId);
        $logic->setIsMarketOrdersOnly(true);
        $this->exchangeService = $logic->getExchangeService();

        $listenKey = $this->exchangeService->getListenKey();
        if (is_array($listenKey) && $listenKey['listenKey']) {
            $listenKey = $listenKey['listenKey'];
        }
        $this->info("Listen Key:");
        $this->info(print_r($listenKey, true));
        $loop = Loop::get();

        // Set STDIN to non-blocking
        stream_set_blocking(STDIN, false);

        // Set terminal to raw mode
        shell_exec('stty raw -echo');

        $stdin = new ReadableResourceStream(STDIN, $loop);
        $stdin->on('data', function ($data) use (&$loop) {

            // Raw mode will include line returns
            $data = rtrim($data, "\n");

            if ($data === 'q') { // Let's say 'q' to quit
                //$this->info('Quitting...');
                $this->output->write("\rQuitting...\n");
                $loop->stop();

                // Reset terminal settings
                shell_exec('stty sane');
            } else {
                //$this->info("You typed: {$data}");
                $this->output->write("\rYou typed: {$data}\n");
            }
        });


        $reactConnector = new Connector($loop);
        $connector = new \Ratchet\Client\Connector($loop, $reactConnector);
        if ($session->getMode() === 'f') {
            $client = new Futures(['wsConnector' => $connector]);
        } else {
            $client = new Spot(['wsConnector' => $connector]);
        }

        $callbacks = [
            'message' => function($conn, $eventJson) use (&$logic) {
                $event = json_decode($eventJson, true);
                $logic->socketEventHandler($event);
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
        $client->userData($listenKey, $callbacks);
        $loop->run();

        // Make sure to reset terminal settings if loop exits unexpectedly
        shell_exec('stty sane');
    }

    private function createView(): View
    {
        return $this->viewFactory->create($this, $this->output);
    }
}
