<?php

namespace App\Http\Controllers\Shipper;

use App\Http\Controllers\Controller;
use App\Models\OrderDelivery;
use App\Models\Shipper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoyaltyService;

class DeliveryController extends Controller
{
    public function index()
    {
        $shipper = Shipper::where('user_id', Auth::id())->firstOrFail();

        $assignedDeliveries = $shipper->deliveries()
            ->with(['order' => function ($query) {
                $query->select('id', 'order_number', 'status', 'total_amount', 'delivery_charge', 'first_name', 'last_name', 'address1', 'phone');
            }])
            ->latest()
            ->paginate(10, ['*'], 'assigned_page');

        $availableDeliveries = OrderDelivery::available()
            ->with(['order' => function ($query) {
                $query->select('id', 'order_number', 'status', 'total_amount', 'delivery_charge', 'first_name', 'last_name', 'address1', 'phone');
            }])
            ->latest()
            ->paginate(10, ['*'], 'available_page');

        return view('shipper.deliveries.index', compact('shipper', 'assignedDeliveries', 'availableDeliveries'));
    }

    public function accept(OrderDelivery $delivery): RedirectResponse
    {
        $shipper = Shipper::where('user_id', Auth::id())->firstOrFail();

        if ($delivery->status !== OrderDelivery::STATUS_PENDING || ($delivery->shipper_id && $delivery->shipper_id !== $shipper->id)) {
            return back()->with('error', 'Đơn giao không còn khả dụng để nhận.');
        }

        $delivery->shipper()->associate($shipper);
        $delivery->assignment_type = 'self-claim';
        $delivery->assigned_at = now();
        if ($delivery->delivery_fee == 0 && $delivery->order) {
            $delivery->delivery_fee = $delivery->order->delivery_charge ?? 0;
        }
        $delivery->markAccepted();

        return back()->with('success', 'Bạn đã nhận đơn giao hàng thành công.');
    }

    public function progress(OrderDelivery $delivery): RedirectResponse
    {
        $shipper = Shipper::where('user_id', Auth::id())->firstOrFail();
        if ($delivery->shipper_id !== $shipper->id) {
            return back()->with('error', 'Bạn không có quyền cập nhật đơn giao này.');
        }

        if (!in_array($delivery->status, [OrderDelivery::STATUS_ACCEPTED, OrderDelivery::STATUS_PENDING])) {
            return back()->with('error', 'Trạng thái hiện tại không thể cập nhật sang đang giao.');
        }

        if ($delivery->status === OrderDelivery::STATUS_PENDING) {
            $delivery->markAccepted();
        }

        $delivery->markInTransit();

        return back()->with('success', 'Đơn giao đã được chuyển sang trạng thái đang giao.');
    }

    public function complete(Request $request, OrderDelivery $delivery): RedirectResponse
    {
        $shipper = Shipper::where('user_id', Auth::id())->firstOrFail();
        if ($delivery->shipper_id !== $shipper->id) {
            return back()->with('error', 'Bạn không có quyền hoàn tất đơn giao này.');
        }

        if (!in_array($delivery->status, [OrderDelivery::STATUS_ACCEPTED, OrderDelivery::STATUS_IN_TRANSIT])) {
            return back()->with('error', 'Đơn giao phải ở trạng thái đã nhận hoặc đang giao.');
        }

        $validated = $request->validate([
            'tip_amount' => 'nullable|numeric|min:0',
        ]);

        $delivery->markCompleted();
        $delivery->order->update([
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);

        LoyaltyService::syncForOrder($delivery->order);

        $shipper->incrementCompletedDeliveries();
        $shipper->adjustTrustScore(0.2);

        if (isset($validated['tip_amount'])) {
            $delivery->tip_amount = $validated['tip_amount'];
            $delivery->save();
        }

        return back()->with('success', 'Đơn giao đã được hoàn tất.');
    }

    public function cancel(Request $request, OrderDelivery $delivery): RedirectResponse
    {
        $shipper = Shipper::where('user_id', Auth::id())->firstOrFail();
        if ($delivery->shipper_id !== $shipper->id) {
            return back()->with('error', 'Bạn không có quyền huỷ đơn giao này.');
        }

        if ($delivery->status === OrderDelivery::STATUS_COMPLETED) {
            return back()->with('error', 'Không thể huỷ đơn đã hoàn thành.');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $delivery->cancel_reason = $validated['reason'] ?? null;
        $delivery->cancelled_at = now();
        $delivery->shipper()->dissociate();
        $delivery->status = OrderDelivery::STATUS_PENDING;
        $delivery->tip_amount = 0;
        $delivery->notes = trim(($delivery->notes ? $delivery->notes . PHP_EOL : '') . 'Shipper huỷ: ' . ($validated['reason'] ?? 'Không rõ lý do') . ' - ' . now()->format('d/m/Y H:i'));
        $delivery->save();

        $shipper->incrementCancelledDeliveries();
        $shipper->adjustTrustScore(-0.5);

        return back()->with('success', 'Đơn giao đã được trả lại vào danh sách chờ.');
    }
}
