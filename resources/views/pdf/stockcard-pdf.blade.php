<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
}
.container {
    width: 90%;
    margin: 30 auto;
    padding: 10px;
    box-sizing: border-box;
}
.header,
.footer {
    width: 100%;
    text-align: left;
    margin-bottom: 10px;
}
.title {
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 10px;
}
.btn-container {
    margin: auto;
    text-align: right;
}
.download-btn {
    font-weight: bold;
    background-color: #4CAF50;
    border-radius: 5px;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}
.info-table {
    width: 100%;
    margin-bottom: 10px;
}
.info-table td {
    /* padding: 2px 5px; */
    vertical-align: top;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}
.data-table th,
.data-table td {
    border: 1px solid #000;
    padding: 4px;
    text-align: center;
    font-size: 11px;
}
.footer {
    margin-top: 10px;
    font-weight: bold;
}
</style>
<div class="container">
    <div class="title">STOCK CARD</div>
    <table class="info-table">
        <tr>
            <td><strong>Product: </strong> </td>
            <td><strong>Lot no: </strong> 01021020</td>
        </tr>
        <tr>
            <td><strong>UOM:</strong> Each</td>
            <td><strong>Date:</strong> April 01, 2025 to April 30, 2025</td>
        </tr>
        <tr>
            <td><strong>Date/Time Generated:</strong> {{ now() }}</td>
            <td><strong>Expiration Date:</strong> 06/30/2025</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>DATE</th>
                <th>LoT/Serial No.</th>
                
                <th>Quantity</th>
                <th>Total Items</th>
                <th>Transaction Type</th>
                <th>Warehouse</th>
                <th>Customer/Supplier Name</th>
                <th>Document No.</th>
                <th>Remarks</th>
                <th>End User</th>
                <th>Approver</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockcards as $item)
                <tr>
                    <td style="text-align: left;">{{ $item['transaction_date'] }}</td>
                    <td style="text-align: left;">{{ $item['serialNo'] ?? $item['lotNo'] }}</td>
                    <td style="text-align: left;">{{ $item['quantity'] }}</td>
                    <td style="text-align: left;">{{ $item['balance'] }}</td>
                    <td style="text-align: left;">{{ $item['transaction_type'] }}</td>
                    <td style="text-align: left;">{{ $item['warehouse'] }}</td>
                    <td style="text-align: left;">{{ $item['reference'] }}</td>
                    <td style="text-align: left;">{{ $item['document_no'] }}</td>
                    <td style="text-align: left;">{{ $item['remarks'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="8" style="text-align: left;">Total records: {{ count($stockcards) }}</td>
            </tr>
        </tbody>
    </table>
</div>
