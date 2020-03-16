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
        <div class="panel panel-default" style="width: 35%; margin: 0 auto; min-width: 350px">
            <div class="panel-heading">
                <h3 class="panel-title">Login Console</h3>
            </div>
            <div class="panel-body">
                <form action="{{URL::to('/processLogin')}}" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="exampleInputEmail1">Account : </label>
                        <input type="text" class="form-control" name="account"  placeholder="Account">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password : </label>
                        <input type="password" class="form-control" name="password"  placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-default">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>


</body>
</html>