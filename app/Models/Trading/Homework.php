<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;
    protected $fillable = ['symbol', 'timeframe', 'strategy', 'direction', 'title', 'description', 'additional_data'];
}
