<?php

namespace App\Models\Exchange;

use App\Contracts\Exchange\KlineInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kline extends Model implements KlineInterface
{
    use HasFactory;
    protected $table = 'exchange_klines';
    public $timestamps = false;
}
