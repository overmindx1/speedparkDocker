<?php
/**
 * Created by PhpStorm.
 * User: Overmind
 * Date: 2016/11/2
 * Time: 下午 09:04
 */

namespace App\Repository;

use App\OrderList;
use App\Repository\ItemsImageRepos;
use App\OrderShipList;
use App\Repository\OrderShipListRepos;
use App\ItemsImage;
use App\Ebay\Query;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OrderListRepos {

    /**
     * @const   搜尋的選項
     */
    const SEARCH_LUT = [
        1 => 'id',
        2 => 'ebayItemsList',
        3 => 'ebayItemsList',
        4 => 'paypalTxnId',
        5 => 'paypalPaymentStatus',
        6 => 'ebayBuyerId',
        7 => 'paypalPayerMail',
        8 => 'shippingRecipientName',
        10 => 'shippingCountry',
        11 => 'ebaySeller'
    ];


    /**
     * @var OrderList $orderList
     */
    private $orderList;

    public function __construct(OrderList $orderList)
    {
        $this->orderList = $orderList;
    }

    /**
     * 依頁數取得訂單的資料
     * @param integer $page 頁數
     * @return array  資料
     */
    public function getOrderListByPage($page) {
        $total = $this->orderList->select('id')->count();
        $offset = ($page - 1) * 30;
        $orderList = $this->orderList->with('hasShip')
                                     ->orderBy('created_at' , 'desc')
                                     ->offset($offset)
                                     ->limit(30)
                                     ->get();

        $orderList = $this->getOrdersImageList($orderList);

        $object = [
            'totalPage'     => ceil($total/30),
            'total'         => $total ,
            'page'          => $page ,
            'orderList'     => $orderList
        ];

        return $object;
    }

    /**
     * 即時定單頁面用 -> 取得今天所有訂單
     * @return  \Illuminate\Database\Eloquent\Collection
     */
    public function getTodayOrderList() {
        $date = date('Y-m-d');
        $todayOrders = $this->orderList->where('paymentDate' , '>=', $date)
                                       ->orderBy('created_at' , 'desc')
                                       ->get();
        return $this->getOrdersImageList($todayOrders);
    }

    /**
     * 找出5個未完成訂單
     * @return Collection|OrderList[]
     */
    //public function getFirst5UncompletedOrder() {
    //    $uncompletedList = $this->orderList ->where('ProcessStatus' , 0)
    //                                        ->orderBy('created_at' , 'desc')
    //                                        ->take(5)->get();
    //    $itemsImageRepos = new ItemsImageRepos(new ItemsImage());
    //    foreach( $uncompletedList as $key => $order) {
    //        /**
    //         * @var OrderList $order
    //         */
    //        $items = json_decode($order->ebayItemsList);
    //        $searchImg = [];
    //        foreach($items->itemsList as $ky => $item) {
    //            $searchImg[] = $item->itemId;
    //        }
    //        $imagesData = $itemsImageRepos->getImagesByItemsId($searchImg); //搜尋圖片
    //        $imagePathTmp = [];
    //        foreach($imagesData as $image) {  //將圖片位置存進陣列
    //            $imagePathTmp[$image->itemId] = json_decode($image->path);
    //        }
    //        $order->imagePath = $imagePathTmp;
    //    }
    //    return $uncompletedList;
    //}

    /**
     * 依單號取得訂單詳細資料
     * @param $startId  int 起始單號
     * @param $endId    int 結束單號
     * @param $sellers  array 選定銷售帳號
     * @param $showImage bool 是否顯示圖檔
     * @return \Illuminate\Database\Eloquent\Collection|Collection|static[]
     */
    public function getOrderForProcess($startId , $endId , $sellers , $showImage) {

        $orderList = $this->getOrderBySetId($startId , $endId ,$sellers);

        if($showImage) {
            $orderList = $this->getOrdersImageList($orderList);
        }

        return $orderList;
    }

    /**
     * 依id範圍找出相關的地址資訊
     * @param $startId  int 訂單起始位置
     * @param $endId    int 訂單結束位置
     * @param $sellers  array 選擇的銷售帳號
     * @return \stdClass object 回傳的資料
     */
    public function getAddressInfoByIds($startId , $endId , $sellers) {
        $object = new \stdClass();
        $storeInfo = config('store');
        $col = [
            'id',
            'ebayBuyerId',
            'paypalPayerMail',
            'shippingCountry',
            'shippingAddressState',
            'shippingAddressCity',
            'shippingAddressStreet',
            'shippingAddressZip',
            'shippingRecipientName',
            'payerPhone',
            'ebaySeller'
        ];
        $orderList = $this->orderList->select($col)
                                     ->where('id' , '>=' , $startId)
                                     ->where('id' , '<=' , $endId)
                                     ->where('paypalPaymentStatus' , '=' , 'Completed')
                                     ->whereIn('ebaySeller' , $sellers)
                                     ->get();
        $object->storeInfo = $storeInfo;
        $object->orderList = $orderList;
        return $object;
    }

    /**
     * 依搜尋模式找出要的定單資料
     * @param int       $type       搜尋的模式
     * @param string    $keyword    KeyWord
     * @param array     $sellers    選定銷售帳號
     * @param string    $startDate  起始時間
     * @param string    $endDate    結束時間
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getOrderBySearchType($type , $keyword , $sellers , $startDate , $endDate) {

        $startDate  = $startDate. ' 00:00:00';
        $endDate    = $endDate  . ' 23:59:59';
        $column = collect(self::SEARCH_LUT)->get($type);
        if($type == 2 || $type == 3 || $type == 8) {
            $arrayKeyword = explode(' ' , $keyword);
            if(count($arrayKeyword) <= 1) {
                $orderList = $this->orderList->where($column , 'LIKE' , '%'.$keyword.'%')
                                    ->where('paymentDate' , '>=' , $startDate)
                                    ->where('paymentDate' , '<=' , $endDate)
                                    ->whereIn('ebaySeller' , $sellers)
                                    ->orderBy('created_at' , 'DESC')->with('hasShip')->get();
            } else { //多重關鍵字的話
                $whereArr = [];
                foreach ($arrayKeyword as $key => $keyWord) {
                    if($keyWord != '') {
                        $whereArr[] = [$column , 'LIKE' , '%'.$keyWord.'%'];
                    }
                }
                $orderList = $this->orderList->where($whereArr)
                                            ->where('paymentDate' , '>=' , $startDate)
                                            ->where('paymentDate' , '<=' , $endDate)
                                            ->whereIn('ebaySeller' , $sellers)
                                            ->orderBy('created_at' , 'DESC')->with('hasShip')->get();
            }


        } elseif($type == 9) {
            $shipping = new OrderShipListRepos(new OrderShipList());
            $data = $shipping->findShippingRecordByTrackingNumber($keyword);
            $order = $data->belongOrder;
            $order->has_ship = $data->belongOrder->hasShip;
            $orderList = collect([$order]);

        } elseif($type == 5) {
            if ($keyword != 'completed' && $keyword != 'pending') {
                $orderList = $this->orderList->where('paymentUpdate', 'LIKE', '%' . $keyword . '%')
                    ->where('paymentDate', '>=', $startDate)
                    ->where('paymentDate', '<=', $endDate)
                    ->whereIn('ebaySeller' , $sellers)
                    ->orderBy('created_at', 'DESC')->with('hasShip')->get();
            } else {
                $orderList = $this->orderList->where($column, $keyword)
                    ->where('paymentDate', '>=', $startDate)
                    ->where('paymentDate', '<=', $endDate)
                    ->whereIn('ebaySeller' , $sellers)
                    ->orderBy('created_at', 'DESC')->with('hasShip')->get();
            }
        } elseif($type == 1) {
            $orderList = $this->orderList->where($column , $keyword)
                        ->orderBy('created_at' , 'DESC')->with('hasShip')->get();

        } elseif($type == 11) {
            $orderList = $this->orderList->whereIn($column , $sellers)
                                         ->where('paymentDate', '>=', $startDate)
                                         ->where('paymentDate', '<=', $endDate)
                                         ->orderBy('created_at' , 'DESC')->with('hasShip')->get();
        } else {
            $orderListRepo = $this->orderList->where($column , $keyword)
                        ->where('paymentDate' , '>=' , $startDate)
                        ->where('paymentDate' , '<=' , $endDate);
            if($type != 7 && $type != 4) { //Buyer Mail , Txd 不受 Sellers 影響
                $orderListRepo->whereIn('ebaySeller' , $sellers);
            }
            $orderList = $orderListRepo->orderBy('created_at' , 'DESC')->with('hasShip')->get();

        }
        return $this->getOrdersImageList($orderList);
    }

    /**
     * 更新所選擇的order 定單處理狀態
     * @param $orderId      int 定單編號
     * @param $statusType   int 處理狀態
     * @return bool|int     回傳是否更新
     */
    public function updateOrderStatusById($orderId , $statusType) {
        return $this->orderList->whereId($orderId)->update(['ProcessStatus' => $statusType]);
    }


    /**
     * 找出未完成的訂單
     * @return Collection|OrderList[]
     */
    //public function getUncompletedOrder() {
    //    $uncompletedList = $this->orderList ->where('ProcessStatus' , 0)
    //                                        ->orderBy('created_at' , 'desc')
    //                                        ->paginate(15);
    //    return $uncompletedList;
    //
    //}

    /**
     * 找出某個id之後的未完成訂單
     * @param $id
     * @return  \Illuminate\Database\Eloquent\Collection|OrderList[]
     */
    public function getUncompletedOrderByIdAfter($id) {
        $orderList = $this->orderList->where('id' , '>' , $id)->with('hasShip')->get();
        return $this->getOrdersImageList($orderList);
    }

    /**
     * 依id範圍找出相關的訂單
     * @param $startId  int 訂單起始範圍
     * @param $endId    int 訂單結束範圍
     * @param $sellers  array 搜尋使用的賣家帳號
     * @return \Illuminate\Database\Eloquent\Collection|OrderList[]
     */
    public function getOrderBySetId($startId , $endId , $sellers) {
        $orderList = $this->orderList->with('hasShip')
                                     ->where('id' , '>=' , $startId)
                                     ->where('id' , '<=' , $endId)
                                     ->whereIn('ebaySeller' , $sellers)
                                     //->where('paypalPaymentStatus' , '=' , 'Completed')
                                     ->get();
        return $orderList;
    }

    /**
     * 找出要更新送貨資料的資料
     * @param array $orderKeys
     * @return \Illuminate\Database\Eloquent\Collection|OrderList[]
     */
    public function getOrderToUpdateShippingData(array $orderKeys) {
        $cols = ['id' ,'ebaySeller' , 'ebayItemsList' , 'ProcessStatus'];
        $orders = $this->orderList->select($cols)
                                  ->whereIn('id' ,$orderKeys )
                                  ->where('paypalPaymentStatus' , '=' , 'Completed')
                                  ->get();
        return $orders;

    }


    //public function getOrderList() {
    //    $orderList = $this->orderList->orderBy('created_at' , 'desc')->paginate(30);
    //    return $orderList;
    //}

    /**
     * @param $orderId
     * @param $memo
     * @return bool|OrderList
     */
    public function updateOrderSelfMemo($orderId , $memo) {
        $order = $this->orderList->find($orderId);
        $order->selfMemo = $memo;
        if($order->save()) {
            return $order;
        } else {
            return false;
        }
    }

    /**
     * 將IPN資料寫入資料庫 或是更新訂單
     * @param array $ipnOrder   IPN送來的資料
     * @return OrderList|array
     */
    //public function insertNewOrder(array $ipnOrder ) {
    //    $order = $this->processOrder($ipnOrder);
    //    if($order != false) { //看有沒有新單
    //        $orderResult = $this->orderList->create($order);
    //        return $orderResult;
    //    } else {
    //        return [];
    //    }
    //}

    /**
     * 將IPN資料寫入資料庫 或是更新訂單
     * @param array $ipnOrder   IPN送來的資料
     * @return OrderList|array
     */
    public function insertIpnOrder(array $ipnOrder ) {
        $orderStatus = $this->processIpnOrder($ipnOrder);
        if($orderStatus['action'] == 'Create') { //看有沒有新單
            $orderResult = $this->orderList->create($orderStatus['ipnOrder']);
            \Log::info($orderStatus);
            return $orderResult;
        } else {
            return $orderStatus;
        }
    }

    /**
    * 處理一般訂單
    * @param array $ipnOrder
    * @return array|bool
    */
    //private function processOrder(array $ipnOrder) {
//
    //    $items = collect($ipnOrder);
    //    $getOrder = $this->orderList->where('paypalTxnId' , '=' , $items->get('txn_id'))->get();
//
    //    if($getOrder->count() == 0) { // 如果沒有重複的 paypalTxnId 才新增
//
    //        if($items->get('txn_type') != 'send_money') {
    //            $ebayQuery = new Query('joeyangair2010' , 'GetItem');
    //            $sellerId = $ebayQuery->getSellerIdByItemID($items->get('item_number1'));
    //        } else {
    //            $sellerId = 'send_money';
    //        }
    //        $order = [];
    //        $order['ebaySeller']            = $sellerId;
    //        $order['paypalSellerMail']      = $items->get('business');
    //        $order['verifyStatus']          = $items->get('verified');
    //        $order['paymentDate']           = date('Y-m-d H:i:s', strtotime( $items->get('payment_date') . " GMT+8"));
    //        $order['paymentDatePDT']        = $items->get('payment_date');
    //        $order['paypalTxnId']           = $items->get('txn_id');
//
    //        if($items->has('txn_type')) {
    //            $order['paypalTxnType']         = $items->get('txn_type');
    //        } else {
    //            switch ($items->get('payment_status')) {
    //                case 'Refunded':
    //                    $order['paypalTxnType'] = '退款';
    //                    break;
    //                case 'Canceled_Reversal':
    //                    $order['paypalTxnType'] = '爭議解決';
    //                    break;
    //                case 'Reversed':
    //                    $order['paypalTxnType'] = '款項扣除';
    //                    break;
    //            }
    //        }
    //        $order['paypalPayerId']         = $items->get('payer_id');
    //        $order['paypalPayerMail']       = $items->get('payer_email');
    //        $order['paypalPayerFirstName']  = $items->get('first_name');
    //        $order['paypalPayerLastName']   = $items->get('last_name');
    //        $order['paypalProtectionEligibility']  = $items->get('protection_eligibility');
    //        $order['paypalPaymentStatus']   = $items->get('payment_status');
    //        if($items->get('payment_status') != 'Completed') { //如果交易未定
    //            $order['paypalStatusReason'] = ( $items->has('pending_reason') ? $items->get('pending_reason') : $items->get('reason_code') );
    //        }
//
    //        $order['paypalPayerStatus']  = ($items->has('payer_status') ? $items->get('payer_status') : '');
//
    //        $order['paypalPaymentType']  = $items->get('payment_type');
    //        $order['paypalVerifySign']   = $items->get('verify_sign');
    //        $order['paypalIPNTrackId']   = $items->get('ipn_track_id');
//
    //        $order['paypalPayerAddressConfirmed'] = ($items->has('address_status') ? $items->get('address_status') : '');
//
    //        if($items->has('memo')) {
    //            $order['paypalPayerMemo']     = $items->get('memo');
    //        }
//
    //        $order['custom']     = $items->get('custom');
    //        //Items
    //        $obj = ['itemsList'=>[]];
    //        if($items->get('txn_type') != 'send_money') {
    //            $itemsImageRepos = new ItemsImageRepos(new ItemsImage());
    //            for($i =1 ; $i<10; $i++) {
    //                if($items->has('item_name'.$i)) {
    //                    if(!$itemsImageRepos->findHasItem($items->get('item_number'.$i))) { //找看看這個物品的圖片有沒有記錄下來
    //                        $imagesPath = json_encode( (array) $ebayQuery->getPicUrlByItemID($items->get('item_number'.$i))); //將物品圖片資料轉成JSoN存到資料庫
    //                        $itemInsert = $itemsImageRepos->insertNewItemImage(['itemId'=>$items->get('item_number'.$i) , 'path'=>$imagesPath]); //存到資料庫
    //                        \Log::debug('$itemInsert' , [$itemInsert]);
    //                        \Log::debug('$imagesPath' , [$imagesPath]);
    //                        \Log::debug('tiems' , [$items->get('item_number'.$i)]);
    //                    }
    //                    $obj['itemsList'][] = [
    //                        'itemId'    => $items->get('item_number'.$i),
    //                        'itemName'  => $items->get('item_name'.$i),
    //                        'itemPrice' => $items->get('mc_gross_'.$i) .'|'. $items->get('mc_currency'),
    //                        'quantity'  => $items->get('quantity'.$i),
    //                        'txnId'     => $items->get('ebay_txn_id'.$i)
    //                    ];
    //                }
    //            }
    //            $order['ebayBuyerId']     = $items->get('auction_buyer_id');
    //        } else {
    //            $order['ebayBuyerId']     = '';
    //        }
//
    //        $order['ebayItemsList']   = json_encode($obj);
    //        //Address for Shipping
    //        $order['shippingCountryCode']       = $items->get('residence_country');
    //        $order['shippingCountry']           = $items->get('address_country');
    //        $order['shippingAddressState']      = $items->get('address_state');
    //        $order['shippingAddressCity']       = $items->get('address_city');
    //        $order['shippingAddressStreet']     = $items->get('address_street');
    //        $order['shippingAddressZip']        = $items->get('address_zip');
    //        $order['shippingRecipientName']     = $items->get('address_name');
    //        if($items->has('contact_phone')) {
    //            $order['payerPhone']     = $items->get('contact_phone');
    //        }
    //        //Money
    //        $order['totalPayment']      = $items->get('mc_gross');
    //        $order['paypalFee']         = $items->get('mc_fee');
    //        if($items->get('txn_type') != 'send_money') {
    //            $order['shippingFee']   = $items->get('mc_shipping');
    //            $order['shippingMethod']= $items->get('shipping_method');
    //        } else {
    //            $order['shippingFee']    = 0;
    //            $order['shippingMethod'] = 0;
    //        }
//
    //        if($items->has('tax')) {
    //            $order['Tax'] = $items->get('tax');
    //        }
//
    //        $order['CurrencyCode']          = $items->get('mc_currency');
    //        $order['ProcessStatus']        = 0;
//
    //        return $order; //回傳陣列
    //    } else {
//
//
    //        return FALSE;
    //    }
//
    //}

    /**
     * 處理訂單 => 有重複的 paypalTxnId 更新 或是不新增  , 沒有更新
     * @param array $ipnOrder
     * @return array|bool
     */
    private function processIpnOrder(array $ipnOrder) {

        $items = collect($ipnOrder);
        if($items->has('parent_txn_id')) {
            $findPrevOrder = $this->orderList->where('paypalTxnId' , '=' , $items->get('parent_txn_id'))->first();
            if($findPrevOrder != null) {
                $hasUpdate = json_decode($findPrevOrder->paymentUpdate, true);
                if ($hasUpdate == null) { //如果payment update 沒有記錄
                    $updateArray = [
                        [
                            'paypalTxnId'   => $items->get('txn_id'),
                            'paymentStatus' => $items->get('payment_status'),
                            'money'         => $items->get('mc_gross'),
                            'fee'           => $items->get('mc_fee'),
                            'update'        => date('Y-m-d h:i:s')
                        ]
                    ];
                    $updateJson = json_encode($updateArray);
                    $findPrevOrder->paypalPaymentStatus = $items->get('payment_status');
                    $findPrevOrder->paymentUpdate = $updateJson;
                    $findPrevOrder->save();
                } else { //更新payment Update
                    $updateArray = $hasUpdate;
                    $updateArray[] = [
                        'paypalTxnId'   => $items->get('txn_id'),
                        'paymentStatus' => $items->get('payment_status'),
                        'money'         => $items->get('mc_gross'),
                        'fee'           => $items->get('mc_fee'),
                        'update'        => date('Y-m-d h:i:s')
                    ];
                    $updateJson = json_encode($updateArray);
                    $findPrevOrder->paypalPaymentStatus = $items->get('payment_status');
                    $findPrevOrder->paymentUpdate = $updateJson;
                    $findPrevOrder->save();
                }
                return ['action' => 'Update', 'orderId' => $findPrevOrder->id, 'paymentStatus' => $items->get('payment_status')];
            }

        } else { //新增

            $getOrder = $this->orderList->where('paypalTxnId', '=', $items->get('txn_id'))->get();

            if ($getOrder->count() == 0 ) { // 如果沒有重複的 paypalTxnId 才新增

                if ($items->get('txn_type') != 'send_money') {
                    $ebayQuery = new Query('joeyangair2010', 'GetItem');
                    $sellerId = $ebayQuery->getSellerIdByItemID($items->get('item_number1'));
                } else {
                    $sellerId = 'send_money';
                }
                $order = [];
                $order['ebaySeller'] = $sellerId;
                $order['paypalSellerMail'] = $items->get('business');
                $order['verifyStatus'] = $items->get('verified');
                $order['paymentDate'] = date('Y-m-d H:i:s', strtotime($items->get('payment_date') . " GMT+8"));
                $order['paymentDatePDT'] = $items->get('payment_date');
                $order['paypalTxnId'] = $items->get('txn_id');

                if ($items->has('txn_type')) {
                    $order['paypalTxnType'] = $items->get('txn_type');
                } else {
                    switch ($items->get('payment_status')) {
                        case 'Refunded':
                            $order['paypalTxnType'] = '退款';
                            break;
                        case 'Canceled_Reversal':
                            $order['paypalTxnType'] = '爭議解決';
                            break;
                        case 'Reversed':
                            $order['paypalTxnType'] = '款項扣除';
                            break;
                    }
                }
                $order['paypalPayerId'] = $items->get('payer_id');
                $order['paypalPayerMail'] = $items->get('payer_email');
                $order['paypalPayerFirstName'] = $items->get('first_name');
                $order['paypalPayerLastName'] = $items->get('last_name');
                $order['paypalProtectionEligibility'] = $items->get('protection_eligibility');
                $order['paypalPaymentStatus'] = $items->get('payment_status');
                if ($items->get('payment_status') != 'Completed') { //如果交易未定
                    $order['paypalStatusReason'] = ($items->has('pending_reason') ? $items->get('pending_reason') : $items->get('reason_code'));
                }

                $order['paypalPayerStatus'] = ($items->has('payer_status') ? $items->get('payer_status') : '');

                $order['paypalPaymentType'] = $items->get('payment_type');
                $order['paypalVerifySign'] = $items->get('verify_sign');
                $order['paypalIPNTrackId'] = $items->get('ipn_track_id');

                $order['paypalPayerAddressConfirmed'] = ($items->has('address_status') ? $items->get('address_status') : '');

                if ($items->has('memo')) {
                    $order['paypalPayerMemo'] = $items->get('memo');
                }

                $order['custom'] = $items->get('custom');
                //Items
                $obj = ['itemsList' => []];
                if ($items->get('txn_type') != 'send_money') {
                    $itemsImageRepos = new ItemsImageRepos(new ItemsImage());
                    for ($i = 1; $i < 10; $i++) {
                        if ($items->has('item_name' . $i)) {
                            $itemImage = $itemsImageRepos->findHasItem($items->get('item_number' . $i));
                            if ($itemImage != false) { //找看看這個物品的圖片有沒有記錄下來 資料庫有的話
                                //$today = date('Y-m-d');
                                //$deadline = $itemImage->updated_at->addDays(7)->toDateString(); //最後更新日加七天
                                //if($today > $deadline) { //超過七天就更新
                                    $imagesPath = json_encode((array)$ebayQuery->getPicUrlByItemID($items->get('item_number' . $i))); //將物品圖片資料轉成JSoN存到資料庫
                                    $itemImage->path = $imagesPath;
                                    $itemImage->updated_at = Carbon::now();
                                    $itemImage->save();
                                    \Log::debug('Update Image Path', [$items->get('item_number' . $i)]);
                               // }
                            } else { //資料庫沒有的話
                                $imagesPath = json_encode((array)$ebayQuery->getPicUrlByItemID($items->get('item_number' . $i))); //將物品圖片資料轉成JSoN存到資料庫
                                $itemInsert = $itemsImageRepos->insertNewItemImage(['itemId' => $items->get('item_number' . $i), 'path' => $imagesPath]); //存到資料庫
                                \Log::debug('itemInsert', [$itemInsert]);
                                \Log::debug('new imagesPath', [$imagesPath]);
                                \Log::debug('items', [$items->get('item_number' . $i)]);
                            }
                            $obj['itemsList'][] = [
                                'itemId' => $items->get('item_number' . $i),
                                'itemName' => $items->get('item_name' . $i),
                                'itemPrice' => $items->get('mc_gross_' . $i) . '|' . $items->get('mc_currency'),
                                'quantity' => $items->get('quantity' . $i),
                                'txnId' => $items->get('ebay_txn_id' . $i)
                            ];
                        }
                    }
                    $order['ebayBuyerId'] = $items->get('auction_buyer_id');
                } else {
                    $order['ebayBuyerId'] = '';
                }

                $order['ebayItemsList'] = json_encode($obj);
                //Address for Shipping
                if($sellerId != 'send_money') {
                    $order['shippingCountryCode'] = $items->get('residence_country');
                    $order['shippingCountry'] = $items->get('address_country');
                    $order['shippingAddressState'] = $items->get('address_state');
                    $order['shippingAddressCity'] = $items->get('address_city');
                    $order['shippingAddressStreet'] = $items->get('address_street');
                    $order['shippingAddressZip'] = $items->get('address_zip');
                    $order['shippingRecipientName'] = $items->get('address_name');
                } else { //如果是 sendMoney 則另外給值
                    $order['shippingCountryCode'] = $items->get('residence_country');
                    $order['shippingCountry'] = '';
                    $order['shippingAddressState'] = '';
                    $order['shippingAddressCity'] = '';
                    $order['shippingAddressStreet'] = '';
                    $order['shippingAddressZip'] = '';
                    $order['shippingRecipientName'] = '';
                }
                if ($items->has('contact_phone')) {
                    $order['payerPhone'] = $items->get('contact_phone');
                }
                //Money
                $order['totalPayment'] = $items->get('mc_gross');
                $order['paypalFee'] = $items->get('mc_fee');
                if($order['paypalFee'] == null) { // 預防她出現null
                    $order['paypalFee'] = 0;
                }
                if ($items->get('txn_type') != 'send_money') {
                    $order['shippingFee'] = $items->get('mc_shipping');
                    $order['shippingMethod'] = $items->get('shipping_method');
                } else {
                    $order['shippingFee'] = 0;
                    $order['shippingMethod'] = 0;
                }

                if ($items->has('tax')) {
                    $order['Tax'] = $items->get('tax');
                }

                $order['CurrencyCode'] = $items->get('mc_currency');
                $order['ProcessStatus'] = 0;

                return ['action' => 'Create', 'ipnOrder' => $order, 'paymentStatus' => $items->get('payment_status')];
                //return $order; //回傳陣列
            } else {
                $dbOrder = $getOrder->first(); //看是不是這個單號是不是pending

                if( ($dbOrder->paypalPaymentStatus == 'Pending')  && ( $items->get('payment_status') == 'Completed') ) {
                    $dbOrder->paypalPaymentStatus = $items->get('payment_status');
                    $dbOrder->save();
                    return ['action' => 'Pending To Completed Updated' , 'orderId' => $dbOrder->id];
                }

                return ['action' => 'Reduplicate' , 'order' => []];
            }
        }
    }




    /**
     * 找尋出訂單的圖片位置 , 並回寫入collection
     * @param  \Illuminate\Database\Eloquent\Collection  $orderCollection
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getOrdersImageList($orderCollection) {
        $itemsImageRepos = new ItemsImageRepos(new ItemsImage());
        $searchImg = [];
        foreach( $orderCollection as $key => $order) {
            /**
             * @var OrderList $order
             */

            $items = json_decode($order->ebayItemsList);
            foreach($items->itemsList as $ky => $item) {
                $searchImg[] = $item->itemId;
            }
        }
        $imagesData = $itemsImageRepos->getImagesByItemsId($searchImg);
        $imagePath = [];
        foreach($imagesData as $image) {  //將圖片位置存進陣列
            $imagePath[$image->itemId] = json_decode($image->path);
        }
        $imageCollection =  collect($imagePath);

        foreach ($orderCollection as $key => $order) {
            $items = json_decode($order->ebayItemsList);
            $imageTmpPath = [];
            foreach($items->itemsList as $itemKey => $item) {

                if($imageCollection->has($item->itemId)) {
                    $imageTmpPath[$item->itemId] = $imageCollection->get($item->itemId);
                }
            }
            $order->imagePath = $imageTmpPath;
        }

        return $orderCollection;

    }

    /**
     * todo 完善她
     * @param $ipnOrder
     * @return array
     */
    private function processIpn($ipnOrder) {
        $items = collect($ipnOrder);
        $getOrder = $this->orderList->where('paypalTxnId', '=', $items->get('txn_id'))->get();
        if ($getOrder->count() == 0) { // 如果沒有重複的 paypalTxnId 才新增

            if ($items->get('txn_type') != 'send_money') {
                $ebayQuery = new Query('joeyangair2010', 'GetItem');
                $sellerId = $ebayQuery->getSellerIdByItemID($items->get('item_number1'));
            } else {
                $sellerId = 'send_money';
            }
            $order = [];
            $order['ebaySeller'] = $sellerId;
            $order['paypalSellerMail'] = $items->get('business');
            $order['verifyStatus'] = $items->get('verified');
            $order['paymentDate'] = date('Y-m-d H:i:s', strtotime($items->get('payment_date') . " GMT+8"));
            $order['paymentDatePDT'] = $items->get('payment_date');
            $order['paypalTxnId'] = $items->get('txn_id');

            if ($items->has('txn_type')) {
                $order['paypalTxnType'] = $items->get('txn_type');
            } else {
                $findPrevOrder = $this->orderList->where('paypalTxnId' , '=' , $items->get('parent_txn_id'))->first();
                switch ($items->get('payment_status')) {
                    case 'Refunded':
                        $order['paypalTxnType'] = '退款';
                        break;
                    case 'Canceled_Reversal':
                        $order['paypalTxnType'] = '爭議解決';
                        break;
                    case 'Reversed':
                        $order['paypalTxnType'] = '款項扣除';
                        break;
                }
            }
            $order['paypalPayerId'] = $items->get('payer_id');
            $order['paypalPayerMail'] = $items->get('payer_email');
            $order['paypalPayerFirstName'] = $items->get('first_name');
            $order['paypalPayerLastName'] = $items->get('last_name');
            $order['paypalProtectionEligibility'] = $items->get('protection_eligibility');
            $order['paypalPaymentStatus'] = $items->get('payment_status');
            if ($items->get('payment_status') != 'Completed') { //如果交易未定
                $order['paypalStatusReason'] = ($items->has('pending_reason') ? $items->get('pending_reason') : $items->get('reason_code'));
            }

            $order['paypalPayerStatus'] = ($items->has('payer_status') ? $items->get('payer_status') : '');

            $order['paypalPaymentType'] = $items->get('payment_type');
            $order['paypalVerifySign'] = $items->get('verify_sign');
            $order['paypalIPNTrackId'] = $items->get('ipn_track_id');

            $order['paypalPayerAddressConfirmed'] = ($items->has('address_status') ? $items->get('address_status') : '');

            if ($items->has('memo')) {
                $order['paypalPayerMemo'] = $items->get('memo');
            }

            $order['custom'] = $items->get('custom');
            //Items
            $obj = ['itemsList' => []];
            if ($items->get('txn_type') != 'send_money') {
                $itemsImageRepos = new ItemsImageRepos(new ItemsImage());
                for ($i = 1; $i < 10; $i++) {
                    if ($items->has('item_name' . $i)) {
                        if (!$itemsImageRepos->findHasItem($items->get('item_number' . $i))) { //找看看這個物品的圖片有沒有記錄下來
                            $imagesPath = json_encode((array)$ebayQuery->getPicUrlByItemID($items->get('item_number' . $i))); //將物品圖片資料轉成JSoN存到資料庫
                            $itemInsert = $itemsImageRepos->insertNewItemImage(['itemId' => $items->get('item_number' . $i), 'path' => $imagesPath]); //存到資料庫
                            \Log::debug('$itemInsert', [$itemInsert]);
                            \Log::debug('$imagesPath', [$imagesPath]);
                            \Log::debug('tiems', [$items->get('item_number' . $i)]);
                        }
                        $obj['itemsList'][] = [
                            'itemId' => $items->get('item_number' . $i),
                            'itemName' => $items->get('item_name' . $i),
                            'itemPrice' => $items->get('mc_gross_' . $i) . '|' . $items->get('mc_currency'),
                            'quantity' => $items->get('quantity' . $i),
                            'txnId' => $items->get('ebay_txn_id' . $i)
                        ];
                    }
                }
                $order['ebayBuyerId'] = $items->get('auction_buyer_id');
            } else {
                $order['ebayBuyerId'] = '';
            }

            $order['ebayItemsList'] = json_encode($obj);
            //Address for Shipping
            if(!$sellerId == 'send_money') {
                $order['shippingCountryCode'] = $items->get('residence_country');
                $order['shippingCountry'] = $items->get('address_country');
                $order['shippingAddressState'] = $items->get('address_state');
                $order['shippingAddressCity'] = $items->get('address_city');
                $order['shippingAddressStreet'] = $items->get('address_street');
                $order['shippingAddressZip'] = $items->get('address_zip');
                $order['shippingRecipientName'] = $items->get('address_name');
            } else { //如果是 sendMoney 則另外給值
                $order['shippingCountryCode'] = $items->get('residence_country');
                $order['shippingCountry'] = '';
                $order['shippingAddressState'] = '';
                $order['shippingAddressCity'] = '';
                $order['shippingAddressStreet'] = '';
                $order['shippingAddressZip'] = '';
                $order['shippingRecipientName'] = '';
            }

            if ($items->has('contact_phone')) {
                $order['payerPhone'] = $items->get('contact_phone');
            }
            //Money
            $order['totalPayment'] = $items->get('mc_gross');
            $order['paypalFee'] = $items->get('mc_fee');
            if ($items->get('txn_type') != 'send_money') {
                $order['shippingFee'] = $items->get('mc_shipping');
                $order['shippingMethod'] = $items->get('shipping_method');
            } else {
                $order['shippingFee'] = 0;
                $order['shippingMethod'] = 0;
            }

            if ($items->has('tax')) {
                $order['Tax'] = $items->get('tax');
            }

            $order['CurrencyCode'] = $items->get('mc_currency');
            $order['ProcessStatus'] = 0;

            return ['action' => 'Create', 'ipnOrder' => $order, 'paymentStatus' => $items->get('payment_status')];
            //return $order; //回傳陣列
        } else {
            return ['action' => 'Reduplicate' , 'order' => []];
        }

    }

    /**
     * 依日期找出當日有多少訂單 及啟始跟結束定單號碼
     * @param $date     String  搜尋日期
     * @return \stdClass
     */
    public function getOrdersRangeByDate($date){
        $dateEnd = date('Y-m-d' , strtotime($date.'+1 day'));
        $result = $this->orderList->select('id')
                       ->where('paymentDate' , '>='  , $date)
                       ->where('paymentDate' , '<'   , $dateEnd)
                       ->get();

        $object = new \stdClass();
        $object->firstId = $result->first()->id;
        $object->lastId  = $result->last()->id;
        $object->count   = $result->count();

        return $object;
    }

}