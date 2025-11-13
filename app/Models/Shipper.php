<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipper extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'vehicle_type',
        'vehicle_plate',
        'trust_score',
        'completed_deliveries',
        'cancelled_deliveries',
        'average_rating',
        'is_available',
        'bio',
        'metadata',
    ];

    protected $casts = [
        'trust_score' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_available' => 'boolean',
        'completed_deliveries' => 'integer',
        'cancelled_deliveries' => 'integer',
        'metadata' => AsArrayObject::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(OrderDelivery::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ShipperReview::class);
    }

    public function incrementCompletedDeliveries(): void
    {
        $this->completed_deliveries++;
        $this->save();
    }

    public function incrementCancelledDeliveries(): void
    {
        $this->cancelled_deliveries++;
        $this->save();
    }

    public function adjustTrustScore(float $delta): void
    {
        $newScore = max(0, min(10, (float) $this->trust_score + $delta));
        $this->trust_score = round($newScore, 2);
        $this->save();
    }
}
