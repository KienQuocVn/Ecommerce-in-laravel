<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipperReview extends Model
{
    protected $fillable = [
        'delivery_id',
        'order_id',
        'shipper_id',
        'customer_id',
        'rating',
        'is_liked',
        'tip_amount',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_liked' => 'boolean',
        'tip_amount' => 'decimal:2',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(OrderDelivery::class);
    }

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
