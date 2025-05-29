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
    <div class="title">LIST OF OUT TRANSACTIONS THAT HAVE PR NUMBER FROM</div>
    <div class="title" style="text-align: center;">March 10, 2025 - April 10, 2025</div> {{-- This line was Made By UNFORGIVABLE BACK-END DEVELOPER --}}
    @if(!Route::is('show.stockcard.pdf') )
       <div class="btn-container">
            <a href="/generate-stockcard-pdf" target='_blank' class="download-btn">Download PDF</a>
        </div>
    @endif
    <table class="info-table">
        <tr>
            <td><strong>Date/Time Generated:</strong> {{ now() }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>DATE POSTED</th>
                <th>LOT NO.</th>
                <th>PR NO.</th>
                <th>PRODUCT CODE</th>
                <th>UOM</th>
                <th>QUANTITY</th>
                <th>ADDED BY</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="13" style="text-align: left;">Total records: 0</td>
            </tr>
        </tbody>
    </table>
</div>
