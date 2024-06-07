<?php

namespace App\Console\Commands;

use App\Services\Exchange\ExchangeServiceFactory;
use Illuminate\Console\Command;

class ShowExchangeAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:account:show {mode=s : The mode of operation ("f" for futures, "s" for spot)} ' .
    '{symbol? : specify symbol to show leverage and margin type in futures mode} ' .
    '{asset=USDT : specify asset to show balance in futures mode}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show exchange account balance';

    public function __construct(
        protected readonly ExchangeServiceFactory $exchangeServiceFactory
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $mode = $this->argument('mode');
        $symbol = $this->argument('symbol');
        $asset = $this->argument('asset');
        if ($mode === 'f') {
            $exchangeService = $this->exchangeServiceFactory->create(true);
            $balance = $exchangeService->getFuturesBalance($asset);
            if ($symbol) {
                $marginAndLeverage = $exchangeService->getMarginTypeAndLeverage($symbol);
                $leverage = $marginAndLeverage['leverage'];
                $marginType = $marginAndLeverage['marginType'];
                $this->table(
                    ['Mode', 'Balance', 'Asset', 'Symbol', 'Leverage', 'Margin'],
                    [[$mode, $balance, $asset, $symbol, $leverage, $marginType]]
                );
            } else {
                $this->table(
                    ['Mode', 'Balance', 'Asset'],
                    [[$mode, $balance, $asset]]
                );
            }
        } else {
            $exchangeService = $this->exchangeServiceFactory->create(false);
            $balance = $exchangeService->getBalance();
            $this->table(
                ['Mode', 'Balance'],
                [[$mode, $balance]]
            );
        }
    }
}
