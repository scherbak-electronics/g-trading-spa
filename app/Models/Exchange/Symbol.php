<?php

namespace App\Models\Exchange;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symbol extends Model
{
    use HasFactory;
    use Searchable;
    protected $table = 'exchange_symbols';
    public $timestamps = false;
    protected array $searchFields = ['symbol'];
}
