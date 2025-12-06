<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest('id')->paginate(10);
        return view('backend.banner.index', compact('banners'));
    }


    public function create()
    {
        return view('backend.banner.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'photo' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $slug = $this->generateUniqueSlug($request->title);
        $validatedData['slug'] = $slug;

        $banner = Banner::create($validatedData);

        $message = $banner
            ? 'Đã thêm biểu ngữ thành công'
            : 'Đã xảy ra lỗi khi thêm biểu ngữ';

        return redirect()->route('banner.index')->with(
            $banner ? 'success' : 'error',
            $message
        );
    }


    public function show($id)
    {
        // Implement if needed
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('backend.banner.edit', compact('banner'));
    }


    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'photo' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Tạo slug mới nếu title thay đổi
        if ($banner->title !== $request->title) {
            $validatedData['slug'] = $this->generateUniqueSlug($request->title);
        }

        $status = $banner->update($validatedData);

        $message = $status
            ? 'Banner đã được cập nhật thành công'
            : 'Đã xảy ra lỗi khi cập nhật biểu ngữ';

        return redirect()->route('banner.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $status = $banner->delete();

        $message = $status
            ? 'Biểu ngữ đã được xóa thành công'
            : 'Đã xảy ra lỗi khi xóa biểu ngữ';

        return redirect()->route('banner.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }


    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = Banner::where('slug', $slug)->count();

        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }

        return $slug;
    }
}
