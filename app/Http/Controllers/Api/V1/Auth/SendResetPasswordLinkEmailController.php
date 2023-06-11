<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class SendResetPasswordLinkEmailController extends Controller
{
    use SendsPasswordResetEmails;
}
