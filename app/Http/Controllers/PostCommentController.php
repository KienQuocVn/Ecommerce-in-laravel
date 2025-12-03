<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\StatusNotification;
use App\Models\PostComment;

class PostCommentController extends Controller
{

    public function index()
    {
        $comments = PostComment::getAllComments();
        return view('backend.comment.index')->with('comments', $comments);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $post_info = Post::getPostBySlug($request->slug);
        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'active';
        $status = PostComment::create($data);
        $user = User::where('role', 'admin')->get();
        $details = [
            'title' => "New Comment created",
            'actionURL' => route('blog.detail', $post_info->slug),
            'fas' => 'fas fa-comment'
        ];
        Notification::send($user, new StatusNotification($details));
        if ($status) {
            session()->flash('success', 'Cảm ơn bạn đã bình luận');
        } else {
            session()->flash('error', 'Có lỗi xảy ra! Vui lòng thử lại!!');
        }
        return redirect()->back();
    }

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $comments = PostComment::find($id);
        if (!$comments) {
            session()->flash('error', 'Bình luận không tìm thấy');
            return redirect()->back();
        }
        return view('backend.comment.edit')->with('comment', $comments);
    }


    public function update(Request $request, $id)
    {
        $comment = PostComment::find($id);
        if (!$comment) {
            session()->flash('error', 'Bình luận không tìm thấy');
            return redirect()->back();
        }

        $this->validate($request, [
            'comment' => 'required|string'
        ]);

        $data = $request->all();
        $status = $comment->fill($data)->update();
        if ($status) {
            session()->flash('success', 'Bình luận đã được cập nhật thành công');
        } else {
            session()->flash('error', 'Có lỗi xảy ra! Vui lòng thử lại!!');
        }
        return redirect()->route('comment.index');
    }

    public function destroy($id)
    {
        $comment = PostComment::find($id);
        if (!$comment) {
            session()->flash('error', 'Bình luận không tìm thấy');
            return redirect()->back();
        }

        $status = $comment->delete();
        if ($status) {
            session()->flash('success', 'Đã xóa bình luận thành công');
        } else {
            session()->flash('error', 'Đã xảy ra lỗi, vui lòng thử lại');
        }
        return back();
    }
}
