<?php

namespace App\Models\Exchange;

use App\Utilities\Data;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveSymbol extends Model
{
    use HasFactory;
    protected $table = 'active_symbols';
    protected $fillable = [
        'symbol',
        'using_by',
        'last_update_time'
    ];
    public $timestamps = false;

    public static function getActiveSymbols(): array
    {
        return self::query()
        ->select('*')
            ->get()
            ->toArray();
    }

    public static function setActiveSymbol(string $symbol): void
    {
        self::query()->upsert([
            [
                'symbol' => $symbol,
                'last_update_time' => Data::getTimestampInMilliseconds()
            ]
        ], ['symbol']);
    }
}
