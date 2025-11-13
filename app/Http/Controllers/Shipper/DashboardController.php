<?php

namespace App\Http\Controllers\Shipper;

use App\Http\Controllers\Controller;
use App\Models\OrderDelivery;
use App\Models\Shipper;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $shipper = Shipper::with(['deliveries' => function ($query) {
            $query->latest()->limit(5)->with('order');
        }])->where('user_id', Auth::id())->firstOrFail();

        $stats = [
            'pending' => $shipper->deliveries()->where('status', OrderDelivery::STATUS_PENDING)->count(),
            'in_progress' => $shipper->deliveries()->whereIn('status', [OrderDelivery::STATUS_ACCEPTED, OrderDelivery::STATUS_IN_TRANSIT])->count(),
            'completed' => $shipper->deliveries()->where('status', OrderDelivery::STATUS_COMPLETED)->count(),
            'cancelled' => $shipper->deliveries()->where('status', OrderDelivery::STATUS_CANCELLED)->count(),
            'available_pool' => OrderDelivery::available()->count(),
        ];

        $recentDeliveries = $shipper->deliveries->take(5);

        return view('shipper.dashboard', compact('shipper', 'stats', 'recentDeliveries'));
    }
}
