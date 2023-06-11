<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    /**
     * Change user's password
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'old_password' => 'required|current_password',
            'password' => [
                'required',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ]
        ]);

        $request->user()->update(['password' => bcrypt($request->password)]);
        return response()->noContent(200);
    }
}
