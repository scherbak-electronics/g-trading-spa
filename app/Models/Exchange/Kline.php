<?php

namespace App\Models\Exchange;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kline extends Model
{
    use HasFactory;
    protected $table = 'exchange_klines';
    public $timestamps = false;
}
