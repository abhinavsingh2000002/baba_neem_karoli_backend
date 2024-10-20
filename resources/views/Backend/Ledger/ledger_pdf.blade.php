<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Report</title>
    <style>
        body {
            background-color: #ffffff; /* White background */
            color: #000000; /* Dark black text color */
            font-family: Arial, sans-serif; /* Ensure a generic font is used */
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        thead {
            background-color: #007bff;
            color: white;
        }
        th, td {
            padding: 8px; /* Reduced padding for smaller size */
            text-align: left;
            border: 1px solid #dee2e6; /* Borders for PDF clarity */
        }
        th:nth-child(1),
        td:nth-child(1) {
            width: 15%; /* Bill No column */
        }
        th:nth-child(2),
        td:nth-child(2) {
            width: 15%; /* Order No column */
        }
        th:nth-child(3),
        td:nth-child(3) {
            width: 20%; /* Date & Time column */
        }
        th:nth-child(4),
        td:nth-child(4) {
            width: 25%; /* Distributor Name column */
        }
        th:nth-child(5),
        td:nth-child(5) {
            width: 25%; /* Total Amount column */
        }
        tbody tr:hover {
            background-color: #f1f1f1; /* Hover effect may not apply in PDF */
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <h1>Ledger Report</h1>
    <table>
        <thead>
            <tr>
                <th>Bill No</th>
                <th>Order No</th>
                <th>Date & Time</th>
                <th>Distributor Name</th>
                <th>Total Amount</th>
                  <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bills as $bill)
            <tr>
                <td>{{ $bill->bill_no }}</td>
                <td>{{ $bill->order_no }}</td>
                <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d-m-Y') }} {{ \Carbon\Carbon::parse($bill->bill_time)->format('h:i A') }}</td>
                <td>{{ $bill->name }}</td>
                <td>{{ number_format($bill->total_amount, 2) }}</td> <!-- Rupee symbol -->
               <td>
                @switch($bill->order_status)
                    @case(0)
                        Failed
                        @break
                    @case(1)
                        Pending
                        @break
                    @case(2)
                        Confirmed
                        @break
                    @case(3)
                        Delivered
                        @break
                    @default
                        Unknown
                @endswitch
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total-section">
        Total: {{ number_format($bills->sum('total_amount'), 2) }} <!-- Rupee symbol -->
    </div>
</body>
</html>
