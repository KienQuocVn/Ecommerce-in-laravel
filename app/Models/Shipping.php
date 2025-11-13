<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'type',
        'service_level',
        'delivery_zone',
        'price',
        'pricing_strategy',
        'percentage_rate',
        'min_cart_total',
        'max_cart_total',
        'estimated_time',
        'supports_cod',
        'is_recommended',
        'description',
        'priority',
        'status',
        'code',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'percentage_rate' => 'decimal:2',
        'min_cart_total' => 'decimal:2',
        'max_cart_total' => 'decimal:2',
        'supports_cod' => 'boolean',
        'is_recommended' => 'boolean',
        'priority' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailableForTotal($query, float $cartTotal)
    {
        return $query
            ->where(function ($q) use ($cartTotal) {
                $q->whereNull('min_cart_total')
                    ->orWhere('min_cart_total', '<=', $cartTotal);
            })
            ->where(function ($q) use ($cartTotal) {
                $q->whereNull('max_cart_total')
                    ->orWhere('max_cart_total', '>=', $cartTotal);
            });
    }

    public function calculateCost(float $cartTotal): float
    {
        $strategy = $this->pricing_strategy ?? 'flat';
        $basePrice = (float) $this->price;
        $rate = (float) $this->percentage_rate;

        return match ($strategy) {
            'percentage' => round($cartTotal * ($rate / 100), 2),
            'mixed' => round($basePrice + ($cartTotal * ($rate / 100)), 2),
            default => round($basePrice, 2),
        };
    }

    public function isAvailableForCart(float $cartTotal): bool
    {
        if (!is_null($this->min_cart_total) && $cartTotal < (float) $this->min_cart_total) {
            return false;
        }

        if (!is_null($this->max_cart_total) && $cartTotal > (float) $this->max_cart_total) {
            return false;
        }

        return true;
    }
}
