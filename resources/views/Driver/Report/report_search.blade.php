<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Report</title>
    <style>
        * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

.table-container {
    width: 100%;
    overflow-x: auto;
    background-color: white;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    border: 2px solid #333;
}

thead th {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    text-align: left;
    border: 2px solid #333;
}

tbody td {
    padding: 10px;
    border: 2px solid #333;
    text-align: left;
    color: black;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}
    </style>
</head>
<body>
    <div class="table-container">
        <h1 class="text-center">Order Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Distributor Name</th>
                    <th>Order Number</th>
                    @foreach($all_product as $product)
                        <th>{{ $product->product_no }}</br> {{ $product->product_name }}</br> ({{ $product->company_name }}) </br>({{ $product->product_quantity }})</th>
                    @endforeach
                    <th>Total Quantity</th> <!-- New Total Quantity Column -->
                    {{-- <th>Total Amount</th>   <!-- New Total Amount Column --> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($order as $order)
                <tr>
                    <td>{{ $order->user->name }}</td> <!-- Distributor Name or ID -->
                    <td>{{ $order->order_no }}</td>
                    @php
                        $totalQuantity = 0; // Initialize total quantity
                        $totalAmount = 0;   // Initialize total amount
                    @endphp
                    @foreach($all_product as $product)
                        @php
                            // Check if the orderDetails for the current product_no exists in the current order
                            $detail = $order->orderDetails->firstWhere('product_no', $product->product_no);
                        @endphp
                    {{-- @dd($detail); --}}
                        @if($detail)
                            <td>{{ $detail->product_quantity }}</td> <!-- Show quantity if product exists in order -->
                            @php
                                $totalQuantity += $detail->product_quantity; // Add to total quantity
                                $totalAmount += $detail->amount; // Assuming there's a price field in $product
                            @endphp
                        @else
                            <td></td> <!-- Otherwise, show 0 -->
                        @endif
                    @endforeach
                    <td>{{ $totalQuantity }}</td> <!-- Display total quantity -->
                    {{-- <td>{{ $totalAmount }}</td>   <!-- Display total amount --> --}}
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</body>
</html>
