<?php

namespace App\Http\Controllers\Api\V1\Dynasty;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dynasty\RecievedJoinRequest;
use App\Models\Dynasty\DynastyMessage;
use App\Models\Dynasty\DynastyPrize;
use App\Models\Dynasty\JoinRequest;
use App\Models\DynastyPermission;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\JoinDynastyNotification;
use Morilog\Jalali\Jalalian;

class AcceptJoinRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('account.security')->only(['accept', 'reject']);
    }

    public function index()
    {
        return RecievedJoinRequest::collection(request()->user()->recievedJoinRequests);
    }

    public function show(JoinRequest $joinRequest)
    {
        $this->authorize('view', $joinRequest);
        return new RecievedJoinRequest($joinRequest);
    }

    public function accept(Request $request, JoinRequest $joinRequest)
    {
        $this->authorize('accept', $joinRequest);

        $requestedUser = $joinRequest->fromUser;
        $joinRequest->update(['status' => 1]);
        $user = $request->user();

        if ($requestedUser->isUnderEighteen() && $joinRequest->relationship === 'father') {
            $permssions = DynastyPermission::first();
            $$requestedUser->permissions()->create([
                'verified' => 1,
                'BFR' => $permssions->BFR,
                'SF' => $permssions->SF,
                'W' => $permssions->W,
                'JU' => $permssions->JU,
                'DM' => $permssions->DM,
                'PIUP' => $permssions->PIUP,
                'PITC' => $permssions->PITC,
                'PIC' => $permssions->PIC,
                'ESOO' => $permssions->ESOO,
                'COTB' => $permssions->COTB
            ]);
        } elseif ($user->isUnderEighteen() && $joinRequest->relationship === 'offspring') {
            $user->permissions->update(['verified' => 1]);
        }

        $dynasty = $requestedUser->dynasty;
        $family = $dynasty->family;

        $family->familyMembers()->create([
            'relationship' => $joinRequest->relationship,
            'user_id' => $user->id,
        ]);

        $requesterMessage = DynastyMessage::firstWhere('type', 'requester_accept_message')->message;
        $recieverMessage = DynastyMessage::firstWhere('type', 'reciever_accept_message')->message;

        $requesterMessage = str_replace(
            ['[sender-code]', '[reciever-code]', '[relationship]', '[created_at]', '[relationship]'],
            [
                $requestedUser->code,
                $user->code,
                $joinRequest->getRelationShipTitle(),
                Jalalian::forge($joinRequest->created_at)->format('Y/m/d'),
                $joinRequest->getRelationShipTitle()
            ],
            $requesterMessage
        );

        $recieverMessage = str_replace(
            ['[reciever-code]', '[created_at]', '[sender-code]', '[relationship]', '[sender-name]'],
            [
                $user->code,
                Jalalian::forge($joinRequest->created_at)->format('Y/m/d'),
                $requestedUser->code,
                $joinRequest->getRelationShipTitle(),
                $requestedUser->name,
            ],
            $recieverMessage
        );

        $prize = DynastyPrize::where('member', $joinRequest->relationship)->first();

        $requestedUser->recievedDynastyPrizes()->create([
            'prize_id' => $prize->id,
            'message' => $requesterMessage,
        ]);

        $requestedUser->notify(new JoinDynastyNotification([
            'type' => 'requester_accept_message',
            'message' => $requesterMessage,
            'request' => $joinRequest
        ]));

        $user->notify(new JoinDynastyNotification([
            'type' => 'reciever_accept_message',
            'message' => $recieverMessage,
            'request' => $joinRequest
        ]));

        return response()->noContent();
    }

    public function reject(JoinRequest $joinRequest)
    {
        $this->authorize('reject', $joinRequest);
        $joinRequest->update(['status' => -1]);
        $requestedUser = $joinRequest->fromUser;
        $requestedUser->notify(new JoinDynastyNotification([
            'message' => "درخواست پیوستن به سلسله شما توسط کاربر {$joinRequest->toUser->code} رد شد!",
        ]));
        return response()->noContent();
    }
}
