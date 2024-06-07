<?php

namespace App\Console\Commands;

use App\Console\ViewFactory;
use App\Services\Exchange\ExchangeService;
use App\Services\Exchange\ExchangeServiceFactory;
use App\Services\Trading\SessionService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class UpdateSessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trading-session:update ' .
    '{--id= : Session identifier} ' .
    '{--symbol= : Trading pair symbol} ' .
    '{--state= : State (wait_for_entry_point | position_opened | position_closed)} ' .
    '{--entry_point_price= : Entry point price} ' .
    '{--take_profit_price= : Take profit price} ' .
    '{--stop_loss_price= : Stop loss price} ' .
    '{--stop_loss_safe_time= : Stop loss safe time} ' .
    '{--default_timeframe= : Default timeframe} ' .
    '{--side= : Side (SHORT | LONG)} ' .
    '{--is_futures= : Is futures} ' .
    '{--leverage= : Leverage} ' .
    '{--marginType= : Margin type (ISOLATED | CROSSED)} ' .
    '{--quantity= : Quantity} ' .
    '{--quantity_percent= : Quantity as percent of balance} ' .
    '{--status= : Status (new | active | running | stopped | completed)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update session by id';
    protected ExchangeService $exchangeService;

    public function __construct(
        protected readonly SessionService $sessionService,
        protected readonly ExchangeServiceFactory $exchangeServiceFactory,
        protected readonly ViewFactory $viewFactory
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $sessionId = (int)$this->option('id');
        if (empty($sessionId)) {
            return;
        }
        $session = $this->sessionService->getSession($sessionId);

        if (!$session) {
            $this->error('Session not found.');
            return;
        }

        $options = $this->options();
        $options['is_futures'] = $this->filterBooleanOption($options['is_futures']);
        if(!$this->validate($options)) {
            return;
        }

        // Update each model property if it is provided and not empty
//        foreach ($options as $key => $value) {
//            if (!empty($value) && $key !== 'id' && $key !== 'quantity_percent') {
//                $session->$key = $value;
//            }
//        }
        $this->setSessionData($session, $options);
        if ($session->is_futures) {
            $this->exchangeService = $this->exchangeServiceFactory->create(true);
        } else {
            $this->exchangeService = $this->exchangeServiceFactory->create(false);
        }
        $this->updateLeverageAndMargin($session, $options);
        if (!empty($options['quantity_percent'])) {
            try {
                $this->updateQtyByPercentOfBalance($session, (int)$options['quantity_percent']);
            } catch (Exception $e) {
                $this->error($e->getMessage());
                return;
            }
        }
        $session->save();

        $this->info('Session updated successfully.');
        $session->refresh();
        $view = $this->viewFactory->create($this, $this->output);
        $view->showSessionTable($session);
    }

    private function updateLeverageAndMargin($session, $options): void
    {
        if (empty($session->symbol)) {
            return;
        }
        $this->info('Retrieving leverage and margin type from exchange...');
        $marginAndLeverage = $this->exchangeService->getMarginTypeAndLeverage($session->symbol);
        $this->info('Leverage: ' . $marginAndLeverage['leverage']);
        $this->info('Margin: ' . $marginAndLeverage['marginType']);
        if (!empty($options['leverage']) || !empty($options['marginType'])) {
            if (!empty($options['leverage']) && ($options['leverage'] != $marginAndLeverage['leverage'])) {
                $session->leverage = $options['leverage'];
                $this->info('Update leverage to ' . $session->leverage);
                $this->exchangeService->setFuturesLeverage($session->symbol, $session->leverage);
            }
            if (!empty($options['marginType']) && ($options['marginType'] != $marginAndLeverage['marginType'])) {
                $session->marginType = $options['marginType'];
                $this->info('Update margin to ' . $session->marginType);
                $this->exchangeService->setFuturesMarginType($session->symbol, $session->marginType);
            }
        } else {
            if (empty($session->leverage) && !empty($marginAndLeverage['leverage'])) {
                $session->leverage = $marginAndLeverage['leverage'];
            }
            if (empty($session->marginType) && !empty($marginAndLeverage['marginType'])) {
                $session->marginType = $marginAndLeverage['marginType'];
            }
        }
    }


    private function updateQtyByPercentOfBalance(&$session, $qtyPercent): void
    {
        if (empty($session->symbol)) {
            return;
        }
        if ($session->is_futures) {
            $balance = $this->exchangeService->getFuturesBalance();
            if ($balance) {
                $qty = $this->exchangeService->calculateOrderQuantity($session->symbol, $balance, $qtyPercent);
                if (!$this->exchangeService->validateQuantity($session->symbol, $qty)) {
                    throw new Exception('Invalid quantity.');
                }
                $this->info('Futures balance: ' . $balance);
                $this->info('Calculated quantity: ' . $qty);
                $session->quantity = $qty;
            } else {
                $this->warn('You have no balance');
            }
        } else {
            $this->warn('Spot not implemented yet');
        }
    }

    private function getValidationRules(): array
    {
        return [
            'symbol' => 'nullable|string',
            'state' => 'nullable|string|in:wait_for_entry_point,position_opened,position_closed,stop_loss_triggered',
            'entry_point_price' => 'nullable|numeric',
            'take_profit_price' => 'nullable|numeric',
            'stop_loss_price' => 'nullable|numeric',
            'stop_loss_safe_time' => 'nullable|integer',
            'default_timeframe' => 'nullable|string',
            'side' => 'nullable|string|in:SHORT,LONG',
            'is_futures' => 'nullable|boolean',
            'leverage' => 'nullable|integer',
            'marginType' => 'nullable|string|in:ISOLATED,CROSSED',
            'quantity' => 'nullable|numeric',
            'quantity_percent' => 'nullable|numeric',
            'status' => 'nullable|string|in:new,active,running,stopped,completed',
        ];
    }

    private function validate($options): bool
    {
        $validator = Validator::make($options, $this->getValidationRules());

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return false;
        }
        return true;
    }

    private function setSessionData($session, $options): void
    {
        $validationRules = $this->getValidationRules();

        foreach ($options as $key => $value) {
            if (array_key_exists($key, $validationRules) && !empty($value) && $key !== 'id' && $key !== 'quantity_percent') {
                $session->{$key} = $this->castAttribute($key, $value);
            }
        }
    }

    private function castAttribute(string $key, $value)
    {
        $rules = $this->getValidationRules()[$key];

        if (str_contains($rules, 'integer')) {
            return (int) $value;
        } elseif (str_contains($rules, 'numeric')) {
            return (float) $value;
        } elseif (str_contains($rules, 'boolean')) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        return $value;
    }

    private function filterBooleanOption($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null ?
            filter_var($value, FILTER_VALIDATE_BOOLEAN) : false;
    }
}
