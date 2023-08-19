<?php

namespace App\Http\Controllers\Api\V1\Dynasty;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddFamilyMemberRequest;
use App\Http\Resources\Dynasty\SentRequestsResource;
use App\Models\Dynasty\DynastyMessage;
use App\Models\Dynasty\JoinRequest;
use App\Models\DynastyPermission;
use App\Models\User;
use App\Notifications\JoinDynastyNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class SendJoinRequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('account.security')->only(['store', 'destroy']);
    }

    /**
     * Get all sent join requests
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return SentRequestsResource::collection($request->user()->sentJoinRequests);
    }

    /**
     * Get a join request
     * @param JoinRequest $joinRequest
     * @return SentRequestsResource
     */
    public function show(JoinRequest $joinRequest)
    {
        $this->authorize('view', $joinRequest);
        return new SentRequestsResource($joinRequest);
    }

    /**
     * Send a join request
     * @param AddFamilyMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(AddFamilyMemberRequest $request)
    {
        $user = $request->user();

        // Get the user to add
        $userToAdd = User::findOrFail($request->user);

        // Prevent user from setting permissions for the user who is older than 18
        abort_if(
            $request->relationship === 'offspring' && !$userToAdd->isUnderEighteen() && $request->has('permissions'),
            403,
            'شما مجاز به تعریف دسترسی برای فرزند بالای 18 سال نیستید.'
        );

        // Check if the user is authorized to add the user
        $this->authorize('addFamilyMember', [$userToAdd, $request->relationship]);

        // Get the sender confirmation message and reciever message
        $senderConfirmationMessage = DynastyMessage::where('type', 'requester_confirmation_message')->pluck('message')->first();
        $recieverMessage = DynastyMessage::where('type', 'reciever_message')->pluck('message')->first();

        // Replace the placeholders with the actual values
        $senderConfirmationMessage = str_replace(
            [
                '[sender-code]',
                '[relationship]',
                '[reciever-code]',
                '[created_at]',
                '[sender-name]',
                '[reciever-name]',
            ],
            [
                $user->code,
                getRelationshipTitle($request->relationship),
                $userToAdd->code,
                Jalalian::forge(now())->format('Y/m/d'),
                $user->name,
                $userToAdd->name,
            ],
            $senderConfirmationMessage
        );

        // Replace the placeholders with the actual values
        $recieverMessage = str_replace(
            [
                '[reciever-code]',
                '[sender-code]',
                '[relationship]',
                '[relationship]',
                '[sender-code]',
                '[created_at]',
                '[sender-name]',
                '[reciever-name]',
            ],
            [
                $userToAdd->code,
                $user->code,
                getRelationshipTitle($request->relationship),
                getRelationshipTitle($request->relationship),
                $user->code,
                Jalalian::forge(now())->format('Y/m/d'),
                $user->name,
                $user->name,
            ],
            $recieverMessage
        );

        // Create a join request
        $joinRequest = JoinRequest::create([
            'from_user' => $user->id,
            'to_user' => $userToAdd->id,
            'status' => 0,
            'relationship' => $request->relationship,
            'message' => $recieverMessage
        ]);

        // Add permissions if the relationship is offspring and the destination user is under 18
        if ($request->relationship === 'offspring' && $userToAdd->isUnderEighteen()) {
            $permissions = $request->permissions;
            $joinRequest->toUser->permissions()->create([
                'verified' => false,
                'BFR'      => $permissions['BFR'],
                'SF'       => $permissions['SF'],
                'W'        => $permissions['W'],
                'JU'       => $permissions['JU'],
                'DM'       => $permissions['DM'],
                'PIUP'     => $permissions['PIUP'],
                'PITC'     => $permissions['PITC'],
                'PIC'      => $permissions['PIC'],
                'ESOO'     => $permissions['ESOO'],
                'COTB'     => $permissions['COTB']
            ]);
        }

        // Notify the user that the join request has been sent
        $user->notify(new JoinDynastyNotification([
            'type' => 'requester_confirmation_message',
            'request' => $joinRequest,
            'message' => $senderConfirmationMessage
        ]));

        // Notify the user that the join request has been recieved
        $userToAdd->notify(new JoinDynastyNotification([
            'type' => 'reciever_message',
            'request' => $joinRequest,
            'message' => $recieverMessage
        ]));

        // Return the join request
        return new SentRequestsResource($joinRequest);
    }

    /**
     * Delete a join request
     * @param JoinRequest $joinRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destrory(JoinRequest $joinRequest)
    {
        // Check if the user is authorized to delete the join request
        $this->authorize('delete', $joinRequest);

        // Delete the join request
        $joinRequest->delete();

        // Return a 200 response
        return response()->noContent(200);
    }

    /**
     * Get the permissions
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions(Request $request)
    {
        $request->validate(['relationship' => 'required|string|in:offspring']);
        $permissions = DynastyPermission::first();
        return response()->json(['permissions' => $permissions]);
    }

    /**
     * Search for a user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function search(Request $request)
    {
        $request->validate(['searchTerm' => 'required|string']);

        // Search for the user
        $user = User::select(['id', 'code'])
            ->where('name', 'like', '%' . $request->searchTerm . '%')
            ->orWhere('code', 'like', '%' . $request->searchTerm . '%')
            ->orWhere(function ($query) {
                $query->whereHas('kyc', function ($query) {
                    $query->where('fname', 'like', '%' . request()->searchTerm . '%')
                        ->orWhere('lname', 'like', '%' . request()->searchTerm . '%');
                });
            })
            ->with(['kyc', 'profilePhotos'])
            ->first();

        // Throw an exception if the user is not found
        if (is_null($user)) throw new ModelNotFoundException();

        return response()->json([
            'id' => $user->id,
            'code' => $user->code,
            'name' => optional($user->kyc)->fname . ' ' . optional($user->kyc)->lname,
            'image' => optional($user->profilePhotos->last())->url,
            'verified' => $user->verified(),
            'age' => $user->verified() ? $user->kyc->birthdate->diffInYears(now()) : null,
        ]);
    }
}
