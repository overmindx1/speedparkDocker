<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//Ajax REST
Route::group(['prefix' => 'v1'] , function() {
    //Get
    Route::get('/getRealTimeOrder/{id?}'        , 'AjaxController@getRealTimeIpnOrder');
    //
    Route::get('/getOrderListByPage/{page?}'    , 'AjaxController@getOrderListByPage');
    Route::get('/getDutyRecordByPage/{page?}'   , 'AjaxController@getDutyRecordByPage');

    //Post
    //更新訂單的自我記事
    Route::post('/updateOrderSelfMemo'      , 'AjaxController@updateOrderSelfMemo');
    //取得要撿貨的訂單資料
    Route::post('/getOrderDetailForProcess' , 'AjaxController@getOrderDetailForProcess');
    //更新訂單的處理狀態
    Route::post('/updateOrderStatusById'    , 'AjaxController@updateOrderStatusById');
    //取得要列印的地址資料
    Route::post('/getAddressInfoByIds'      , 'AjaxController@getAddressInfoByIds');
    //搜尋訂單
    Route::post('/getOrderBySearch'         , 'AjaxController@getOrderBySearchType');
    //更新訂單的送貨資料
    Route::post('/updateOrderShippingData'  , 'AjaxController@updateOrderShippingData');

    //刪除訂單的送貨紀錄
    Route::post('/deleteShipRecordByOrderId', 'AjaxController@deleteShipRecordByOrderId');

    //
    Route::post('/getStatisticsByDate'      , 'AjaxController@getStatisticsByDate');
    //新增責任資料
    Route::post('insertNewDutyRecord'       , 'AjaxController@insertNewDutyRecord');
    //修改責任資料
    Route::post('updateDutyRecord'          , 'AjaxController@updateDutyRecord');
    //取得相關的id紀錄(檢貨 , 貨運 , 地址單)
    Route::post('/getRecord'                , 'AjaxController@getRecord');
    Route::post('/updateRecord'             , 'AjaxController@updateRecord');

    Route::post('/getBuyCountryCountByDateRange' , 'AjaxController@getBuyCountryCountByDateRange');

    //eBay功能 -> 更新TxnId
    Route::post('/updateEbayItemTxnIdByOrderId' , 'AjaxController@updateEbayItemTxnIdByOrderId');
});


//主要頁面
Route::get('login' , 'LoginController@showLoginPage');
Route::post('processLogin' , 'LoginController@processLogin');
//主要處理頁面IPN
Route::post('ipn' , 'OrderController@getIpn');
//主要處理頁面IPN
Route::get('/', 'OrderController@showRealTimeOrder');

Route::get('/shipping'      , 'OrderController@showShippingPage');
Route::get('/orderProcess'  , 'OrderController@showProcessOrderPage');
Route::get('/printAddress'  , 'OrderController@showPrinterPage');
Route::get('/printTemplate' , 'OrderController@showPrinterTemplatePage');
Route::get('/search'        , 'OrderController@showSearchPage');
Route::get('/list'          , 'OrderController@showOrderListPage');
Route::get('/duty'          , 'OrderController@showDutyRecordListPage');
Route::get('/statistics'    , 'OrderController@showStatisticsPage');
Route::get('/countryBuyCount' , function(){
    return view('countryBuyCount');
});

//Ebay

Route::get('/eBayTxnId' , 'OrderController@showEbayUpdateTxnId');

Route::get('/paypals' , 'OrderController@showPaypal');

Route::get('/addressInputs' , function(){
   return view('addressInput');
});
Route::get('/addressInputData' , 'AjaxAddressController@getRealTimeIpnOrder' );
Route::post('/updateAddress' , 'AjaxAddressController@getUpdateAddress' );

//LineNotify
Route::get('/ipnLineNotifyCb' , 'OrderController@lineNotifyCallBack');

