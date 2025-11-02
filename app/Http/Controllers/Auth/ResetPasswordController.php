<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use App\Models\User;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        Validator::extend('not_same_as_old_password', function ($attribute, $value, $parameters, $validator) {
            $email = $validator->getData()['email'];
            $user = User::where('email', $email)->first(); // Sử dụng App\Models\User

            if ($user && Hash::check($value, $user->password)) {
                return false; // Mật khẩu mới trùng với mật khẩu cũ
            }

            return true; // Mật khẩu mới khác
        });

        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                'not_same_as_old_password', // Quy tắc tùy chỉnh để ngăn tái sử dụng mật khẩu cũ
            ],
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'password.not_same_as_old_password' => 'Mật khẩu mới không được trùng với mật khẩu cũ.',
        ];
    }
}
