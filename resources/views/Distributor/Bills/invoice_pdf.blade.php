<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Template</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 10mm; /* Adjust margin as needed */
            }
        }
        body {
            font-family: Arial, sans-serif; /* Changed font to Arial */
            margin: 0;
            padding: 10px; /* Reduced padding */
            color: #333; /* Dark gray text */
            max-width: 800px; /* A4 width */
            margin: auto; /* Center the invoice */
            font-size: 10px; /* Set text size to 10px */
            background-color: #fff; /* Simple background */
        }
        .invoice-container {
            padding: 10px; /* Adjusted padding for the invoice */
            border-radius: 8px;
            border: 1px solid #ddd; /* Added outer border */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h3, h4 {
            color: #000; /* Black for all headings */
            margin: 0;
        }
        h1 {
            font-size: 20px; /* Reduced title font size */
        }
        h2 {
            font-size: 18px; /* Reduced font size */
        }
        h3 {
            font-size: 16px; /* Reduced font size */
        }
        h4 {
            font-size: 14px; /* Reduced font size */
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 5px; /* Reduced margin */
        }
        .company-logo img {
            width: 60px; /* Adjusted logo size */
        }
        .company-details {
            margin-bottom: 5px; /* Reduced margin */
        }
        .invoice-details {
            text-align: right; /* Align invoice details to the right */
            margin-bottom: 5px; /* Reduced margin */
        }
        .table {
            width: 100%; /* Adjust table width */
            border-collapse: collapse;
            margin-bottom: 10px; /* Reduced margin */
        }
        .table th, .table td {
            padding: 5px; /* Increased padding for better content size */
            border: 1px solid #ddd;
            text-align: left; /* Align text to the left */
            font-size: 10px; /* Set a larger font size for table */
        }
        .table th {
            background-color: #3498db; /* Blue background for header */
            color: #fff; /* White text */
        }
        .total {
            font-weight: bold;
            font-size: 1em; /* Slightly reduced font size */
            text-align: right; /* Align total to the right */
            color: #000; /* Black for total amount text */
        }
        .total-line {
            border-top: 1px solid #000; /* Black line under total section */
            margin-top: 5px; /* Space between total and line */
            margin-bottom: 10px; /* Space between line and next section */
        }
        .btn-print {
            background-color: #2980b9;
            color: #fff;
            border: none;
            padding: 6px 12px; /* Reduced padding */
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em; /* Reduced font size */
        }
        .btn-print:hover {
            background-color: #3498db;
        }
        .terms {
            text-align: left;
        }
        /* Enhanced border styling */
        .item-description {
            border: 1px solid #3498db; /* Border for item description */
            padding: 5px; /* Padding for better spacing */
            border-radius: 5px; /* Rounded corners */
            background-color: #eaf5ff; /* Light blue background */
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>Invoice</h1>
        </div>

        <div class="company-details">
            <div class="company-logo">
                <img src="../../../app-assets/images/logo/logo-80x80.png" alt="Company Logo">
            </div>
            <h4>Baba Neem karoli Traders (Trade/Retail-Wholesaler)</h4>
            <br>
            <b>Mr.PANKAJ KESARWANI S/O KANDHAI LAL KESARWANI</b>
            <p>14/1, CHURCH ROAD GWALTOLI KANPUR Kanpur Nagar, Uttar Pradesh-208001</p>
        </div>

        <div class="invoice-details">
            <h3>Invoice Details</h3>
            <p>Invoice No: <strong>{{$order->bill_no}}</strong></p>
            <p>Order Date: <strong>{{$order->order_date}}</strong></p>
            <p>Order Time: <strong>{{$order->order_time}}</strong></p>
        </div>

        <div class="customer-details">
            <h3>Bill To</h3>
            <p><strong>{{Auth::user()->name}}</strong></p>
            <p class="address">{{Auth::user()->address}}</p>
        </div>

        <div class="items-details">
            <h3>Invoice Items</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>S No</th>
                        <th class="item-description">Item & Description</th> <!-- Adjusted class for item description -->
                        <th>Weight</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderDetail as $key =>$order)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>
                            <p>{{$order->product_name}} ({{$order->company_name}})</p>
                            <p class="text-muted">{{Str::limit($order->product_description, 30, '...')}}</p>
                        </td>
                        <td>{{$order->product_weight}}</td>
                        <td>{{$order->product_quantity}}</td>
                        <td>{{$order->item_per_cred}}</td>
                        <td><i class="fa-solid fa-indian-rupee-sign"></i>{{$order->amount}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total">
            <h3>Total Amount</h3>
            <p>Sub Total: <strong><i class="fa-solid fa-indian-rupee-sign"></i>{{$order->total_amount}}</strong></p>
            <p>TAX (12%): <strong>0</strong></p>
            <p>Total: <strong><i class="fa-solid fa-indian-rupee-sign"></i>{{$order->total_amount}}</strong></p>
        </div>
        <div class="total-line"></div> <!-- Added line below total section -->

    </div>
</body>
</html>
