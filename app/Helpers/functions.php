<?php

use App\Constants\FamilyMembersType;
use App\Constants\TicketStatus;
use App\Models\Captcha;
use App\Models\Feature;
use App\Models\Feature\FeatureHourlyProfit;
use App\Models\Level\Level;
use App\Models\Trade;
use App\Models\BuyFeatureRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Variable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function fee(Feature $feature)
{
    return [
        'psc' => $feature->properties->price_psc * config('rgb.fee'),
        'irr' => $feature->properties->price_irr * config('rgb.fee')
    ];
}

/*
 This function caculates the total price of a Feature including comissions
*/
function totalPrice(Feature $feature, string $type, array $comissions)
{
    switch ($type) {
        case 'buyer':
            return [
                'psc' => $feature->properties->price_psc + $comissions['psc'],
                'irr' => $feature->properties->price_irr + $comissions['irr'],
            ];
            break;
        case 'seller':
            return [
                'psc' => $feature->properties->price_psc - $comissions['psc'],
                'irr' => $feature->properties->price_irr - $comissions['irr'],
            ];
            break;
        default:
            return null;
    }
}


function chargeBuyer(User $buyer, $feature)
{
    $amount = totalPrice($feature, 'buyer', fee($feature));
    $buyer->assets->decrement('psc', $amount['psc']);
    $buyer->assets->decrement('irr', $amount['irr']);
}

function addSeller(User $seller, $feature)
{
    $amount = totalPrice($feature, 'seller', fee($feature));
    $seller->assets->increment('psc', $amount['psc']);
    $seller->assets->increment('irr', $amount['irr']);
}

function iszero($value): bool
{
    return $value == 0;
}

function convertDateToCarbon($date)
{
    $date = \Morilog\Jalali\CalendarUtils::convertNumbers($date, true);
    $date = str_replace('/', '-', $date);
    $date = Carbon::parse($date)->format('Y-m-d');
    $date = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y-m-d', $date)->format('Y-m-d');
    return $date;
}

function isUnderEighteen(User $user)
{
    $birthdate = Carbon::parse($user->kyc->birthdate)->format('Y-m-d');
    $birthdate = Carbon::createFromDate($birthdate);
    if ($birthdate->diffInYears(now()) < 18) return true;
    return false;
}

function getFamilyRelationship($relationship)
{
    switch ($relationship) {
        case FamilyMembersType::BROTHER:
            return 'برادر';
            break;
        case FamilyMembersType::FATHER:
            return 'بدر';
            break;
        case FamilyMembersType::MOTHER:
            return 'مادر';
            break;
        case FamilyMembersType::HUSBAND:
            return 'شوهر';
            break;
        case FamilyMembersType::WIFE:
            return 'همسر';
            break;
        case FamilyMembersType::SISTER:
            return 'خواهر';
            break;
        case FamilyMembersType::OWNER:
            return 'صاحب سلسله';
            break;
        case FamilyMembersType::OFFSPRING:
            return 'فرزند';
            break;
    }
}

function currentColorPrice($color)
{
    return Variable::getRate($color);
}

function currentPscPrice()
{
    return Variable::getRate('psc');
}

function validateOtp(User $user, int $code)
{
    $otp = $user->otp->where('otp_reason', 'trade-feature')->first();
    if ($otp->code != $code || $otp->updated_at->diffInMinutes(now()) > 60) return false;
    return true;
}

function ticketDepartmentsTitle($department)
{
    switch ($department) {
        case 'technical_support':
            return 'پشتیبانی فنی';
            break;
        case 'citizens_safety':
            return 'امنیت شهروندان';
            break;
        case 'investment':
            return 'سرمایه گذاری';
            break;
        case 'inspection':
            return 'بازرسی';
            break;
        case 'protection':
            return 'حراست';
            break;
        case 'ztb':
            return 'مدیریت کل ز ت ب';
            break;
    }
}

function ticketStatusTitle($status)
{
    switch ($status) {
        case TicketStatus::NEW:
            return 'جدید';
            break;
        case TicketStatus::ANSWERED:
            return 'پاسخ داده شده';
            break;
        case TicketStatus::TRACKING:
            return 'درحال بررسی';
            break;
        case TicketStatus::CLOSED:
            return 'بسته شده';
            break;
        case TicketStatus::RESOLVED:
            return 'حل شده';
            break;
        case TicketStatus::UNRESOLVED:
            return 'حل نشده';
            break;
    }
}

