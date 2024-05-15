<?php

namespace App\Models\Exchange\Futures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    use HasFactory;
    protected $table = 'exchange_futures_tickers';
    public $timestamps = false;
    protected $fillable = [
        'price_change', 'price_change_percent', 'last_price', 'open', 'high', 'low',
        'volume', 'quote_volume', 'open_time', 'close_time'
    ];

    public static function convertSocketData(array $data): array
    {
        return [
            'symbol' => $data['s'] ?? null,
            'price_change_percent' => $data['P'] ?? null,
            'price_change' => $data['p'] ?? null,
            'last_price' => $data['c'] ?? null,
            'open' => $data['o'] ?? null,
            'high' => $data['h'] ?? null,
            'low' => $data['l'] ?? null,
            'volume' => $data['v'] ?? null,
            'quote_volume' => $data['q'] ?? null,
            'open_time' => $data['O'] ?? null,
            'close_time' => $data['C' ?? null]
        ];
    }

    public static function convertRestData(array $data): array
    {
        return [
            'symbol' => $data['symbol'],
            'price_change_percent' => $data['priceChangePercent'],
            'price_change' => $data['priceChange'],
            'last_price' => $data['lastPrice'],
            'open' => $data['openPrice'],
            'high' => $data['highPrice'],
            'low' => $data['lowPrice'],
            'volume' => $data['volume'],
            'quote_volume' => $data['quoteVolume'],
            'open_time' => $data['openTime'],
            'close_time' => $data['closeTime']
        ];
    }
}
