<?php

namespace App\Console\Commands;

use App\Console\View;
use App\Console\ViewFactory;
use App\Models\AppLogic;
use App\Models\Exchange\Order;
use App\Models\Trading\Logic;
use App\Models\Trading\LogicFactory;
use App\Models\Trading\Session;
use App\Models\User;
use App\Services\Exchange\ExchangeService;
use App\Services\Exchange\ExchangeServiceFactory;
use App\Services\Trading\SessionService;
use Binance\Websocket\Futures;
use Binance\Websocket\Spot;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use React\EventLoop\Loop;
use React\Socket\Connector;
use React\Stream\ReadableResourceStream;

class RunCommand extends Command
{
    protected $signature = 'app:run {mode=s : The mode of operation ("f" for futures, "s" for spot)}';
    protected $description = 'Run application asynchronous processes. ';
    protected View $view;
    protected ExchangeService $exchangeService;
    protected SessionService $sessionService;
    public function __construct(
        protected LogicFactory $logicFactory,
        protected ViewFactory $viewFactory
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $userId = $this->selectUser();
        if ($userId === 0) {
            return;
        }
        //$mode = $this->argument('mode');
        $mode = $this->choice(
            'Select trading mode',
            ['f' => 'Futures', 's' => 'Spot'],
            's'
        );
        $this->info("Mode selected: " . ($mode === 'f' ? 'Futures' : 'Spot'));

        $this->view = $this->createView();
        $logic = $this->logicFactory->create($userId, $mode, $this->view);

        $this->exchangeService = $logic->getExchangeService();
        $this->sessionService = $logic->getSessionService();

        $session = $this->setUpSession($userId, $mode);


        if (!$this->confirm('Run session?')) {
            return;
        }

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
        if ($mode === 'f') {
            $client = new Futures(['wsConnector' => $connector]);
        } else {
            $client = new Spot(['wsConnector' => $connector]);
        }

        $callbacks = [
            'message' => function($conn, $eventJson) use (&$logic, $mode) {
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

    private function setUpFutures($symbol): array
    {
        // Interactive futures trading setup
        $balance = $this->exchangeService->getFuturesBalance();
        $this->info("Your futures wallet balance is: $" . $balance);

        $marginAndLeverage = $this->exchangeService->getMarginTypeAndLeverage($symbol);
        $leverage = $marginAndLeverage['leverage'];
        $marginType = $marginAndLeverage['marginType'];
        $this->info("Your margin type is: " . $marginType);
        $this->info("Your leverage is: " . $leverage);
        if ($this->confirm('Do you want to change margin and leverage?')) {
            $leverage = $this->ask('Enter the leverage (ex. 20x)');
            $this->exchangeService->setFuturesLeverage($symbol, $leverage);
            $marginType = $this->choice('Enter margin type', [
                1 => 'ISOLATED',
                2 => 'CROSSED'
            ]);
            $this->exchangeService->setFuturesMarginType($symbol, $marginType);

            $marginAndLeverage = $this->exchangeService->getMarginTypeAndLeverage($symbol);
            $leverage = $marginAndLeverage['leverage'];
            $marginType = $marginAndLeverage['marginType'];
            $this->info("Your margin type is: " . $marginType);
            $this->info("Your leverage is: " . $leverage);
        }


        $percent = $this->ask('Enter the percentage of your balance (' . $balance . ') you want to spend');

        $quantity = $this->exchangeService->calculateOrderQuantity($symbol, $balance, $percent);
        $this->info("You can buy approximately {$quantity} of {$symbol}");

        return [
            'symbol' => $symbol,
            'leverage' => $leverage,
            'quantity' => $quantity,
            'marginType' => $marginType
        ];
    }

    private function setUpSpot($symbol): array
    {
        return [];
    }

    private function setUpSession($userId, $mode): ?Session
    {
        $symbol = $this->ask('Enter the futures pair you want to trade, e.g., BTCUSDT');
        if ($mode === 'f') {
            $setup = $this->setUpFutures($symbol);
            $setup['is_futures'] = true;
        } else {
            $setup = $this->setUpSpot($symbol);
            $setup['is_futures'] = false;
        }

        $setup['user_id'] = $userId;

        if ($this->confirm('Create new session?')) {
            return $this->setUpNewSession($setup);
        } else {

            return $this->setUpExistingSession($setup);
        }
    }

    private function setUpNewSession($data): ?Session
    {
        return $this->sessionService->create($data);
    }

    private function setUpExistingSession($data): ?Session
    {
        $sessions = $this->sessionService->getUserSessions($data['user_id']);
        if ($sessions->isEmpty()) {
            return null;
        } else {
            // Prepare sessions for display
            $options = $sessions->mapWithKeys(function ($session) {
                return [$session->id => "{$session->id} | {$session->symbol} | {$session->side}"];
            })->toArray();

            // Ask user to select a session
            $sessionId = array_search($this->choice('Please select a session:', $options), $options);
            $session = $this->sessionService->getSession($sessionId);

            if ($session) {
                $session->symbol = $data['symbol'];
                $session->leverage = $data['leverage'];
                $session->quantity = $data['quantity'];
                $session->marginType = $data['marginType'];
                $session->save();
                return $session;
            }
        }
        return null;
    }

    private function selectUser(): int|array|string
    {
        // Fetch all users from the database
        $users = User::all();

        // Check if there are any users
        if ($users->isEmpty()) {
            $this->error('No users found.');
            return 0;
        }

        // Create an array of usernames for the choice method
        $options = $users->pluck('email', 'id')->toArray();

        // Ask the user to select a user
        $selectedEmail = $this->choice('Please select a user:', $options);
        $selectedUserId = array_search($selectedEmail, $options);
        // Output the selected user
        $this->info("You have selected: " . $selectedEmail);
        return $selectedUserId;
    }

    private function createView(): View
    {
        return $this->viewFactory->create($this, $this->output);
    }
}
