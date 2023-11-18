<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="html5,css3">
    <meta name="author" content="TES">
    <meta name="description" content="80fekra invoice">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;700&family=Reem+Kufi:wght@400;700&display=swap"
        rel="stylesheet">
    <title>80fekra invoice</title>
</head>

<body style="direction: rtl; font-family: 'Montserrat', sans-serif; padding: 0; margin: 0;background-color: rgba(120, 210, 248, 0.2);">
<!-- start home -->
<section class="home" style="padding: 0;">
    <div class="layer"
         style="width: 100%; background-color: rgba(120, 210, 248, 0.2); margin: auto;  position: relative; ">

        <div class="content" style="padding: 15px;">
            <div class="container" style="padding: 20px 30px;">


                        <div class="logo" style="padding-right: 20px;align-items: flex-end;text-align: left">
                            <img src="{{url('/pdf_logo.png')}}" alt="80 fekra logo" style="width: 75px;height: 100px">
                        </div>



                <div class="row invoice-number" style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px;" >
                    <div class="invoice-date" style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
                        <p style="margin: 5px 0 10px 0; font-size: 20px;"><strong style="font-weight: bold;">رقم
                                الفاتورة : </strong> {{$order->id}} </p>
                        <p style="margin: 5px 0; font-size: 14px;"> {{\Carbon\Carbon::parse($order->created_at)->format('Y-m-d')}} </p>
                    </div>
                </div>
                <div class="row details"
                     style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 50px;">
                    <div class="reciever-details">
                        <h3 style="font-size: 18px; font-weight: bold; margin: 10px 0;">فاتورة الى : </h3>
                        <p style="margin: 5px 0; font-size: 14px;">{{$order->address->f_name}}</p>
                        <p style="margin: 5px 0; font-size: 14px;">{{$order->address->phone}}</p>
                        <p style="margin: 5px 0; font-size: 14px;">{{$order->address->address}}</p>
                    </div>
                </div>
                <div class="row table-data" style="margin-bottom: 40px;">
                    <table style="width:100%; border-collapse: collapse; overflow: hidden;">
                        <tr style="width: 100%; border-bottom: 1px solid #888; border-top: 1px solid #888;">
                            <th style="text-align: right; padding: 14px 5px; font-size: 18px; font-weight: bold;">
                                العنصر</th>
                            <th
                                style="text-align: right; padding: 14px 5px; text-align: center; font-size: 18px; font-weight: bold;">
                                الكمية</th>
                            <th
                                style="text-align: right; padding: 14px 5px; text-align: center; font-size: 18px; font-weight: bold;">
                                سعر القطعه</th>
                            <th
                                style="text-align: right; padding: 14px 5px; text-align: center; font-size: 18px; font-weight: bold;">
                                السعر الكلى</th>
                        </tr>
                        @foreach($order->orderDetails as $details)
                        <tr style="width: 100%; border-bottom: 1px solid #888;">
                            <td style="text-align: right; padding: 12px 5px; max-width: 300px; ">{{$details->product->title_ar}}
                            </td>
                            <td
                                style="text-align: right; padding: 12px 5px; width: 80px; text-align: center; overflow: hidden;">
                                {{$details->qty}}</td>
                            <td
                                style="text-align: right; padding: 12px 5px; width: 80px; text-align: center; overflow: hidden;">
                                {{$details->price}}</td>
                            <td
                                style="text-align: right; padding: 12px 5px; width: 80px; text-align: center; overflow: hidden;">
                                {{$details->total}}</td>
                        </tr>
                        @endforeach


                        <tr style="width: 100%;">
                            <td></td>
                            <td></td>
                            <td
                                style="text-align: right; padding: 14px 3px; width: 120px; text-align: center; overflow: hidden; font-size: 18px; font-weight: bold;">
                                المجموع الفرعي</td>
                            <td
                                style="text-align: right; padding: 14px 3px; width: 120px; text-align: center; overflow: hidden;">
                                {{$order->subtotal}}</td>
                        </tr>
                        <tr style="width: 100%;">
                            <td></td>
                            <td></td>
                            <td
                                style="text-align: right; padding: 14px 3px; width: 120px; text-align: center; overflow: hidden; font-size: 16px; font-weight: bold; ">
                                الخصم  </td>
                            <td
                                style="text-align: right; padding: 14px 3px; width: 120px; text-align: center; overflow: hidden;">
                                {{$order->discount}}</td>
                        </tr>
                        <tr style="width: 100%;">
                            <td></td>
                            <td></td>
                            <td
                                style="text-align: right; padding: 14px 3px; width: 120px; text-align: center; overflow: hidden; font-size: 16px; font-weight: bold; border-bottom: 1px solid #888;">
                                مصاريف الشحن </td>
                            <td
                                style="text-align: right; padding: 14px 3px; width: 120px; text-align: center; overflow: hidden; border-bottom: 1px solid #888;">
                                {{$order->shipping_cost}}</td>
                        </tr>
                        <tr style="width: 100%;">
                            <td></td>
                            <td></td>
                            <td
                                style="text-align: right; padding: 14px 3px; width: 120px; text-align: center; overflow: hidden; font-size: 20px; font-weight: bold">
                                السعر الكلى</td>
                            <td
                                style="text-align: right; padding: 14px 3px; width: 120px; text-align: center; overflow: hidden">
                                {{$order->total}}</td>
                        </tr>

                    </table>
                </div>
                <div class="row message" style="margin-bottom: 40px;">
                    <h2 style="font-size: 20px; color: #555; margin-right: 10px;">" شكرًا لثقتك في منتجات صناع العباقرة 80 فكرة "</h2>
                </div>
                <div class="row payment"
                     style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                    <table>
                        <tr>
                            <td class="payment-info" style="padding: 5px;">
                                <p><strong style="color: #555;">اسم الشركة:</strong> 80 فكرة</p>
                                <p><strong style="color: #555;">رقم الهاتف:</strong> 01067995833</p>
                                <p style="white-space: pre;"><strong style="color: #555;">العنوان:</strong><br>٩ ش محمد النادي، تقاطع مكرم عبيد مع مصطفى النحاس، مدينة نصر، القاهرة</p>
                            </td>
                            <td class="user-info" style="padding: 5px;">
{{--                                <h4 style="font-family: 'Reem Kufi', sans-serif; font-size: 20px; margin: 0;">Samira Hadid</h4>--}}
{{--                                <p style="font-size: 14px; margin: 0;">123 Anywhere St., Any City, ST 12345</p>--}}
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- end home -->
</body>

</html>
