<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Referal;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'name' => 'required|string|min:2|max:50|not_regex:/hm-/i',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    Password::min(8)->mixedCase()->symbols(),
                ],
                'referral' => 'nullable|exists:users,code'
            ],
            [
                'name' => [
                    'required' => 'نام خود را وارد کنید',
                    'string' => 'نام صحیح نمی باشد'
                ],
                'email' => [
                    'required' => 'ایمیل را وارد کنید',
                    'email' => 'آدرس ایمیل صحیح نیست',
                    'unique' => 'آدرس ایمیل قبلا استفاده شده است'
                ],
                'password.required' => 'رمز عبور را وارد کنید',
                'referral.exists' => 'لینک رفرال صحیح نیست.'
            ]
        );
    }

    protected function guard()
    {
        return Auth::guard('web');
    }

    protected function registered(Request $request, $user)
    {
        if ($request->referral) {
            $reference_user = User::firstWhere('code', $request->referral);
            Referal::create([
                'reference_id' => $reference_user->id,
                'referer_id' => $user->id,
            ]);
        }
        $user->registered();
        return response()->noContent();
    }
}
