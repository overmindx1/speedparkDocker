<?php
/**
 * Created by PhpStorm.
 * User: Overmind
 * Date: 2016/11/3
 * Time: 下午 09:53
 */
namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Repository\DutyRecordRepos;
use App\Repository\OrderListRepos;
use App\Repository\OrderShipListRepos;
use App\OrderShipList;
use App\OrderList;
use App\DutyRecord;
use App\Ebay\Query;
use App\record;

class AjaxController extends Controller
{
    /**
     * 取得即時訂單
     * @param null $id  如果為空就抓取最後五筆
     * @return string   JSON String
     */
    public function getRealTimeIpnOrder($id = null) {
        $orderRepos = new OrderListRepos(new OrderList());
        if(is_null($id)) {
            $orderList = $orderRepos->getTodayOrderList();
        } else {
            $orderList = $orderRepos->getUncompletedOrderByIdAfter($id);
        }

        return $orderList->toJson();

    }

    /**
     * 一頁數取得列表資料
     * @param int $page 頁數
     * @return string JSON String
     */
    public function getOrderListByPage($page = 1) {
        $orderRepos = new OrderListRepos(new OrderList());
        $object = $orderRepos->getOrderListByPage($page);
        return json_encode($object);
    }

    /**
     * 取得要撿貨的品項跟圖片
     * @return string JSON API String
     */
    public function getOrderDetailForProcess() {
        $orderRepos = new OrderListRepos(new OrderList());
        $post = \Request::all();
        if(!isset($post['sellers'])) {
        	$post['sellers'] = ["joeyangair2010", "joeyangair2011", "joeyangair2012", "speedpark.bici", "speedpark.velo" , "rapido.ltd" , "marktsp"];
        }
        return $orderRepos->getOrderForProcess($post['startOrder'] , $post['endOrder'] , $post['sellers'], $post['showImage'])->toJson();
    }

    /**
     * 更新訂單的狀態
     * @return string
     */
    public function updateOrderStatusById() {
        $orderRepos = new OrderListRepos(new OrderList());
        $post = \Request::all();
        $response = new \stdClass();
        if($orderRepos->updateOrderStatusById($post['orderId'] , $post['statusType']) > 0 ) {
            $response->isSuccess = true;
        } else {
            $response->isSuccess = false;
        }
        return json_encode($response);
    }

    /**
     * 取得訂單的地址資訊
     * @return string
     */
    public function getAddressInfoByIds() {
        $orderRepos = new OrderListRepos(new OrderList());
        $post = \Request::all();
        $object = $orderRepos->getAddressInfoByIds($post['startOrder'] , $post['endOrder'] , $post['sellers']);
        return json_encode($object);
    }

    /**
     * 一搜尋的方式找出要的訂單
     * @return string
     */
    public function getOrderBySearchType() {
        $orderRepos = new OrderListRepos(new OrderList());
        $post = \Request::all();
        return $orderRepos->getOrderBySearchType($post['searchType'] ,$post['keyword'] , $post['sellers'] ,$post['startDate'] , $post['endDate'])->toJson();
    }

    public function updateOrderSelfMemo() {
        $post = \Request::all();
        $orderRepos = new OrderListRepos(new OrderList());
        $order =  $orderRepos->updateOrderSelfMemo($post['orderId'] , $post['memo']);
        return ($order != false ? $order->toJson() : json_encode(['id' => false]) );
    }

