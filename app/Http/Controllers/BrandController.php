<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;
use App\Helpers\helpers;

class BrandController extends Controller
{

    public function index()
    {
        $brands = Brand::orderBy('id', 'ASC')->paginate();
        return view('backend.brand.index', compact('brands'));
    }


    public function create()
    {
        return view('backend.brand.create');
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $slug = generateUniqueSlug($request->title, Brand::class);

        $validatedData['slug'] = $slug;

        $brand = Brand::create($validatedData);

        $message = $brand
            ? 'Thương hiệu được tạo thành công'
            : 'Error, Please try again';

        return redirect()->route('brand.index')->with(
            $brand ? 'success' : 'error',
            $message
        );
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->back()->with('error', 'Brand not found');
        }

        return view('backend.brand.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->back()->with('error', 'Brand not found');
        }

        $validatedData = $request->validate([
            'title' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Tạo slug mới nếu title thay đổi
        if ($brand->title !== $request->title) {
            $validatedData['slug'] = generateUniqueSlug($request->title, Brand::class);
        }

        $status = $brand->update($validatedData);

        $message = $status
            ? 'Thương hiệu đã được cập nhật thành công'
            : 'Lỗi, vui lòng thử lại';

        return redirect()->route('brand.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->back()->with('error', 'Brand không tìm thấy');
        }

        // Kiểm tra xem brand có products không
        $productCount = \App\Models\Product::where('brand_id', $id)->count();

        if ($productCount > 0) {
            return redirect()->route('brand.index')->with(
                'error',
                'Không thể xóa thương hiệu này vì vẫn còn ' . $productCount . ' sản phẩm. Vui lòng xóa hoặc chuyển các sản phẩm sang thương hiệu khác trước.'
            );
        }

        $status = $brand->delete();

        $message = $status
            ? 'Thương hiệu đã bị xóa thành công'
            : 'Lỗi, vui lòng thử lại';

        return redirect()->route('brand.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }
}
