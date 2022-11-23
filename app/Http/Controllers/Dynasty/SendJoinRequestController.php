<?php

namespace App\Http\Controllers\Dynasty;

use App\Constants\FamilyMembersType;
use App\Constants\JoinRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddFamilyMemberRequest;
use App\Models\Dynasty\DynastyMessage;
use App\Models\Dynasty\JoinRequest;
use App\Models\User;
use App\Notifications\GetOtpNotification;
use App\Notifications\JoinDynastyNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class SendJoinRequestController extends Controller
{
    public function store(AddFamilyMemberRequest $request)
    {
        $user = $request->user();
        $user_to_add = User::findOrFail($request->user_id);
        if($user->can('addFamilyMember', [$user_to_add, $request->relationship]))
        {
            $joinRequest = JoinRequest::create([
                'from_user' => $user->id,
                'to_user' => $user_to_add->id,
                'status' => JoinRequestStatus::PENDING,
                'relation' => $request->relationship,
            ]);
            $code = random_int(100000, 999999);
            $joinRequest->otp()->create([
                'user_id' => $user->id,
                'code' => Hash::make($code)
            ]);
            $user->notify(new GetOtpNotification($code));
            return response()->json([
                'message' => 'کد تاییدی به شماره تلفن همراه شما ارسال گردید.',
                'id' => $joinRequest->id,
                'from_user' => $joinRequest->from_user,
                'to_user' => $joinRequest->to_user,
                'status' => $joinRequest->status,
                'relationship' => $joinRequest->relation
            ], 200);
        }
        abort(401, 'عملیات با خطا مواجه شد!');
    }

    public function verify(User $user, JoinRequest $joinRequest, Request $request)
    {
        $otp = $joinRequest->otp->where('user_id', $user->id)->first();
        if(Hash::check($request->code, $otp->code))
        {
            $joinRequest->update(['status' => 1]);
            $senderConfirmationMessage = DynastyMessage::where('type', 'requester_confirmation_message')->first();
            $senderConfirmationMessage = $senderConfirmationMessage->message;
            $recieverMessage = DynastyMessage::where('type', 'reciever_message')->first();
            $recieverMessage = $recieverMessage->message;

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
                    $request->user()->code,
                    FamilyMembersType::familyMembersTypeList()[$joinRequest->relation],
                    $joinRequest->toUser->code,
                    Jalalian::forge($joinRequest->created_at)->format('Y/m/d'),
                    $joinRequest->fromUser->name,
                    $joinRequest->toUser->name,
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
                    '[yes]',
                    '[no]',
                    '[created_at]',
                    '[sender-name]',
                    '[reciever-name]',
                ],
                [
                    $joinRequest->toUser->code,
                    $joinRequest->fromUser->code,
                    FamilyMembersType::familyMembersTypeList()[$joinRequest->relation],
                    FamilyMembersType::familyMembersTypeList()[$joinRequest->relation],
                    $joinRequest->fromUser->code,
                    '<a href="">می پذیرم</a>',
                    '<a href="">رد میکنم</a>',
                    Jalalian::forge($joinRequest->created_at)->format('Y/m/d'),
                    $joinRequest->fromUser->name,
                    $joinRequest->toUser->name,
                ],
                $recieverMessage
            );

            $user->notify(new JoinDynastyNotification([
                'type' => 'requester_confirmation_message',
                'message' => $senderConfirmationMessage
            ]));

            $joinRequest->toUser->notify(new JoinDynastyNotification([
                'type' => 'reciever_confirmation_message',
                'message' => $recieverMessage,
            ]));
            return response()->json(['success' => 'درخواست پیوستن به سلسله با موفقیت ارسال گردید.'], 200);
        }
        return response()->json(['error' => 'کد تایید صحیح نمی باشد یا منقضی شده است!'], 404);
    }

    public function resendOtp(User $user, JoinRequest $joinRequest, Request $request)
    {
        $code = random_int(100000, 999999);
        $joinRequest->otp->updateOrCreate(
            ['user_id', $request->user()->id],
            ['code' => Hash::make($code)]
        );
        $user->notify(new GetOtpNotification($code));
        return response()->json(['success' => 'کد تایید مجددا ارسال گردید.'], 200);
    }
}
