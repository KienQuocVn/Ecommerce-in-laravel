<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'photo',
        'address_line1',
        'address_line2',
        'country',
        'post_code',
        'status',
        'provider',
        'provider_id',
        'loyalty_points',
        'loyalty_tier',
        'total_spent',
        'total_orders',
        'last_order_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'total_spent' => 'decimal:2',
        'last_order_at' => 'datetime',
        'loyalty_points' => 'integer',
        'total_orders' => 'integer',
    ];

    public function hasCompleteProfile(): bool
    {
        return !empty($this->first_name)
            && !empty($this->last_name)
            && !empty($this->email)
            && !empty($this->phone)
            && !empty($this->address_line1);
    }

    public function needsProfileCompletion(): bool
    {
        return !$this->hasCompleteProfile();
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function shipperProfile()
    {
        return $this->hasOne(Shipper::class);
    }

    public function shipperReviews()
    {
        return $this->hasMany(ShipperReview::class, 'customer_id');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->withPivot(['source', 'obtained_at', 'expires_at', 'used_at', 'used_in_order_id'])
            ->withTimestamps();
    }

    public function availableCoupons()
    {
        return $this->coupons()
            ->whereNull('user_coupons.used_at')
            ->where(function ($query) {
                $query->whereNull('user_coupons.expires_at')
                    ->orWhere('user_coupons.expires_at', '>', now());
            })
            ->where('coupons.status', 'active');
    }
}