function getScorePercentageToNextLevel(?Level $level, int $score): int
{
    if (!$level) {
        if ($score == 0) return 0;

        $firstLevel = Level::first();
        return ($score / $firstLevel->score) * 100;
    } else {
        $nextLevel = Level::find($level->id + 1);
        if (is_null($nextLevel)) return 0;
        return ($score / $nextLevel->score) * 100;
    }
}

function getRemainedTimePercentage($date)
{
}

function hourlyProfitInfo(User $user): array
{
    $firstHourlyProfit = FeatureHourlyProfit::with(['feature', 'feature.properties'])->firstWhere('user_id', $user->id);
    if ($firstHourlyProfit) {
        $dead_line = new Carbon($firstHourlyProfit->dead_line);
        $user_withdraw_profit_limit = $user->variables->withdraw_profit * 86400;
        return [
            'percentage' => floor(($dead_line->diffInSeconds(now()) / $user_withdraw_profit_limit) * 100),
            'karbari' => $firstHourlyProfit->feature->properties->karbari,
        ];
    }
    return [];
}

function getLevelsImages($userLevel)
{
    $images = [];
    if ($userLevel) {
        $levels = Level::orderBy('score')->lazy();
        foreach ($levels as $level) {
            if ($userLevel->score >= $level->score) {
                array_push($images, $level->image?->url);
            }
        }
    }
    return $images;
}

function generate_captcha()
{
    $IMG = imagecreate(130, 50);

    $bgColor = imagecolorallocate($IMG, 255, 255, 255);
    imagefilledrectangle($IMG, 0, 0, 130, 50, $bgColor);

    for ($i = 0; $i < 4; $i++) {
        $bgColorEllipse = imagecolorallocate($IMG, rand($i + 50, 255), rand($i, 255), rand(0, 255));
        imagefilledellipse($IMG, rand(5, 130), rand(0, 50), rand(0, 100), rand(0, 50), $bgColorEllipse);
        imagefilledellipse($IMG, rand(20, 100), rand(0, 40), rand(0, 130), rand(20, 50), $bgColorEllipse);
    }

    $characters = "AaBbCcDdEeFfGgHh1234567890iIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789";

    $fonts = [
        "fonts/poppins/poppins-v5-latin-300.ttf",
        "fonts/poppins/poppins-v5-latin-500italic.ttf",
        "fonts/poppins/poppins-v5-latin-600.ttf",
        "fonts/poppins/poppins-v5-latin-700.ttf"
    ];

    $txtColor = imagecolorallocate($IMG, 0, 0, 0);

    $phrase = "";
    for ($i = 0; $i < 4; $i++) {
        $selectedFont = $fonts[rand(0, count($fonts) - 1)];
        $font = public_path($selectedFont);
        $character = $characters[rand(0, strlen($characters) - 1)];
        imagettftext($IMG, 18, rand(40, -20), 20 + ($i * 30), 35 + $i, $txtColor, $font, $character);
        $phrase .= $character;
    }

    $captchaPath = public_path('/captcha/');

    if (!file_exists($captchaPath)) {
        mkdir($captchaPath, 0777);
    }

    $captchaFileName = uniqid();
    $captcha = $captchaPath . $captchaFileName . ".jpeg";
    imagejpeg($IMG, $captcha);
    Captcha::updateOrCreate(
        ['ip' => request()->ip()],
        [
            'code' => $phrase,
            'expires_at' => time() + 30,
            'fileName' => $captcha
        ]
    );
}

function getTransactionTitle(Transaction $transaction) {
    if($transaction->payable instanceof BuyFeatureRequest) {
        return 'پیشنهاد خرید ملک';
    } elseif($transaction->payable instanceof Trade) {
        return 'معامله ملک';
    } elseif($transaction->payable instanceof Order) {
        return 'خرید دارایی';
    }
}

function getTransactionStatus(Transaction $transaction) {
    switch($transaction->status) {
        case 1:
            return 'موفق';
            break;
        case -1:
            return 'ناموفق';
            break;
        case 0:
            return 'معلق';
            break;
    }
}
