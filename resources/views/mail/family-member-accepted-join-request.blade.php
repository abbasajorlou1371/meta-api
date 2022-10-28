<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>قبول درخواست ملحق شدن به سلسله خانوادگی</title>
    <style>
        body{
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center">رخواست ملحق شدن به سلسله پذیرفته شد</h2>
    <p>
        سلام {{ $fromUser->name }}
    </p>
    <p>
        شما درخواستی برای سلسله خود به شهروند {{ $joinRequest->to_user }} به عنوان {{ $joinRequest->relation }} خونی خود ارسال نموده اید
    </p>
    <p>
        این سلسله توسط شهروند {{ $joinRequest->to_user }} در تاریخ {{ \Morilog\Jalali\Jalalian::forge($joinRequest->created_at)->format('Y/m/d') }} تایید شده است و حذف آن امکان پذیر نخواهد بود
    </p>
    <br>
    <h3>پاداش دریافت شده در خصوص افزایش سلسله به شرح زیر است :</h3>
    <p>PSC : 400</p>
    <p>افزایش حجم ذخیره دیتا : 4%</p>
    <p>افزایش حجم سود پاداش معرفی : 4%</p>
</body>
</html>
