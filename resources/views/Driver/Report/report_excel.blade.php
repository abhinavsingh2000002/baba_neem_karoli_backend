<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #333;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Distributor Name</th>
                <th>Order Number</th>
                @foreach($all_product as $product)
                    <th>{{ $product->product_no }} {{ $product->product_name }} ({{ $product->company_name }}) ({{ $product->product_quantity }})</th>
                @endforeach
                <th>Total Quantity</th> <!-- New Total Quantity Column -->
                {{-- <th>Total Amount</th>   <!-- New Total Amount Column --> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($order as $order)
            <tr>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->order_no }}</td>
                @php
                    $totalQuantity = 0; // Initialize total quantity
                    $totalAmount = 0;   // Initialize total amount
                @endphp
                @foreach($all_product as $product)
                    @php
                        $detail = $order->orderDetails->firstWhere('product_no', $product->product_no);
                        $quantity = $detail ? $detail->product_quantity : 0;
                        $amount = $detail ? $detail->amount : 0; // Assuming there's an amount field in the detail

                        $totalQuantity += $quantity; // Add to total quantity
                        $totalAmount += $amount;       // Add to total amount
                    @endphp
                    <td>{{ $quantity }}</td>
                @endforeach
                <td>{{ $totalQuantity }}</td> <!-- Display total quantity -->
                {{-- <td>{{ $totalAmount }}</td>   <!-- Display total amount --> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
