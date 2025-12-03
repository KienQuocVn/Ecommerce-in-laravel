<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostTag;
use Illuminate\Support\Str;

class PostTagController extends Controller
{

    public function index()
    {
        $postTag = PostTag::orderBy('id', 'asc')->paginate(10);
        return view('backend.posttag.index')->with('postTags', $postTag);
    }

    public function create()
    {
        return view('backend.posttag.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'string|required',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = PostTag::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $data['slug'] = $slug;
        $status = PostTag::create($data);
        if ($status) {
            session()->flash('success', 'Đã thêm thẻ bài viết thành công');
        } else {
            session()->flash('error', 'Vui lòng thử lại!!');
        }
        return redirect()->route('post-tag.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $postTag = PostTag::findOrFail($id);
        return view('backend.posttag.edit')->with('postTag', $postTag);
    }

    public function update(Request $request, $id)
    {
        $postTag = PostTag::findOrFail($id);
        $this->validate($request, [
            'title' => 'string|required',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $status = $postTag->fill($data)->save();
        if ($status) {
            session()->flash('success', 'Thẻ bài viết đã được cập nhật thành công');
        } else {
            session()->flash('error', 'Vui lòng thử lại!!');
        }
        return redirect()->route('post-tag.index');
    }


    public function destroy($id)
    {
        $postTag = PostTag::findOrFail($id);

        // Kiểm tra xem tag có được dùng trong posts không
        // Nếu posts có lưu tags dưới dạng comma-separated string thì cần custom logic
        // Nhưng nên convert sang bảng trung gian post_post_tag tương lai

        $status = $postTag->delete();

        if ($status) {
            session()->flash('success', 'Thẻ bài viết đã được xóa thành công');
        } else {
            session()->flash('error', 'Lỗi khi xóa thẻ bài viết');
        }
        return redirect()->route('post-tag.index');
    }
}
