<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function changePassword(ResetPasswordRequest $request)
    {
        $user = $request->user();

        if(! Hash::check($request->input('old_password'), $user->password))
        {
            throw ValidationException::withMessages([
                'error' => 'رمز عبور وارد شده صحیح نیست'
            ]);
        }

        $pass_pattern = "/^(?=.*[!@#$%^&*()])(?=.*[A-Z])(?=.*[a-z]).{8,}$/";

        if(! preg_match($pass_pattern, $request->password))
        {
            throw ValidationException::withMessages([
                'error' => 'رمز عبور باید حداقل 8 کاراکتر شامل حداقل یک حرف کوچک، یک حرف بزرگ و یکی از سمبل های !@#$%^&* باشد'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);
        return response()->json([
            'success' => 'رمز عبور تغییر داده شد'
        ]);
    }
}
