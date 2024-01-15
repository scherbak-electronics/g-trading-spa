<?php

namespace App\Models\Exchange;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    use HasFactory;
    protected $table = 'exchange_tickers';
    public $timestamps = false;
}
