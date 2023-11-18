<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Order # {{$order->id}}</title>
    <style>
        /* Add your CSS styles here */
        * { font-family: DejaVu Sans, sans-serif; }

    </style>
</head>
<body dir="rtl" style="direction: rtl">
<div class="invoice">
    <div class="header">
        <h1>Order # {{$order->id}}</h1>
    </div>
    <div class="content">
        <div class="invoice-details">
            <div class="right">
                <p>فاتورة رقم :
                    {{$order->id}}</p>
                <p>التاريخ:
                    {{\Carbon\Carbon::parse($order->created_at)->format('Y-m-d')}}</p>
            </div>
            <div class="right">
                <p>اسم العميل:
                    {{$order->address->f_name}}  {{$order->address->l_name}}  </p>
                <p>البريد الالكتروني:
                    {{$order->address->email}}</p>
                <p>رقم التلفون:
                    {{$order->address->phone}}</p>
            </div>
        </div>
        <table style="table-border-color-dark: black">
            <thead>
            <tr>
                <th>اسم المنتج</th>
                <th>الكمية</th>
                <th>سعر المنتج</th>
                <th>الاجمالى</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->orderDetails as $details)
                <tr>
                    <td>{{$details->product->title_en}}</td>
                    <td>{{$details->qty}}</td>
                    <td>{{$details->price}}</td>
                    <td>{{$details->total}}</td>

                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr></tr>
            <tr></tr>
            <tr>
                <td colspan="3"></td>
                <td>اجمالى الطلب: {{$order->total}}</td>
            </tr>
            </tfoot>
        </table>
    </div>
    <div class="footer">
        <p>شكرآ لطلبكم من 80 فكرة!</p>
    </div>
</div>
</body>
</html>
