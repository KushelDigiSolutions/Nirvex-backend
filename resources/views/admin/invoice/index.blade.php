<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        h2 {
            margin-top: 0;
        }
    </style>
</head>
<body class="container">
    <h2>Invoice Details</h2>
    <table>
        <tr>
            <th>Billing Address</th>
            <td>Nelib Swargiary, Bongaigaon Refinery 1st Gate, Bongaigaon Refinery, Chirang District 783385 Assam</td>
        </tr>
        <tr>
            <th>Order ID</th>
            <td>#{{$order->id}}</td>
        </tr>
        <tr>
            <th>Order Date</th>
            <td>{{$order->created_at}}</td>
        </tr>
        <tr>
            <th>Invoice Date</th>
            <td>{{$order->created_at}}</td>
        </tr>
        <tr>
            <th>Sold By</th>
            <td>Nirvix</td>
        </tr>
        <tr>
            <th>GSTIN</th>
            <td>29AACCF0683K1ZD</td>
        </tr>
    </table>

    <h2>Product Details</h2>
    <table>
        <tr>
            <th>Description</th>
            <th>Qty</th>
            <th>Price (₹)</th>
            <th>Discount (₹)</th>
            <th>Total (₹)</th>
        </tr>
        @foreach( $orderItems as $item)
        <tr>
            <td>{{$item->variant->name}}</td>
            <td>{{$item->qty}}</td>
            <td>₹{{$item->sale_price}}</td>
            <td>₹0.0</td>
            <td>₹{{$item->total_price}}</td>
        </tr>
        @endforeach
         <tr>
            <th colspan="4">Tax {{$order->total_tax}}%</th>
            <th>₹{{$tax = ($order->total_price*$order->total_tax)/100}}</th>
        </tr>
        <tr>
            <th colspan="4">Grand Total (₹)</th>
            <th>₹{{($order->total_price + $tax)}}</th>
        </tr>
    </table>

    <h2>Shipping Details</h2>
    <table>
        <tr>
            <th>Ship To</th>
            <td>{{$address->name}}, {{$address->address1}}, {{$address->address2}}, {{$address->city}}, {{$address->state}}, {{$address->landmark}}, {{$address->pincode}}</td>
        </tr>
    </table>

    <h2>Signature</h2>
    <p>Nirvix - Authorized Signatory</p>
</body>
</html>

<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const element = document.querySelector('.container');
            
            const options = {
                margin: 10,
                filename: 'invoice.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(options).from(element).save().then(() => {
                window.close(); // Close the page after downloading
            });
        });
    </script>