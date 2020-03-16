<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>eBay Test</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body class="docs language-php">
<div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <td>項次</td>
                <td>物品</td>
                <td>交易狀態</td>
                <td>數量</td>
                <td>金額</td>
                <td>付款</td>
                <td>貨幣</td>
                <td>買家</td>
                <td>交易日期</td>
            </tr>
        </thead>
        <tbody>
            <?php
                $collection = collect($xml->TransactionArray);
                $collection->sortByDesc('CreatedDate');
                    $i= 0;
                dd($collection)  ;
            ?>
            @foreach($collection as $key => $trans)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$trans->Item->ItemID}}</td>
                    <td>{{$trans->Status->CompleteStatus}}</td>
                    <td>{{$trans->QuantityPurchased}}</td>
                    <td>{{$trans->Item->SellingStatus->CurrentPrice}}</td>
                    <td>{{$trans->AmountPaid}}</td>
                    <td>{{$trans->Item->Currency}}</td>
                    <td>{{$trans->Buyer->UserID}}</td>
                    <td>{{$trans->CreatedDate}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>