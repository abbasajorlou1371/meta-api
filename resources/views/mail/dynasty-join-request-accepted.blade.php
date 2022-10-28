<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body{
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body>
<h2 style="text-align: center">افزایش سلسله</h2>
<p>
    ارسال به {{ $joinRequest->relation }}  {{ $joinRequest->to_user }}
</p>
<p>
    تاریخ درخواست : {{ \Morilog\Jalali\Jalalian::forge($joinRequest->created_at)->format('Y/m/d') }}
</p>
<p>
    شما در این تاریخ درخواست کرده اید که شهروند {{ $joinRequest->to_user }} {{ $joinRequest->relation }} شما میباشد و در سلسله شما به عنوان {{ $joinRequest->relation }} عضو گردد
</p>
</body>
</html>