    //-----Tracking Number And Shipping
    /**
     * 更新訂單的Tracking Number
     * @return string
     */
    public function updateOrderShippingData() {
        $post = \Request::all();
        $trackingNumbersCollection  = collect($post['trackingNumber']);
        $shippingChargeCollection   = collect($post['shippingCharge']);
        $shippingDateCollection     = collect($post['shippingDate']);
        $shippingLogisticsCollection= collect($post['shippingLogistics']);
        $orderKeys = $trackingNumbersCollection->keys();
        $orderRepos = new OrderListRepos(new OrderList());
        $shipRepos = new OrderShipListRepos(new OrderShipList());
        $orders = $orderRepos->getOrderToUpdateShippingData($orderKeys->all());
        $errorRecord = [];
        foreach ($orders as $key => $order) {
            /**
             * var OrderList $order
             */
            $itemList = json_decode($order->ebayItemsList)->itemsList;
            $sellerId = $order->ebaySeller;
            $orderId  = $order->id;
            $trackingNumber = $trackingNumbersCollection->get($orderId);
            $Logistics = $shippingLogisticsCollection->get($orderId);
            $ebayQuery = new Query($sellerId , 'CompleteSale');
            $errorMsg = '';
            $shipData = $shipRepos->findShippingRecord($orderId);
            $errorRecord[$orderId] = true;
            foreach ($itemList as $itemKey => $item) {
                $action = $ebayQuery->makeTransactionAsShipped($item->itemId ,$item->txnId , $trackingNumber , false , $Logistics );
                \Log::info("-New Shipping - OrderId : ".$orderId." , ItemId: ".$item->itemId." , TxnId : ".$item->txnId." , TrackingNumber : ".$trackingNumber);
                \Log::info("Shipping Status : ".$action);
                if($action !== true) {
                    $errorMsg .= ($errorMsg == "" ? $action : '<br />'.$action);
                    $errorRecord[$orderId] = false;
                }
            }
            $data = [
                'orderId'       => $orderId,
                'trackingNumber'=> $trackingNumber,
                'itemList'      => $order->ebayItemsList,
                'errorMsg'      => $errorMsg,
                'shippingCharge'=> $shippingChargeCollection->get($orderId),
                'shippingDate'  => $shippingDateCollection->get($orderId),
                'shippingLogistics' => $shippingLogisticsCollection->get($orderId)
            ];
            if(is_null($shipData)) {
               $orderShip = $shipRepos->insertShipRecord($data);
            } else {
               $orderShip = $shipData->update($data);
            }
            $order->ProcessStatus = 2;
            $order->save();
        }


        return json_encode($errorRecord);

    }

    /**
     * 依訂單id刪除運送紀錄
     * @return string
     */
    public function deleteShipRecordByOrderId() {
        $orderId = \Request::get('orderId');
        $shipRepos = new OrderShipListRepos(new OrderShipList());
        $action = $shipRepos->deleteShipRecordByOrderId($orderId);
        return json_encode(['success' => $action ]);
    }


    //------處理紀錄---//

    /**
     * 依類別取得已經處理好的單號記錄
     * @return mixed
     */
    public function getRecord() {
        $post = \Request::all(); 
        $recordType = [
            'process'       => 1,
            'shipping'      => 2,
            'tracking'      => 3,
        ];
        $data = \DB::table('record')->where('id' , $recordType[$post['type']])->first();
        return json_encode($data);
    }

    /**
     * 依類別更新已經處理好的單號
     * @return string
     */
    public function updateRecord() {
        $post = \Request::all();
        $recordType = [
            'process'       => 1,
            'shipping'      => 2,
            'tracking'      => 3,
        ];
        $complete = \DB::table('record')->where('id' , $recordType[$post['type']])->update(['record' => $post['recordId']]);
        
        return json_encode(['complete' => $complete]);
    }

    /*--- Duty Record-----*/
    /**
     * 依頁數取得 責任資料清單
     * @param   int     $page   頁數
     * @return  string  JSON String
     */
    public function getDutyRecordByPage($page = 1) {
        $dutyRecordRepos = new DutyRecordRepos(new DutyRecord());
        $object = $dutyRecordRepos->getDutyRecordListByPage($page);
        return json_encode($object);
    }

    /**
     * 新增責任記錄資料
     * @return string
     */
    public function insertNewDutyRecord() {
        $date = date('Y-m-d');
        $post = \Request::all();
        if($date != $post['date']) {
            $data = ["success" => false , "message" => "非今天日期資料"];
            return json_encode($data);
        }
        $dutyRecordRepos = new DutyRecordRepos(new DutyRecord());
        $hasRecord = $dutyRecordRepos->getDutyRecordByDate($post['date']);
        if(!is_null($hasRecord)) {
            $data = ["success" => false , "message" => "已有新增這天的資料了"];
            return json_encode($data);
        }
        $post['processRecord']  = json_encode($post['processRecord']);
        $post['printRecord']    = json_encode($post['printRecord']);
        $post['trackingRecord'] = json_encode($post['trackingRecord']);
        return json_encode(["success" => true , "data" => $dutyRecordRepos->insertNewRecord($post)]);
    }

