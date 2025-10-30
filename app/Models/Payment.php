<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;
    
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        'id',
        'order_id',
        'provider',
        'status',
        'amount',
        'currency',
        'transaction_id',
        'gateway_event_id',
        'raw_payload'
    ];

    protected $casts = [
        'amount' => 'decimal:2', 
        'raw_payload' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}