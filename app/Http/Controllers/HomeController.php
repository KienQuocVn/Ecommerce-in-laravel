<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\PostComment;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\Rules\NotSameAsOldPassword;
use Illuminate\Support\Facades\Auth;
use App\Services\LoyaltyService;
use Helper;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $recentCompletedOrders = $user->orders()
            ->with(['delivery.shipper', 'shipping'])
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $activeOrders = $user->orders()
            ->with(['delivery.shipper', 'shipping'])
            ->whereIn('status', ['new', 'progress', 'process'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recommendedProducts = Helper::recommendedProductsForUser($user, 6);
        $tierMeta = LoyaltyService::tierMeta($user->loyalty_tier);

        return view('user.index', compact('recentCompletedOrders', 'activeOrders', 'recommendedProducts', 'tierMeta', 'user'));
    }

    public function profile()
    {
        $profile = Auth()->user();
        // return $profile;
        return view('user.users.profile')->with('profile', $profile);
    }

    public function profileUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|min:2|max:120',
            'last_name' => 'required|string|min:2|max:120',
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|min:8|max:20',
            'address_line1' => 'required|string|min:5|max:255',
            'photo' => 'nullable|string|max:255',
            'role' => 'nullable|in:admin,user,shipper',
        ]);

        $data['name'] = $data['name'] ?? trim($data['first_name'] . ' ' . $data['last_name']);

        $status = $user->fill($data)->save();
        if ($status) {
            session()->flash('success', 'Đã cập nhật hồ sơ của bạn thành công');
        } else {
            session()->flash('error', 'Vui lòng thử lại!');
        }
        return redirect()->back();
    }

    // Order
    public function orderIndex()
    {
        $orders = Order::with(['shipping', 'delivery.shipper'])
            ->orderBy('id', 'DESC')
            ->where('user_id', auth()->user()->id)
            ->paginate(10);
        return view('user.order.index')->with('orders', $orders);
    }
    public function userOrderDelete($id)
    {
        $order = Order::find($id);
        if ($order) {
            if ($order->status == "process" || $order->status == 'delivered' || $order->status == 'cancel') {
                return redirect()->back()->with('error', 'Bạn không thể xóa đơn hàng này ngay bây giờ');
            } else {
                $status = $order->delete();
                if ($status) {
                    session()->flash('success', 'Đơn hàng đã xóa thành công');
                } else {
                    session()->flash('error', 'Không thể xóa đơn hàng');
                }
                return redirect()->route('user.order.index');
            }
        } else {
            session()->flash('error', 'Không tìm thấy đơn hàng');
            return redirect()->back();
        }
    }

    public function orderShow($id)
    {
        $order = Order::with(['shipping', 'delivery.shipper.user', 'delivery.reviews'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return redirect()->route('user.order.index')->with('error', 'Không tìm thấy đơn hàng.');
        }

        return view('user.order.show', compact('order'));
    }
    // Product Review
    public function productReviewIndex()
    {
        $reviews = ProductReview::getAllUserReview();
        return view('user.review.index')->with('reviews', $reviews);
    }

    public function productReviewEdit($id)
    {
        $review = ProductReview::find($id);
        // return $review;
        return view('user.review.edit')->with('review', $review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productReviewUpdate(Request $request, $id)
    {
        $review = ProductReview::find($id);
        if ($review) {
            $data = $request->all();
            $status = $review->fill($data)->save();
            if ($status) {
                session()->flash('success', 'Đánh giá đã được cập nhật thành công');
            } else {
                session()->flash('error', 'Có lỗi xảy ra! Vui lòng thử lại!');
            }
        } else {
            session()->flash('error', 'Không tìm thấy đánh giá!');
        }

        return redirect()->route('user.productreview.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productReviewDelete($id)
    {
        $review = ProductReview::find($id);
        $status = $review->delete();
        if ($status) {
            session()->flash('success', 'Đã xóa đánh giá thành công');
        } else {
            session()->flash('error', 'Có gì đó không ổn! Hãy thử lại');
        }
        return redirect()->route('user.productreview.index');
    }

    public function userComment()
    {
        $comments = PostComment::getAllUserComments();
        return view('user.comment.index')->with('comments', $comments);
    }
    public function userCommentDelete($id)
    {
        $comment = PostComment::find($id);
        if ($comment) {
            $status = $comment->delete();
            if ($status) {
                session()->flash('success', 'Đã xóa bình luận thành công');
            } else {
                session()->flash('error', 'Đã xảy ra lỗi, vui lòng thử lại');
            }
            return back();
        } else {
            session()->flash('error', 'Không tìm thấy bình luận');
            return redirect()->back();
        }
    }
    public function userCommentEdit($id)
    {
        $comments = PostComment::find($id);
        if ($comments) {
            return view('user.comment.edit')->with('comment', $comments);
        } else {
            session()->flash('error', 'Không tìm thấy bình luận');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userCommentUpdate(Request $request, $id)
    {
        $comment = PostComment::find($id);
        if ($comment) {
            $data = $request->all();
            // return $data;
            $status = $comment->fill($data)->save();
            if ($status) {
                session()->flash('success', 'Bình luận đã được cập nhật thành công');
            } else {
                session()->flash('error', 'Có lỗi xảy ra! Vui lòng thử lại!');
            }
            return redirect()->route('user.post-comment.index');
        } else {
            session()->flash('error', 'Không tìm thấy bình luận');
            return redirect()->back();
        }
    }

    public function changePassword()
    {
        return view('user.layouts.userPasswordChange');
    }

    public function changPasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required', new NotSameAsOldPassword],
            'new_confirm_password' => ['same:new_password'],
        ]);

        // Cập nhật mật khẩu mới
        /** @var User $user */
        $user = Auth::user();
        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('user')->with('success', 'Mật khẩu đã được thay đổi thành công');
    }
}
