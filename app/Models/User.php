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
        'name', 'email', 'password','role','photo','status','provider','provider_id','loyalty_points','loyalty_tier','total_spent','total_orders','last_order_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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

    public function orders(){
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
}
