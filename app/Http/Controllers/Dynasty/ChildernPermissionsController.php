<?php

namespace App\Http\Controllers\Dynasty;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ChildernPermissionsController extends Controller
{
    public function updatePermissions(request $request, User $user)
    {
        $user->permissions()->update([
            'BFR'  => $request->BFR,
            'SF'   => $request->SF,
            'W'    => $request->W,
            'JU'   => $request->JU,
            'DM'   => $request->DM,
            'PIUP' => $request->PIUP,
            'PITC' => $request->PITC,
            'PIC'  => $request->PIC,
            'ESOO' => $request->ESOO,
            'COTB' => $request->COTB,
        ]);
        return response()->json(['success' => 'دسترسی ها بروزرسانی شد!'], 200);
    }
}
