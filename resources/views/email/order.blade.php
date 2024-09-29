<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>
<body>
    <h1>Invoice for Order #{{ $order->id }}</h1>
    <p>Thank you for your order!</p>
    <p>Here are the details:</p>
    <ul>
        @foreach($order->orderItems as $item)
            <li>{{ $item->product->title }} - Qty: {{ $item->qty }} - Price: ${{ number_format($item->price, 2) }}</li>
        @endforeach
    </ul>
    <p>Total: ${{ number_format($order->total, 2) }}</p>
</body>
</html>
