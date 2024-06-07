<?php

namespace App\Console\Commands;

use App\Services\Exchange\ExchangeServiceFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SetExchangeAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:account:set ' .
        '{--symbol= : Exchange symbol} ' .
        '{--leverage= : The leverage for the account} ' .
        '{--margin= : The margin type for the account ("i" for isolated, "c" for cross)} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set futures account leverage and margin type';

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
        $symbol = $this->option('symbol');
        $leverage = $this->option('leverage');
        $margin = $this->option('margin');

        if (($leverage || $margin) && $symbol) {
            $exchangeService = $this->exchangeServiceFactory->create(true);
            if ($leverage) {
                $this->info('Setting leverage to ' . $leverage);
                $exchangeService->setFuturesLeverage($symbol, $leverage);
            }
            if ($margin) {
                $marginType = ($margin === 'i' ? 'ISOLATED' : 'CROSSED');
                $this->info('Setting margin to ' . $marginType);
                $exchangeService->setFuturesMarginType($symbol, $marginType);
            }
            $marginAndLeverage = $exchangeService->getMarginTypeAndLeverage($symbol);
            $leverage = $marginAndLeverage['leverage'];
            $marginType = $marginAndLeverage['marginType'];
            $this->table(
                ['Symbol', 'Leverage', 'Margin'],
                [[$symbol, $leverage, $marginType]]
            );
            return;
        }
        $this->info('Please specify at least one option, --leverage or --margin');
    }
}
