<?php

namespace App\Models\Exchange\Local;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $table = 'exchange_states';
    public $timestamps = false;
}
