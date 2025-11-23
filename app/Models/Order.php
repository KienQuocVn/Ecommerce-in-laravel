<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'sub_total',
        'quantity',
        'delivery_charge',
        'status',
        'total_amount',
        'first_name',
        'last_name',
        'address1',
        'phone',
        'email',
        'payment_method',
        'payment_status',
        'shipping_id',
        'coupon',
        'coupon_id'
    ];

    public function cart_info()
    {
        return $this->hasMany('App\Models\Cart', 'order_id', 'id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function items()
    {
        return $this->hasMany(Cart::class, 'order_id');
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class, 'shipping_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function delivery()
    {
        return $this->hasOne(OrderDelivery::class);
    }

    public function appliedCoupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function ensureDeliveryRecord(float $deliveryFee = 0): OrderDelivery
    {
        if ($this->delivery) {
            return $this->delivery;
        }

        return $this->delivery()->create([
            'delivery_fee' => $deliveryFee,
            'status' => OrderDelivery::STATUS_PENDING,
            'assignment_type' => 'self-claim',
        ]);
    }

    public function shipper()
    {
        return $this->hasOneThrough(Shipper::class, OrderDelivery::class, 'order_id', 'id', 'id', 'shipper_id');
    }

    public static function getAllOrder($id)
    {
        return Order::with('cart_info')->find($id);
    }

    public static function countActiveOrder()
    {
        $data = Order::count();
        return $data ?: 0;
    }
}
