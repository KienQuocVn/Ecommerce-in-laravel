<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipping;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ShippingController extends Controller
{

    public function index()
    {
        $shipping = Shipping::orderBy('priority')->orderBy('id', 'DESC')->paginate(10);
        return view('backend.shipping.index')->with('shippings', $shipping);
    }

    public function create()
    {
        return view('backend.shipping.create', [
            'pricingStrategies' => $this->pricingStrategies(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $payload = $this->sanitizePayload($validated);
        $payload['code'] = $payload['code'] ?? Str::upper(Str::slug($payload['type'], '_')) . '_' . Str::random(6);

        $status = Shipping::create($payload);
        if ($status) {
            session()->flash('success', 'Đã tạo thành công vận chuyển');
        } else {
            session()->flash('error', 'Lỗi, vui lòng thử lại');
        }
        return redirect()->route('shipping.index');
    }

    public function show($id)
    {
        // not used
    }

    public function edit($id)
    {
        $shipping = Shipping::find($id);
        if (!$shipping) {
            session()->flash('error', 'Không tìm thấy vận chuyển');
        }
        return view('backend.shipping.edit', [
            'shipping' => $shipping,
            'pricingStrategies' => $this->pricingStrategies(),
        ]);
    }


    public function update(Request $request, $id)
    {
        $shipping = Shipping::find($id);
        if (!$shipping) {
            session()->flash('error', 'Không tìm thấy vận chuyển');
            return redirect()->route('shipping.index');
        }

        $validated = $this->validateRequest($request, $shipping->id);
        $payload = $this->sanitizePayload($validated);

        $status = $shipping->fill($payload)->save();
        if ($status) {
            session()->flash('success', 'Đã cập nhật vận chuyển thành công');
        } else {
            session()->flash('error', 'Lỗi, vui lòng thử lại');
        }
        return redirect()->route('shipping.index');
    }

    public function destroy($id)
    {
        $shipping = Shipping::find($id);
        if (!$shipping) {
            session()->flash('error', 'Vận chuyển không tìm thấy');
            return redirect()->back();
        }

        // Kiểm tra xem shipping có được dùng trong orders không
        $orderCount = \App\Models\Order::where('shipping_id', $id)->count();
        if ($orderCount > 0) {
            session()->flash('error', 'Không thể xóa phương thức vận chuyển này vì đã được sử dụng trong ' . $orderCount . ' đơn hàng');
            return redirect()->route('shipping.index');
        }

        $status = $shipping->delete();
        if ($status) {
            session()->flash('success', 'Đã xóa thành công vận chuyển');
        } else {
            session()->flash('error', 'Lỗi, vui lòng thử lại');
        }
        return redirect()->route('shipping.index');
    }

    protected function pricingStrategies(): array
    {
        return [
            'flat' => 'Cố định',
            'percentage' => 'Theo phần trăm giá trị đơn',
            'mixed' => 'Kết hợp (cố định + phần trăm)',
        ];
    }

    protected function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:shippings,code';
        if ($ignoreId) {
            $uniqueRule .= ',' . $ignoreId;
        }

        return $this->validate($request, [
            'code' => ['nullable', 'string', 'max:50', $uniqueRule],
            'type' => 'string|required|max:255',
            'service_level' => 'nullable|string|max:150',
            'delivery_zone' => 'nullable|string|max:150',
            'price' => 'nullable|numeric|min:0',
            'pricing_strategy' => 'required|in:flat,percentage,mixed',
            'percentage_rate' => 'nullable|numeric|min:0|max:100',
            'min_cart_total' => 'nullable|numeric|min:0',
            'max_cart_total' => 'nullable|numeric|min:0|gte:min_cart_total',
            'estimated_time' => 'nullable|string|max:255',
            'supports_cod' => 'nullable|boolean',
            'is_recommended' => 'nullable|boolean',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|min:0|max:255',
            'status' => 'required|in:active,inactive',
        ]);
    }

    protected function sanitizePayload(array $validated): array
    {
        $payload = Arr::only($validated, [
            'code',
            'type',
            'service_level',
            'delivery_zone',
            'price',
            'pricing_strategy',
            'percentage_rate',
            'min_cart_total',
            'max_cart_total',
            'estimated_time',
            'description',
            'priority',
            'status',
        ]);

        $payload['supports_cod'] = isset($validated['supports_cod']) ? (bool) $validated['supports_cod'] : false;
        $payload['is_recommended'] = isset($validated['is_recommended']) ? (bool) $validated['is_recommended'] : false;

        if (!isset($payload['price'])) {
            $payload['price'] = 0;
        }

        if (($payload['pricing_strategy'] ?? 'flat') === 'flat') {
            $payload['percentage_rate'] = 0;
        }

        if (!isset($payload['priority'])) {
            $payload['priority'] = 10;
        }

        return $payload;
    }
}
