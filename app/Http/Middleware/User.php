<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || (Auth::check() && Auth::user()->status !== 'active')) {
            Auth::logout();
            return redirect()->route('login.form')->with('error', 'Tài khoản tạm khóa hoặc chưa đăng nhập. Vui lòng liên hệ quản trị viên!');
        }
        return $next($request);
    }
}