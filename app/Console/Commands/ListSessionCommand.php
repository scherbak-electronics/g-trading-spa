<?php

namespace App\Console\Commands;

use App\Services\Trading\SessionService;
use Illuminate\Console\Command;

class ListSessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trading-session:list {user_id : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List trading sessions for a specific user';

    public function __construct(
        protected readonly SessionService $sessionService
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userId = $this->argument('user_id');
        $sessions = $this->sessionService->getUserSessions($userId);
        if ($sessions->isEmpty()) {
            return;
        } else {
            $tableData = $sessions->map(function ($session) {
                return [
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
                ];
            })->toArray();

            // Display the table
            $this->table(
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
    }
}
