<?php

namespace App\Http\Controllers\Api\V1\Dynasty;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateChildrenPermissionsRequest;
class ChildernPermissionsController extends Controller
{
    /**
     * Update children permissions
     * @param UpdateChildrenPermissionsRequest $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UpdateChildrenPermissionsRequest $request, User $user)
    {
        // Check if the user is authorized to update children permissions
        $this->authorize('controlPermissions', $user);
        // Update children permissions
        $user->permissions->update([$request->permission => $request->status]);
        return response()->json([], 200);
    }
}
