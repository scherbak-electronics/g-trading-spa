<?php
namespace App\Console;
use App\Models\Trading\Session;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class View {
    protected Command $command;
    protected OutputInterface $output;
    protected array $data;
    protected array $sessionData;
    protected Session $session;

    public function __construct(Command $command, OutputInterface $output) {
        $this->command = $command;
        $this->output = $output;
    }

    public function render(): void
    {
        $this->output->write("\033c");
        foreach ($this->data as $param => $value) {
            $this->output->write("\r{$param}: {$value}\n");
        }

        $this->output->write("\r\n");
        $this->renderSessionTable();
        $this->output->write("\r\n");
    }

    public function setData(string $key, $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function setSession(Session &$session): static
    {
        $this->session = $session;
        return $this;
    }

    public function showSessionTable($session): void
    {
        $tableData = [[
            'ID' => $session->id,
            'Symbol' => $session->symbol,
            'Side' => $session->side,
            'Quantity' => $session->quantity,
            'Entry Point' => $session->entry_point_price,
            'Take Profit' => $session->take_profit_price,
            'Stop Loss' => $session->stop_loss_price,
            'Leverage' => $session->leverage,
            'Status' => $session->status,
            'State' => $session->state,
        ]];


        // Display the table
        $this->command->table(
            [
                'ID',
                'Symbol',
                'Side',
                'Quantity',
                'Entry Point',
                'Take Profit',
                'Stop Loss',
                'Leverage',
                'Status',
                'State',
            ],
            $tableData
        );
    }

    protected function renderSessionTable(): void
    {
        $this->renderTable(
            ['ID', 'Symbol', 'Side', 'Quantity', 'Price', 'Entry Point', 'Take Profit', 'Stop Loss', 'Leverage', 'Status', 'State'],
            [
                $this->session->id,
                $this->session->symbol,
                $this->session->side,
                $this->session->quantity,
                $this->session->current_price,
                $this->session->entry_point_price,
                $this->session->take_profit_price,
                $this->session->stop_loss_price,
                $this->session->leverage,
                $this->session->status,
                $this->session->state
            ]
        );
    }
    protected function renderTable($headers, $data): void
    {
        // Initialize widths array
        $widths = array_map('strlen', $headers); // Set initial widths based on header lengths

        // Adjust column widths based on data
        foreach ($data as $index => $value) {
            $length = strlen((string) $value);
            if ($length > $widths[$index]) {
                $widths[$index] = $length;
            }
        }

        // Build header and separator lines
        $headerLine = '';
        $separatorLine = '';
        foreach ($headers as $index => $header) {
            $headerLine .= str_pad($header, $widths[$index]) . ' | ';
            $separatorLine .= str_repeat('-', $widths[$index]) . '-+-';
        }

        // Remove extra characters from the end of the separator line
        $separatorLine = substr($separatorLine, 0, -2);

        // Write headers and separator
        $this->output->write("\r" . $separatorLine . "\n");
        $this->output->write("\r" . $headerLine . "\n");
        $this->output->write("\r" . $separatorLine . "\n");

        // Format and write row data using dynamic widths
        $rowData = '';
        foreach ($data as $index => $value) {
            $rowData .= str_pad($value, $widths[$index]) . ' | ';
        }

        // Write the row data
        $this->output->write("\r" . $rowData . "\n");
        $this->output->write("\r" . $separatorLine . "\n");
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
