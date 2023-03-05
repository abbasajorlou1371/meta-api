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

    public function index(Request $request)
    {
        return SentRequestsResource::collection($request->user()->sentJoinRequests);
    }

    public function show(JoinRequest $joinRequest)
    {
        $this->authorize('view', $joinRequest);
        return new SentRequestsResource($joinRequest);
    }

    public function store(AddFamilyMemberRequest $request)
    {
        $user = $request->user();
        $userToAdd = User::findOrFail($request->user);

        abort_if(
            $request->relationship === 'offspirng' && !$userToAdd->isUnderEighteen() && $request->has('permissions'),
            'شما مجاز به تعریف دسترسی برای فرزند بالای 18 سال نیستید.'
        );

        $this->authorize('addFamilyMember', [$userToAdd, $request->relationship]);

        $senderConfirmationMessage = DynastyMessage::where('type', 'requester_confirmation_message')->pluck('message')->first();
        $recieverMessage = DynastyMessage::where('type', 'reciever_message')->pluck('message')->first();

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
                $this->getRelationshipTitle($request->relationship),
                $userToAdd->code,
                Jalalian::forge(now())->format('Y/m/d'),
                $user->name,
                $userToAdd->name,
            ],
            $senderConfirmationMessage
        );
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
                $this->getRelationshipTitle($request->relationship),
                $this->getRelationshipTitle($request->relationship),
                $user->code,
                Jalalian::forge(now())->format('Y/m/d'),
                $user->name,
                $user->name,
            ],
            $recieverMessage
        );

        $joinRequest = JoinRequest::create([
            'from_user' => $user->id,
            'to_user' => $userToAdd->id,
            'status' => 0,
            'relationship' => $request->relationship,
            'message' => $recieverMessage
        ]);

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

        $user->notify(new JoinDynastyNotification([
            'type' => 'requester_confirmation_message',
            'request' => $joinRequest,
            'message' => $senderConfirmationMessage
        ]));

        $userToAdd->notify(new JoinDynastyNotification([
            'type' => 'reciever_message',
            'request' => $joinRequest,
            'message' => $recieverMessage
        ]));

        return new SentRequestsResource($joinRequest);
    }

    public function destrory(JoinRequest $joinRequest)
    {
        $this->authorize('delete', $joinRequest);
        $joinRequest->delete();
        return response()->noContent();
    }

    public function getPermissions(Request $request)
    {
        $request->validate(['relationship' => 'required|string|in:offspring']);
        $permissions = DynastyPermission::first();
        return response()->json(['permissions' => $permissions]);
    }

    public function search(Request $request)
    {
        $request->validate(['searchTerm' => 'required|string']);

        $user = User::select(['id', 'code'])
            ->where('name', 'like', '%' . $request->searchTerm . '%')
            ->orWhere('code', 'like', '%' . $request->searchTerm . '%')
            ->with(['kyc', 'profilePhotos'])
            ->first();

        if (is_null($user)) throw new ModelNotFoundException();

        return response()->json([
            'id' => $user->id,
            'code' => $user->code,
            'name' => $user->kyc?->fname . ' ' . $user->kyc?->lname,
            'image' => $user->profilePhotos->last()?->url,
            'verified' => $user->verified(),
            'age' => $user->kyc?->birthdate->diffInYears(now()),
        ]);
    }

    private function getRelationshipTitle(string $relationsip)
    {
        return match ($relationsip) {
            'brother' => 'برادر',
            'sister' => 'خواهر',
            'offspring' => 'فرزند',
            'father' => 'پدر',
            'mother' => 'مادر',
            'husband' => 'شوهر',
            'wife' => 'زن',
        };
    }
}
