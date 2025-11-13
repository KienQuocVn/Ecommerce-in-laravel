<?php

namespace App\Http\Controllers;

use App\Models\OrderDelivery;
use App\Models\ShipperReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryFeedbackController extends Controller
{
    public function store(Request $request, OrderDelivery $delivery)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'user') {
            return redirect()->route('login.form')->with('error', 'Vui lòng đăng nhập để đánh giá shipper.');
        }

        if ($delivery->order->user_id !== $user->id) {
            return back()->with('error', 'Bạn không thể đánh giá đơn hàng của người khác.');
        }

        if ($delivery->status !== OrderDelivery::STATUS_COMPLETED || !$delivery->shipper) {
            return back()->with('error', 'Đơn hàng chưa hoàn tất để đánh giá.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'is_liked' => 'nullable|boolean',
            'tip_amount' => 'nullable|numeric|min:0',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = ShipperReview::updateOrCreate(
            [
                'delivery_id' => $delivery->id,
                'customer_id' => $user->id,
            ],
            [
                'order_id' => $delivery->order_id,
                'shipper_id' => $delivery->shipper_id,
                'rating' => $validated['rating'],
                'is_liked' => $request->boolean('is_liked'),
                'tip_amount' => $validated['tip_amount'] ?? 0,
                'comment' => $validated['comment'] ?? null,
            ]
        );

        $delivery->tip_amount = $review->tip_amount;
        $delivery->save();

        $shipper = $delivery->shipper;
        $shipper->average_rating = round($shipper->reviews()->avg('rating'), 2);
        $shipper->adjustTrustScore(($review->rating - 3) * 0.1 + ($review->is_liked ? 0.1 : 0));

        return back()->with('success', 'Cảm ơn bạn đã đánh giá shipper!');
    }
}
