<?php

namespace App\Models\Trading;

use App\Models\Exchange\Order;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderQueueItem extends Model
{
    use HasFactory;
    use Searchable;
    protected $table = 'order_queue_items';
    public $timestamps = false;
    protected array $searchFields = ['exchange_order_id'];
    protected $fillable = ['exchange_order_id', 'status', 'time_ms', 'session_id'];

    public function getOrder(): Order
    {
        $order = Order::find($this->exchange_order_id);
        return $order;
    }
}
