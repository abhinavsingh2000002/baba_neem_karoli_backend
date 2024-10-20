<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Table</title>
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
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

@media (max-width: 600px) {
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
}

    </style>
</head>
<body>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Distributor Name</th>
                    @foreach($all_product as $product)
                    <th>{{$product->product_no}}{{$product->product_name}} ({{$product->company_name}})({{$product->product_quantity}})</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($order_detail as $order)
                <tr>
                    <td>{{$order->user_id}}</td>
                    <td>{{$order->product_quantity}}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>0</td>
                    <td>Fcm 1L.= 0</td>
                </tr>
                @endforeach
                <tr>
                    <td>GOLU, Dahi Chauki</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>0</td>
                    <td>Fcm 500ml= 0</td>
                </tr>
                <!-- More rows as needed -->
            </tbody>
        </table>
    </div>
</body>
</html>
