<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class OrderQueue extends Model
{
    protected $table = 'order_queues';

    protected $fillable = ['status', 'queued_orders_json', 'current_order_json', 'acknowledge_received', 'next_order_json'];

    protected $casts = [
        'queued_orders_json' => 'array',
        'current_order_json' => 'array',
        'acknowledge_received' => 'boolean',
        'next_order_json' => 'array',
    ];

    // Setter for queued_orders_json
    public function setQueuedOrders(array $orders)
    {
        $this->queued_orders_json = $orders;
        $this->save();
    }

    // Automatically save whenever a setter method is called
    public function __call($method, $parameters)
    {
        $result = parent::__call($method, $parameters);
        $this->save();
        return $result;
    }

    // Getter for all fields
    public function getStatusAttribute()
    {
        return $this->attributes['status'];
    }

    public function getQueuedOrdersAttribute()
    {
        return $this->attributes['queued_orders_json'];
    }

    public function getCurrentOrderAttribute()
    {
        return $this->attributes['current_order_json'];
    }

    public function getAcknowledgeReceivedAttribute()
    {
        return $this->attributes['acknowledge_received'];
    }

    public function getNextOrderAttribute()
    {
        return $this->attributes['next_order_json'];
    }

    // Method to retrieve all attributes as an array
    public function getData()
    {
        $this->refresh(); // Reload the model from the database
        return $this->attributesToArray();
    }
}
