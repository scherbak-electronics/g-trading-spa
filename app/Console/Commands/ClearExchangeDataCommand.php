<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearExchangeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-exchange-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all records from exchange_ tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('exchange_klines')->truncate();
        $this->comment('exchange_klines done.');
        DB::table('exchange_orders')->truncate();
        $this->comment('exchange_orders done.');
        DB::table('exchange_symbols')->truncate();
        $this->comment('exchange_symbols done.');
        DB::table('exchange_tickers')->truncate();
        $this->comment('exchange_tickers done.');
    }
}
