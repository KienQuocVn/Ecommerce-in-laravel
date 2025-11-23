<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDelivery extends Model
{
    protected $fillable = [
        'order_id',
        'shipper_id',
        'status',
        'assignment_type',
        'delivery_fee',
        'tip_amount',
        'assigned_at',
        'accepted_at',
        'picked_up_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
        'notes',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'tip_amount' => 'decimal:2',
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ShipperReview::class, 'delivery_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('shipper_id')
            ->where('status', self::STATUS_PENDING)
            ->whereHas('order', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('payment_method', 'cod')
                        ->orWhere('payment_status', 'paid');
                });
            });
    }

    public function markAccepted(): void
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->accepted_at = now();
        $this->save();
    }

    public function markInTransit(): void
    {
        $this->status = self::STATUS_IN_TRANSIT;
        $this->picked_up_at = now();
        $this->save();
    }

    public function markCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->save();
    }

    public function markCancelled(?string $reason = null): void
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();
        $this->cancel_reason = $reason;
        $this->save();
    }
}