    /**
     * 更新責任記錄
     * @return string
     */
    public function updateDutyRecord() {
        $date = date('Y-m-d' , strtotime('-1 day'));
        $post = \Request::all();
        if($date > $post['date']) { //如果送出的日期 比前天還前面就無法更新
            $data = ["success" => false , "message" => "非今天日期資料-無法更新"];
            return json_encode($data);
        }
        $dutyRecordRepos = new DutyRecordRepos(new DutyRecord());
        $post['processRecord']  = json_encode($post['processRecord']);
        $post['printRecord']    = json_encode($post['printRecord']);
        $post['trackingRecord'] = json_encode($post['trackingRecord']);
        $action = false;
        $action = $dutyRecordRepos->updateRecord($post);
        return json_encode(["success" => $action , "message" => ""]);
    }

    /** eBay功能 **/
    /**
     * 更新商品錯誤的ItemId And TxnId
     * @return string
     */
    public function updateEbayItemTxnIdByOrderId() {
        $post = \Request::all();
        $orderModel = new OrderList();
        $order = $orderModel->find($post['orderId']);
        if(is_null($order)) {
            return json_encode(['success' => false , 'message' => 'Can\'t Found Order-Db']);
        }
        $eBayQuery = new Query($order->ebaySeller , 'GetOrders');
        $object = $eBayQuery->changeOrderItemTxnId($order);

        if($object->ReturnedOrderCountActual > 0 ) {
            $lists = $object->OrderArray;
            $itemsRecord = [];
            $record = [];
            foreach($lists->Order as $list) {

                if($order->ebayBuyerId == $list->BuyerUserID) { //比對定單買家名稱有沒有一樣
                    foreach($list->TransactionArray[0] as $item) {
                        $itemsRecord[] = [
                            'itemId' =>    $item->Item->ItemID,
                            'txnId'  =>    $item->TransactionID,
                        ];
                    }
                }
            }
            $orderItemList = json_decode($order->ebayItemsList ,true);
            $record['origin'] = $orderItemList;
            foreach ($orderItemList['itemsList'] as $key => $item) {
                foreach ($itemsRecord as $eItem) {
                    if($eItem['itemId'] == $item['itemId']) {
                        $orderItemList['itemsList'][$key]['txnId'] = (string)$eItem['txnId'];
                    }
                }
            }
            $record['new'] = $orderItemList;
            $newOrderItemList = json_encode($orderItemList);
            $order->ebayItemsList = $newOrderItemList;
            $action = $order->save();
            if($action) {
                return json_encode(['success' => true , 'message' => '' , 'data' => $record]);
            } else {
                return json_encode(['success' => false , 'message' => 'Can\'t Find Valid ItemId And TxnId']);
            }
        } else { //找不到定單
            return json_encode(['success' => false , 'message' => 'Can\'t Found Order-Ebay']);
        }
    }

    /**
     * 依日期找出當天的統計資料
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatisticsByDate() {
        $shipRepos  = new OrderShipListRepos(new OrderShipList());
        $orderRepos = new OrderListRepos(new OrderList());
        $post = \Request::all();
        $startDate  = new \DateTime($post['startDate']);
        $interval   = new \DateInterval('P1D');
        $endDate    = new \DateTime($post['endDate']);
        $endDate->add($interval);

        $period = new \DatePeriod($startDate,$interval,$endDate );

        $object     = new \stdClass();
        $object->data = [];
        $countOrder = 0;
        $countUpload= 0;
        foreach($period as $date) {
            $day = $date->format('Y-m-d');
            $data = [
                'date'           => $day,
                'order'          => $orderRepos->getOrdersRangeByDate($day),
                'trackingNumber' => $shipRepos->getUploadRecordCountByDate($day)
            ];
            $countOrder += $data['order']->count;
            $countUpload+= $data['trackingNumber'];
            array_push($object->data , $data);
        }
        $object->countOrder   = $countOrder;
        $object->countUpload  = $countUpload;
        return json_encode($object);
    }

    public function getBuyCountryCountByDateRange(){
        $post = \Request::all();
        $orderRepos = new OrderListRepos(new OrderList());
        $data = $orderRepos->getBuyCountryCountByDateRange($post['startDate'] , $post['endDate'] , $post['sellers']);
        return json_encode($data);
    }
}