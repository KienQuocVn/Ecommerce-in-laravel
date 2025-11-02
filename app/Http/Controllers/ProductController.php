<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

use Illuminate\Support\Str;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::getAllProduct();
        return view('backend.product.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::get();
        $categories = Category::where('is_parent', 1)->get();
        return view('backend.product.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'required|string',
            'size' => 'nullable',
            'stock' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'status' => 'required|in:active,inactive',
            'condition' => 'required|in:default,new,hot',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $slug = generateUniqueSlug($request->title, Product::class);
        $validatedData['slug'] = $slug;
        $validatedData['is_featured'] = $request->input('is_featured', 0);

        if ($request->has('size')) {
            $validatedData['size'] = implode(',', $request->input('size'));
        } else {
            $validatedData['size'] = '';
        }

        $product = Product::create($validatedData);

        $message = $product
            ? 'Sản phẩm đã được thêm thành công'
            : 'Vui lòng thử lại!!';

        return redirect()->route('product.index')->with(
            $product ? 'success' : 'error',
            $message
        );
    }

    public function show($id)
    {
        // Implement if needed
    }

    public function edit($id)
    {
        $brands = Brand::get();
        $product = Product::findOrFail($id);
        $categories = Category::where('is_parent', 1)->get();
        $items = Product::where('id', $id)->get();

        return view('backend.product.edit', compact('product', 'brands', 'categories', 'items'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'required|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'required|string',
            'size' => 'nullable',
            'stock' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'condition' => 'required|in:default,new,hot',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $validatedData['is_featured'] = $request->input('is_featured', 0);

        if ($request->has('size')) {
            $validatedData['size'] = implode(',', $request->input('size'));
        } else {
            $validatedData['size'] = '';
        }

        $status = $product->update($validatedData);

        $message = $status
            ? 'Sản phẩm đã được cập nhật thành công'
            : 'Vui lòng thử lại!!';

        return redirect()->route('product.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $status = $product->delete();

        $message = $status
            ? 'Sản phẩm đã được xóa thành công'
            : 'Lỗi khi xóa sản phẩm';

        return redirect()->route('product.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }
}
