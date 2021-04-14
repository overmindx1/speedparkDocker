<?php

namespace App\Http\Controllers;

use PayPal\PayPalAPI\GetTransactionDetailsReq;
use PayPal\PayPalAPI\GetTransactionDetailsRequestType;
use PayPal\PayPalAPI\TransactionSearchReq;
use PayPal\PayPalAPI\TransactionSearchRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use App\Ebay\eBaySession;
use Fahim\PaypalIPN\PaypalIPNListener;
use App\Repository\OrderListRepos;

class OrderController extends Controller
{
    private $authEbay;
    private $eBaySession;
    private $orderRepos;


    public function __construct(OrderListRepos $orderRepos)
    {
        $this->middleware('auth')->except('getIpn');
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
            //\Log::info($order);
            //\Log::info($data);
        } catch ( \Illuminate\Database\QueryException  $e){
            $to = ['speedparkpt@gmail.com' , 'overmindx@gmail.com'];
            \Log::error([$e->getMessage() , $e->getSql()]);           
            \Mail::raw($e->__toString() , function($mail) use ( $to ) {
               $mail->to($to)->subject('系統出錯報告,有單無法寫入');
            });
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
        } catch (Exception $ex) {
            dd($ex);
            exit;
        }
        if(isset($transactionSearchResponse)) {
            //echo "<table>";
            //echo "<tr><td>Ack :</td><td><div id='Ack'>$transactionSearchResponse->Ack</div> </td></tr>";
            //echo "</table>";
            //echo "<pre>";
            //print_r($transactionSearchResponse);
            //echo "</pre>";
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
        } catch (Exception $ex) {
            dd($ex);
            exit;
        }
        if(isset($transDetailsResponse)) {
            dd($transDetailsResponse);
            //echo "<table>";
            //echo "<tr><td>Ack :</td><td><div id='Ack'>$transDetailsResponse->Ack</div> </td></tr>";
            //echo "</table>";
            //echo "<pre>";
            //print_r($transDetailsResponse);
            //echo "</pre>";

        }
    }

    /*public function showIndex()
    {

        $cc = '<?xml version="1.0" encoding="utf-8"?>
                <GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <RequesterCredentials>
                    <eBayAuthToken>'.$this->authEbay['userToken_'].'</eBayAuthToken>
                  </RequesterCredentials>
                  <CreateTimeFrom>'.date('c' , strtotime("-1 day")).'</CreateTimeFrom>
                  <CreateTimeTo>'.date('c').'</CreateTimeTo>
                  <OrderRole>Seller</OrderRole>
                  <OrderStatus>All</OrderStatus>
                </GetOrdersRequest>';

        $response = $this->eBaySession->sendHttpRequest($cc);
        $xml = simplexml_load_string($response);
        dd($xml->OrderArray->Order[0]);
        return view( 'seller.index', ['xml' => $xml]);
    }

    public function showIndex2()
    {

        $this->authEbay = config('ebay.joeyangair2010');
        $this->eBaySession = new eBaySession(   $this->authEbay['userToken_'],
            $this->authEbay['devID_'],
            $this->authEbay['appID_'],
            $this->authEbay['certID_'],
            $this->authEbay['serverUrl_'],
            $this->authEbay['compatabilityLevel'],
            0,
            'GetOrderTransactions');

        $cc = '<?xml version="1.0" encoding="utf-8"?>
<GetOrderTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>'.$this->authEbay['userToken_'].'</eBayAuthToken>
  </RequesterCredentials>
  <OrderIDArray>
    <OrderID>151010-070124</OrderID>
  </OrderIDArray>
</GetOrderTransactionsRequest>';

        $response = $this->eBaySession->sendHttpRequest($cc);
        $xml = simplexml_load_string($response);
        dd($xml);
    }*/



}