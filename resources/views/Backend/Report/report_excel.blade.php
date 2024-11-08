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
                <th>Total Quantity</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $columnTotals = array_fill(0, count($all_product), 0);
                $grandTotalQuantity = 0;
                $grandTotalAmount = 0;
            @endphp

            @foreach($order as $order)
            <tr>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->order_no }}</td>
                @php
                    $totalQuantity = 0;
                    $totalAmount = 0;
                @endphp
                @foreach($all_product as $index => $product)
                    @php
                        $detail = $order->orderDetails->firstWhere('product_no', $product->product_no);
                        $quantity = $detail ? $detail->product_quantity : 0;
                        $amount = $detail ? $detail->amount : 0;

                        $totalQuantity += $quantity;
                        $totalAmount += $amount;
                        $columnTotals[$index] += $quantity;
                    @endphp
                    <td>{{ number_format($quantity, 2) }}</td>
                @endforeach
                <td>{{ number_format($totalQuantity, 2) }}</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
                @php
                    $grandTotalQuantity += $totalQuantity;
                    $grandTotalAmount += $totalAmount;
                @endphp
            </tr>
            @endforeach

            <!-- Total Row -->
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td>Total</td>
                <td></td>
                @foreach($columnTotals as $total)
                    <td>{{ number_format($total, 2) }}</td>
                @endforeach
                <td>{{ number_format($grandTotalQuantity, 2) }}</td>
                <td>{{ number_format($grandTotalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
