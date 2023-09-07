<?php

namespace App\Models\Trading;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeSymbols extends Model
{
    use HasFactory;
    use Searchable;
    public $timestamps = false;
    protected array $searchFields = ['symbol'];
}
