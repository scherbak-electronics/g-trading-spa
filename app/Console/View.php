<?php
namespace App\Console;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class View {
    protected Command $command;
    protected OutputInterface $output;

    public function __construct(Command $command, OutputInterface $output) {
        $this->command = $command;
        $this->output = $output;
    }

    public function render(array $data): void
    {
        $this->output->write("\033c");
        if ($data['mode']) {
            //$this->command->info($data['mode']);
            $this->output->write("\r{$data['mode']}\n");
        }
        if ($data['items_count']) {
            //$this->command->info("items count: " . $data['items_count']);
            $this->output->write("\ritems count: {$data['items_count']}\n");
        }
        if ($data['params']) {
            foreach ($data['params'] as $param => $value) {
                //$this->command->info($param . ": " . $value);
                $this->output->write("\r{$param}: {$value}\n");
            }
        }
    }

    public function session($session): void
    {
        $this->command->table(
            ['ID', 'Symbol', 'Qty', 'Leverage',  'Status', 'State'],
            [
                [$session->id, $session->symbol, $session->quantity, $session->leverage, $session->status, $session->state]
            ]
        );
    }
    protected function showBalance($balance): void
    {
        $this->command->info('your balance:');
//        [
//    {
//        "accountAlias": "SgsR",    // unique account code
//        "asset": "USDT",    // asset name
//        "balance": "122607.35137903", // wallet balance
//        "crossWalletBalance": "23.72469206", // crossed wallet balance
//        "crossUnPnl": "0.00000000"  // unrealized profit of crossed positions
//        "availableBalance": "23.72469206",       // available balance
//        "maxWithdrawAmount": "23.72469206",     // maximum amount for transfer out
//        "marginAvailable": true,    // whether the asset can be used as margin in Multi-Assets mode
//        "updateTime": 1617939110373
//    }
//]
    }
}
