<?php

namespace App\Http\Controllers\Dynasty;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ChildernPermissionsController extends Controller
{
    public function updatePermissions(request $request, User $user)
    {
        $this->validate(
            $request,
            [
                'permission' => 'required|string|in:BFR,SF,W,JU,DM,PIUP,PITC,PIC,ESOO,COTB',
                'status' => 'required_with:permission|numeric|min:0|max:1',
            ]
        );
        $user->permissions->update([
            $request->permission => $request->status
        ]);
        return response()->json(['success' => 'دسترسی ها بروزرسانی شد!'], 200);
    }
}