Route::get('test2' , function(){
    /*$config = config('ebay.joeyangair2011');
    $ebaySession = new App\Ebay\eBaySession(
        $config['userToken_'],
        $config['devID_'],
        $config['appID_'],
        $config['certID_'],
        $config['serverUrl_'],
        $config['compatabilityLevel'],
        0,
        'GetItem'
    );
    $request = '<?xml version="1.0" encoding="utf-8" ?><GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents"><RequesterCredentials><eBayAuthToken>' . $config['userToken_'] . '</eBayAuthToken></RequesterCredentials><DetailLevel>ReturnAll</DetailLevel><ItemID>110786377786</ItemID><OutputSelector>Seller</OutputSelector></GetItemRequest>';
    $request = trim($request);
    $response = $ebaySession->sendHttpRequest($request);
    $object = simplexml_load_string($response);

    dd([$object[0]->Errors->LongMessage , $ebaySession , $object]);

    //$sellerId = $object->Item->Seller->UserID;
    //return $sellerId;*/
    echo date('Y-m-d H:i:s');//phpinfo();
    //echo phpinfo();
});
Route::get('test' , function(){
    //echo 111;
    $config = config('ebay.rapido.ltd');
    $ebaySession = new App\Ebay\eBaySession(
        $config['userToken_'],
        $config['devID_'],
        $config['appID_'],
        $config['certID_'],
        $config['serverUrl_'],
        $config['compatabilityLevel'],
        0,
        'GetOrders' );

    //$post = \Request::all();
    // $orderModel = new App\OrderList();
    // $order = $orderModel->find(20784);
    // if(is_null($order)) {
        // return json_encode(['success' => false , 'message' => 'Can\'t Found Order-Db']);
    // }
    //$items = \ DB::table('itemsImage')->where('path' , '[]')->get();

    



    //$eBayQuery = new App\Ebay\Query('joeyangair2011' , 'GetItem');
    //$object = $eBayQuery->getPicUrlByItemID(180647065817);
    /*foreach($items as $item) {
        $itemId = $item->itemId; 
        $request = '<?xml version="1.0" encoding="utf-8" ?><GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents"><RequesterCredentials><eBayAuthToken>' . $eBayQuery->config['userToken_'] . '</eBayAuthToken></RequesterCredentials><DetailLevel>ReturnAll</DetailLevel><ItemID>'.$itemId.'</ItemID><OutputSelector>PictureDetails</OutputSelector></GetItemRequest>';
        $request = trim($request);
        $response = $eBayQuery->ebaySession->sendHttpRequest($request);
        $object = simplexml_load_string($response);
        if($object->Ack == 'Success') {
            if(isset($object->Item->PictureDetails->PictureURL)) {
                $json = json_encode (  (array) $object->Item->PictureDetails->PictureURL);
            } else {
                $json= json_encode( (array) $object->Item->PictureDetails->GalleryURL);
            }
            DB::table('itemsImage')
           ->where('id', $item->id)
            ->update(['path' => $json]);
            echo 'updated : ' . $item->itemId . ' : ' .$json .'<br />';
        }
    }*/
    
    
    
    // $orderModel = new App\OrderList();
    // $getOrder = $orderModel->where('paypalTxnId' , '3A845305KU880170S')->get();
    // $getOrder->
    // dd($getOrder->first());
    //$getOrder = $this->orderList->where('paypalTxnId', '=', $items->get('txn_id'))->get();

    // $startTime = (gmdate("Y-m-d\TH:i:s.00\Z" , strtotime('2019-08-01 14:26:06'.'-10 mins')));
    // $endTime = (gmdate("Y-m-d\TH:i:s.00\Z" , strtotime('2019-08-01 14:26:06'.'+10 mins')));
    //echo $startTime;/*
    /*
    $request  = '<?xml version="1.0" encoding="utf-8"?>';
    $request .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
    $request .= '<CreateTimeFrom>'.$startTime.'</CreateTimeFrom>';
    $request .= '<CreateTimeTo>'.$endTime.'</CreateTimeTo>';
    $request .= '<OrderRole>Seller</OrderRole>';
    $request .= '<OrderStatus>Completed</OrderStatus>';
    $request .= '<DetailLevel>ReturnAll</DetailLevel>';
    $request .= '<RequesterCredentials>';
    $request .= '<eBayAuthToken>'.$config['userToken_'].'</eBayAuthToken>';
    $request .= '</RequesterCredentials>';
    $request .= '</GetOrdersRequest>';

    $request = trim($request);
    $response = $ebaySession->sendHttpRequest($request);
    $object = simplexml_load_string($response);
    dd($object);
    if(isset($object->OrderArray->Order)) {
        foreach ($object->OrderArray->Order as $key => $value) {
            // dd($value);
            if($value->ExternalTransaction->ExternalTransactionID == '9SV94445S9104073K') {
                dd($value->ExternalTransaction);
            }
        }
    }
    dd($object);


        // $orders = \App\OrderList::where( 'id' , '>' ,83406)->where('id' , '<' ,83424)->get();
        // //dd($orders);
        // $array = [];
        // foreach ($orders as $key => $order) {
        //     $array[$order->id] = false;
        //     if($order->ebaySeller != 'send_money') {
        //         $sellerId = config('ebay.'.$order->ebaySeller);
        //         $ebaySession = new App\Ebay\eBaySession(
        //             $sellerId['userToken_'],
        //             $sellerId['devID_'],
        //             $sellerId['appID_'],
        //             $sellerId['certID_'],
        //             $sellerId['serverUrl_'],
        //             $sellerId['compatabilityLevel'],
        //             0,
        //             'GetOrders' );
        //         $startTime = (gmdate("Y-m-d\TH:i:s.00\Z" , strtotime($order->paymentDate.'-10 mins')));
        //         $endTime = (gmdate("Y-m-d\TH:i:s.00\Z" , strtotime($order->paymentDate.'+10 mins')));
/*
        //         $request  = '<?xml version="1.0" encoding="utf-8"?>';
        //         $request .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        //         $request .= '<CreateTimeFrom>'.$startTime.'</CreateTimeFrom>';
        //         $request .= '<CreateTimeTo>'.$endTime.'</CreateTimeTo>';
        //         $request .= '<OrderRole>Seller</OrderRole>';
        //         $request .= '<OrderStatus>Completed</OrderStatus>';
        //         $request .= '<DetailLevel>ReturnAll</DetailLevel>';
        //         $request .= '<RequesterCredentials>';
        //         $request .= '<eBayAuthToken>'.$sellerId['userToken_'].'</eBayAuthToken>';
        //         $request .= '</RequesterCredentials>';
        //         $request .= '</GetOrdersRequest>';
        //         $request = trim($request);
        //         $response = $ebaySession->sendHttpRequest($request);
        //         $responseObj = simplexml_load_string($response);
        //         if($responseObj->Ack == 'Success') {
        //             if(isset($responseObj->OrderArray->Order)) {
        //                 foreach ($responseObj->OrderArray->Order as $key => $value) {                    
        //                     if($value->ExternalTransaction->ExternalTransactionID == $order->paypalTxnId) {
        //                         $isUpdate = false;
        //                         if(is_null($order->paypalPayerMail)) {
        //                             $order->paypalPayerMail = $value->TransactionArray->Transaction->Buyer->Email;
        //                             $isUpdate = true;
        //                         }
        //                         if(is_null($order->payerPhone)) {
        //                             $order->payerPhone = $value->ShippingAddress->Phone;
        //                             $isUpdate = true;
        //                         }
        //                         if($isUpdate == true) {
        //                             $order->save();
        //                             $array[$order->id] = true;
        //                             \Log::info('更新訂單 '.$order->id. ' 的Email位置跟送貨電話' );
        //                         }                                                 
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
        // dd($array);
        /**/
        $order = \App\OrderList::where( 'id' , '=' ,89988)->first();
        $sellerId = config('ebay.'.$order->ebaySeller);
        $ebaySession = new App\Ebay\eBaySession(
            $sellerId['userToken_'],
            $sellerId['devID_'],
            $sellerId['appID_'],
            $sellerId['certID_'],
            $sellerId['serverUrl_'],
            $sellerId['compatabilityLevel'],
            0,
            'GetOrders' );
        $startTime = (gmdate("Y-m-d\TH:i:s.00\Z" , strtotime($order->paymentDate.'-10 mins')));
        $endTime = (gmdate("Y-m-d\TH:i:s.00\Z" , strtotime($order->paymentDate.'+10 mins')));

        $request  = '<?xml version="1.0" encoding="utf-8"?>';
        $request .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $request .= '<CreateTimeFrom>'.$startTime.'</CreateTimeFrom>';
        $request .= '<CreateTimeTo>'.$endTime.'</CreateTimeTo>';
        $request .= '<OrderRole>Seller</OrderRole>';
        $request .= '<OrderStatus>Completed</OrderStatus>';
        $request .= '<DetailLevel>ReturnAll</DetailLevel>';
        $request .= '<RequesterCredentials>';
        $request .= '<eBayAuthToken>'.$sellerId['userToken_'].'</eBayAuthToken>';
        $request .= '</RequesterCredentials>';
        $request .= '</GetOrdersRequest>';
        $request = trim($request);
        $response = $ebaySession->sendHttpRequest($request);
        $responseObj = simplexml_load_string($response);
        dd($responseObj );
        if($responseObj->Ack == 'Success') {
            if(isset($responseObj->OrderArray->Order)) {
                foreach ($responseObj->OrderArray->Order as $key => $value) {                    
                    if($value->ExternalTransaction->ExternalTransactionID == $order->paypalTxnId) {
                        $order->paypalPayerMail = $value->TransactionArray->Transaction->Buyer->Email;
                        $order->payerPhone = $value->ShippingAddress->Phone;
                        $order->save();
                    }
                }
            }
        }
        
        
});

Route::get('test3' , function(){
    
    $OR = new \App\Repository\OrderListRepos( new App\OrderList );//$items->get('parent_txn_id')
    $findPrevOrder = $OR->orderList->where('paypalTxnId' , '=' , '2YL020902B780864X' )->get();
    dd($findPrevOrder->count());


});