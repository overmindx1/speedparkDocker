<?php

namespace App\Http\Controllers;

use PayPal\PayPalAPI\GetTransactionDetailsReq;
use PayPal\PayPalAPI\GetTransactionDetailsRequestType;
use PayPal\PayPalAPI\TransactionSearchReq;
use PayPal\PayPalAPI\TransactionSearchRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use App\Ebay\eBaySession;
use Illuminate\Http\Request;
use Fahim\PaypalIPN\PaypalIPNListener;
use App\Repository\OrderListRepos;
use App\LineNotifyToken;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private $authEbay;
    private $eBaySession;
    private $orderRepos;


    public function __construct(OrderListRepos $orderRepos)
    {
        $this->middleware('auth')->except(['getIpn' , 'lineNotifyCallBack']);
        $this->orderRepos = $orderRepos;
    }

    /**
     * 取得即時訂單的頁面 (回傳五筆最後訂單)
     */
    public function showRealTimeOrder() {
        return view('realtime');
    }

    /**
     * 檢貨的頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showProcessOrderPage() {
        return view('process');
    }

    /**
     * 列印要送貨的頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPrinterPage() {
        return view('printer');
    }

    /**
     * 列印要送貨的頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPrinterTemplatePage() {
        return view('print_template');
    }

    /**
     * 輸入送貨追蹤碼頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showShippingPage() {
        return view('shipping');
    }

    /**
     * 搜尋定單頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSearchPage() {
        return view('search');
    }

    /**
     * 搜尋定單頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showOrderListPage() {
        return view('orderList');
    }

    /**
     * 搜尋定單頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDutyRecordListPage() {
        return view('duty');
    }

    /**
     * 搜尋定單頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showEbayUpdateTxnId() {
        return view('ebay.txnUpdate');
    }

    /**
     * 統計頁面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showStatisticsPage() {
        return view('statistics');
    }

    /**
     * 處理IPN定單
     */
    public function getIpn() {
        $data = \Request::all();
        try {
            $ipn = new PaypalIPNListener();
            $ipn->use_sandbox = false;
            $ipn->use_ssl = false;
            $verified = $ipn->processIpn();
            $data['verified'] = 'UNVERIFIED';
            if($verified == 1) {
                $data['verified'] = 'VERIFIED';
            }
            $report = $ipn->getTextReport();
            \Log::info("-----new payment-----");
            \Log::info($report);
            $order = $this->orderRepos->insertIpnOrder($data);
            
        } catch ( \Exception  $e){
            \Log::error([$e->getMessage() ]);
            // 送出賴
            $allToken = LineNotifyToken::all();
            $postData = json_encode(\Request::all());
            $allToken->map(function($item ,$key) use ($postData) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$item->token]  ); // Inject the token into the header
                curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'message'    => "\r\n\r\nPaypal IPN Failed , \r\n\r\n".$postData."\r\n\r\n" . date('Y-m-d h:i:s')                   
                ])); 
                curl_setopt($ch , CURLOPT_RETURNTRANSFER ,true);
                $output = curl_exec($ch); 
                $getInfo = curl_getinfo($ch);
                if($getInfo['http_code'] == 401) {
                    LineNotifyToken::destroy($item->id);
                }
                curl_close($ch);
            });
            
            echo "IpnProcessError!";
        }
    }

    /*************************************************************************************************************/
    /**
     *
     */
    public function showPaypal() {
        $config = config('paypal');
        $transactionSearchRequest = new TransactionSearchRequestType();
        $transactionSearchRequest->StartDate = '2017-01-31T00:00:00+0800';
        $transactionSearchRequest->EndDate = '2017-01-31T20:00:00+0800';
        //$transactionSearchRequest->TransactionID = $_REQUEST['transactionID'];
        $tranSearchReq = new TransactionSearchReq();
        $tranSearchReq->TransactionSearchRequest = $transactionSearchRequest;
        $paypalService = new PayPalAPIInterfaceServiceService($config);
        try {
            /* wrap API method calls on the service object with a try catch */
            $transactionSearchResponse = $paypalService->TransactionSearch($tranSearchReq);
        } catch (\Exception $ex) {
            dd($ex);
            exit;
        }
        if(isset($transactionSearchResponse)) {
          
            if($transactionSearchResponse->Ack == 'Success') {
                $paymentList = collect($transactionSearchResponse->PaymentTransactions);
                $groupBy = $paymentList->groupBy('Status');
            }
            dd($groupBy);
        }

    }

    public function showDetail(){
        $config = config('paypal');
        $transactionDetails = new GetTransactionDetailsRequestType();
        $transactionDetails->TransactionID = '4KW02776YA9863030';
        $request = new GetTransactionDetailsReq();
        $request->GetTransactionDetailsRequest = $transactionDetails;

        $paypalService = new PayPalAPIInterfaceServiceService($config);
        try {
            /* wrap API method calls on the service object with a try catch */
            $transDetailsResponse = $paypalService->GetTransactionDetails($request);
        } catch (\Exception $ex) {
            dd($ex);
            exit;
        }
        if(isset($transDetailsResponse)) {
            dd($transDetailsResponse);
        }
    }

        
    /**
     * lineNotifyCallBack 註冊LineNotify
     *
     * @param  Request $request
     * @return void
     */
    public function lineNotifyCallBack(Request $request){
        try {
            $input = $request->all();
            if(isset($input['state'])) {
                if($input['state'] == 'speedpark') {
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_NOBODY , false);
                    curl_setopt($ch, CURLOPT_URL, "https://notify-bot.line.me/oauth/token");
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                        'grant_type'    => 'authorization_code',
                        'code'          => $input['code'],
                        'redirect_uri'  => 'http://ebay.hapopo.com:82/ipnLineNotifyCb',
                        'client_id'     => 'OoDWSHFmhmnJuyTEYvf6PU',
                        'client_secret' => 'aVIr4GIXc0y4fra4zuRBWK6VyJHe0rudrtGqjlxXMdl'
                    ])); 
                    curl_setopt($ch , CURLOPT_RETURNTRANSFER ,true);
                    $output = curl_exec($ch); 
                    if($output !== false) {
                        $getInfo = curl_getinfo($ch);
                        if($getInfo['http_code'] == 200) {
                            $outputObj = json_decode($output ,true);
                            $lineToken = new LineNotifyToken;
                            $lineToken->token = $outputObj['access_token'];
                            $lineToken->save();
                            return 'Line Notify 成功註冊!';
                        }
                        return 'Line Notify 註冊失敗!';
                    }
                    curl_close($ch);
                }
                return 'Line Notify 註冊回傳失敗!';
            }
            return  'Line Notify Callback 失敗';
            
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


}