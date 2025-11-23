<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'status', 'min_amount', 'max_amount', 'expires_at', 'usage_limit'];

    protected $casts = [
        'expires_at' => 'datetime',
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
    ];

    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }

    public function discount($total)
    {
        if ($this->type == "fixed") {
            return min($this->value, $total);
        } elseif ($this->type == "percent") {
            $discount = ($this->value / 100) * $total;
            if ($this->max_amount) {
                return min($discount, $this->max_amount);
            }
            return $discount;
        } else {
            return 0;
        }
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValidForAmount($amount)
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        if ($this->min_amount && $amount < $this->min_amount) {
            return false;
        }

        return true;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withPivot(['source', 'obtained_at', 'expires_at', 'used_at', 'used_in_order_id'])
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
