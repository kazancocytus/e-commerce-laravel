<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px">

    @if ($mailData['userType'] == 'customer')
    <h1>{{ $mailData['subject'] }} :</h1>
    <h2>Your order Id is : ${{ $mailData['orders']->id }}</h2>
    @else
    <h1>{{ $mailData['subject'] }} : </h1>
    <h2>Order Id #{{ $mailData['orders']->id }}</h2>
    @endif

    <h2>Shipping Address</h2>
    <address>
        <strong>{{ $mailData['orders']->first_name . ' ' . $mailData['orders']->last_name }}</strong><br>
        {{ $mailData['orders']->address }}<br>
        {{ $mailData['orders']->city }}, {{ $mailData['orders']->zip }} {{ getCountry($mailData['orders']->country_id)->name }}<br>
        Phone : {{ $mailData['orders']->mobile }}<br>
        Email : {{ $mailData['orders']->email }}
    </address>

    <h2>Products</h2>

    <table class="table table-striped">
        <thead>
            <tr style="background: #CCC;">
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mailData['orders']->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>${{ number_format($item->price,2) }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->total) }}</td>
                </tr>
            @endforeach
                <br>
                <tr>
                    <th colspan="3" align="right">Subtotal:</th>
                        <td>${{ number_format($mailData['orders']->subtotal,2) }}</td>
                </tr>
                                                
                <tr>
                    <th colspan="3" align="right">Shipping:</th>
                        <td>${{ number_format($mailData['orders']->shipping,2) }}</td>
                </tr>

                <tr>
                    <th colspan="3" align="right">Discount  {{ !empty($mailData['orders']->coupon_code) ? '('.$mailData['orders']->coupon_code.')' : '' }}:</th>
                        <td>${{ number_format($mailData['orders']->discount,2) }}</td>
                </tr>
                                                
                <tr>                                    
                    <th colspan="3" align="right">Grand Total:</th>    
                    <td>${{ number_format($mailData['orders']->grand_total,2) }}</td>    
                </tr>
        </tbody>
    </table>

    <h2>Thank you for shopping at ZEAL E-commerce</h2>

</body>
</html>