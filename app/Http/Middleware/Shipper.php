<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Shipper
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->status !== 'active' || Auth::user()->role !== 'shipper') {
            Auth::logout();
            return redirect()->route('login.form')->with('error', 'Chỉ tài khoản shipper hoạt động mới được truy cập khu vực này.');
        }

        if (!Auth::user()->shipperProfile) {
            return redirect()->route('login.form')->with('error', 'Tài khoản chưa được kích hoạt hồ sơ shipper.');
        }

        return $next($request);
    }
}
