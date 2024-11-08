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
            padding: 20px;
            font-size: 3px;
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
            font-size: 15px;
        }

        tbody td {
            padding: 10px;
            border: 2px solid #333;
            text-align: left;
            color: black;
            font-size: 15px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        @media print {
            body {
                background-color: white;
                color: black;
            }

            .table-container {
                border: none;
                padding: 0;
            }

            table {
                border: none;
                margin: 0;
            }

            thead th {
                border: 1px solid #333;
            }

            tbody td {
                border: 1px solid #333;
            }

            tr {
                page-break-inside: avoid; /* Avoid breaking rows across pages */
            }
        }

        /* Responsive styles for mobile view */
        /* @media (max-width: 600px) {
            thead {
                display: none;
            }

            tr {
                display: block;
                margin-bottom: 20px;
                border-bottom: 2px solid #333;
            }

            td {
                display: block;
                text-align: right;
                padding-left: 50%;
                position: relative;
                border: 1px solid #333;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 45%;
                padding-left: 10px;
                font-weight: bold;
                text-align: left;
                border-right: 1px solid #333;
            }
        } */
    </style>
</head>
{{-- @dd($all_product,$order); --}}
<body>
    <div class="table-container">
        <h1 style="text-align: center;">Order Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Distributor Name</th>
                    <th>Order Number</th>
                    @foreach($all_product as $product)
                    <th>{{ $product->product_no }}</br> {{ $product->product_name }}</br> ({{ $product->company_name }}) </br>({{ $product->product_quantity }})</th>
                    @endforeach
                    <th>Total Quantity</th> <!-- New Total Quantity Column -->
                    <th>Total Amount</th>   <!-- New Total Amount Column -->
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotalQuantity = 0;
                    $grandTotalAmount = 0;
                    $productTotals = array_fill(0, count($all_product), 0);
                @endphp
                @foreach($order as $order)
                    @php
                        $totalQuantity = 0;  // Initialize for each order
                        $totalAmount = 0;    // Initialize for each order
                    @endphp
                <tr>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ $order->order_no }}</td>
                    @foreach($all_product as $index => $product)
                        @php
                            $detail = $order->orderDetails->firstWhere('product_no', $product->product_no);
                        @endphp
                        @if($detail)
                            <td>{{ number_format($detail->product_quantity, 1) }}</td>
                            @php
                                $totalQuantity += $detail->product_quantity;
                                $totalAmount += $detail->amount;
                                $productTotals[$index] += $detail->product_quantity;
                            @endphp
                        @else
                            <td></td>
                        @endif
                    @endforeach
                    <td>{{ number_format($totalQuantity, 1) }}</td>
                    <td>{{ number_format($totalAmount, 2) }}</td>
                    @php
                        $grandTotalQuantity += $totalQuantity;
                        $grandTotalAmount += $totalAmount;
                    @endphp
                </tr>
                @endforeach
                <!-- Total Row -->
                <tr style="font-weight: bold; background-color: #e6e6e6;">
                    <td>Total</td>
                    <td></td>
                    @foreach($productTotals as $total)
                        <td>{{ number_format($total, 1) }}</td>
                    @endforeach
                    <td>{{ number_format($grandTotalQuantity, 1) }}</td>
                    <td>{{ number_format($grandTotalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>

    </div>
</body>
</html>
