<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'ASC')->paginate(10);
        return view('backend.users.index')->with('users', $users);
    }

    public function create()
    {
        return view('backend.users.create');
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'string|required|max:30',
                'email' => 'string|required|unique:users',
                'password' => 'string|required',
                'role' => 'required|in:admin,user',
                'status' => 'required|in:active,inactive',
                'photo' => 'nullable|string',
            ]
        );
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $status = User::create($data);
        if ($status) {
            session()->flash('success', 'Đã thêm người dùng thành công');
        } else {
            session()->flash('error', 'Đã xảy ra lỗi khi thêm người dùng');
        }
        return redirect()->route('users.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('backend.users.edit')->with('user', $user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->validate(
            $request,
            [
                'name' => 'string|required|max:30',
                'email' => 'string|required',
                'role' => 'required|in:admin,user',
                'status' => 'required|in:active,inactive',
                'photo' => 'nullable|string',
            ]
        );
        $data = $request->all();

        $status = $user->fill($data)->save();
        if ($status) {
            session()->flash('success', 'Đã cập nhật thành công');
        } else {
            session()->flash('error', 'Đã xảy ra lỗi khi cập nhật');
        }
        return redirect()->route('users.index');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Kiểm tra không được xóa chính mình
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Bạn không thể vô hiệu hóa tài khoản của chính mình');
            return redirect()->route('users.index');
        }

        // Kiểm tra không được xóa user admin cuối cùng
        $adminCount = User::where('role', 'admin')->where('status', 'active')->count();
        if ($user->role === 'admin' && $user->status === 'active' && $adminCount <= 1) {
            session()->flash('error', 'Phải có ít nhất một admin hoạt động');
            return redirect()->route('users.index');
        }

        // Thay vì xóa, chỉ thay đổi status thành inactive
        $user->status = 'inactive';
        $status = $user->save();

        if ($status) {
            session()->flash('success', 'Người dùng đã bị vô hiệu hóa thành công');
        } else {
            session()->flash('error', 'Có lỗi khi vô hiệu hóa người dùng');
        }
        return redirect()->route('users.index');
    }
}
