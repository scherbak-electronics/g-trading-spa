<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'homework_id',
        'symbol',
        'strategy_code',
        'total_investment',
        'total_profit',
        'state',
        'current_price',
        'main_level_price',
        'entry_point_price',
        'take_profit_price',
        'take_profit_timeout',
        'stop_loss_price',
        'trailing_delta',
        'stop_loss_safe_time',
        'default_timeframe',
        'small_timeframe',
        'big_timeframe',
        'current_timeframe',
        'side',
        'is_futures',
        'leverage',
        'marginType',
        'quantity',
        'status'
    ];
    protected $casts = [
        'current_price' => 'float',
        'total_investment' => 'float',
        'total_profit' => 'float',
        'main_level_price' => 'float',
        'entry_point_price' => 'float',
        'take_profit_price' => 'float',
        'stop_loss_price' => 'float',
        'trailing_delta' => 'float',
        'quantity' => 'float'
    ];
    protected $table = 'sessions';

    public function getMode(): string
    {
        return $this->is_futures ? 'f' : 's';
    }
}
