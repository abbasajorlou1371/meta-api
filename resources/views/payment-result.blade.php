<!doctype html>
<html dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .left {
            text-align: left !important;
        }

        .right {
            text-align: right !important;
        }
    </style>

</head>

<body>


    @if (isset($payment))
        <br>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header text-center">

                            <i class="fa-sharp fa-solid fa-circle-check fa-2x" style="color: green"></i>
                            <h2 class="text-success">خرید موفق </h2>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-center">اطلاعات پرداخت در متارنگ</h5>
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td class="left"><b> شماره ارجاع : </b></td>
                                        <td class="right">{{ $payment->ref_id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"> <b>شماره کارت : </b> </td>
                                        <td class="right"> {{ $payment->card_pan }} </td>
                                    </tr>
                                    <tr>
                                        <td class="left"> <b> نام پذیرنده : </b> </td>
                                        <td class="right">{{ $payment->gateway }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"> <b>مبلغ : </b> </td>
                                        <td class="right">{{ $payment->amount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"> <b>محصول خریداری شده : </b> </td>
                                        @switch($payment->product)
                                            @case('red')
                                                <td class="right">رنگ قرمز</td>
                                            @break

                                            @case('blue')
                                                <td class="right">رنگ آبی</td>
                                            @break

                                            @case('yellow')
                                                <td class="right">رنگ زرد</td>
                                            @break

                                            @case('psc')
                                                <td class="right">psc</td>
                                            @break

                                            @default
                                        @endswitch
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <a href="#" class="btn btn-success mb-2 ">برگشت به صفحه اصلی </a>
                            <br>
                            <div class="card-footer text-muted py-2">
                                ممنونیم از خرید شما
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <div class="row justify-content-center my-auto">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header text-center" style="background-color: rgb(85, 33, 33)">
                            <i class="fa-sharp fa-solid fa-exclamation fa-2x" style="color: rgb(182, 52, 52)"></i>
                            <h2 class="text-danger">{{ $message }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif





    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
</body>

</html>
