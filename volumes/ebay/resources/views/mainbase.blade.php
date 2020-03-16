<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>eBay Paypal Solution</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css?{{time()}}">
    <link rel="stylesheet" href="/css/loading/css-loader.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="/js/config.js?{{time()}}"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row alert alert-success">Ebay Paypal Solution</div>
    <div class="row">
        <!--選單-->
        <div class="col-md-2">
            <div class="list-group">
                <a href="/" class="list-group-item active">
                    RealTime Orders
                </a>
                <a href="/orderProcess"  class="list-group-item">Order Process</a>
                <a href="/search"        class="list-group-item">Order Search</a>
                <a href="/shipping"      class="list-group-item">Upload Tracking Number</a>
                <a href="/printAddress"  class="list-group-item">Order Print</a>
                <a href="/duty"          class="list-group-item">Summary</a>
                <a href="/list"          class="list-group-item">All Orders</a>
                <a href="/statistics"    class="list-group-item">Statistics</a>
                <a href="/countryBuyCount" class="list-group-item">Count Order Count</a>
                <!-- <a href="{{URL::to('/orderProcess')}}"  class="list-group-item">Order Process</a>
                <a href="{{URL::to('/search')}}"        class="list-group-item">Order Search</a>
                <a href="{{URL::to('/shipping')}}"      class="list-group-item">Upload Tracking Number</a>
                <a href="{{URL::to('/printAddress')}}"  class="list-group-item">Order Print</a>
                <a href="{{URL::to('/duty')}}"          class="list-group-item">Summary</a>
                <a href="{{URL::to('/list')}}"          class="list-group-item">All Orders</a>
                <a href="{{URL::to('/statistics')}}"    class="list-group-item">Statistics</a>
                <a href="{{URL::to('/countryBuyCount')}}" class="list-group-item">Count Order Count</a> -->

            </div>

            <div class="list-group">
                <a href="#" class="list-group-item disabled">
                    eBay Function
                </a>
                <a href="{{URL::to('/eBayTxnId')}}" class="list-group-item">Update eBay TxnId</a>
            </div>
        </div>
        <!--內容-->
        <div class="col-md-10">
            @yield('dataContainer')
        </div>
    </div>
</div>


</body>
</html>