<?php
/**
 * Created by PhpStorm.
 * User: Overmind
 * Date: 2016/11/2
 * Time: 下午 09:28
 */

namespace App\Ebay;
//use App\Ebay\eBaySession;


use App\OrderList;

class Query {

    public $config;
    public $ebaySession;

    public function __construct($sellerId = 'joeyangair2010' ,$verb = 'GetItem')
    {
        $this->config = config('ebay.'.$sellerId);
        $this->ebaySession = new eBaySession(
            $this->config['userToken_'],
            $this->config['devID_'],
            $this->config['appID_'],
            $this->config['certID_'],
            $this->config['serverUrl_'],
            $this->config['compatabilityLevel'],
            0,
            $verb );

    }

    /**
     * 用商品id 找出賣家
     * @param $itemId Int ebay 商品id
     * @return String 這個商品的賣家id
     */
    public function getSellerIdByItemID($itemId) {
        $request = '<?xml version="1.0" encoding="utf-8" ?><GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents"><RequesterCredentials><eBayAuthToken>' . $this->config['userToken_'] . '</eBayAuthToken></RequesterCredentials><DetailLevel>ReturnAll</DetailLevel><ItemID>'.$itemId.'</ItemID><OutputSelector>Seller</OutputSelector></GetItemRequest>';
        $request = trim($request);
        $response = $this->ebaySession->sendHttpRequest($request);
        $object = simplexml_load_string($response);
        if($object[0]->Ack == 'Failure') {
            $to = ['speedparkpt@gmail.com' , 'overmindx@gmail.com'];
            \Mail::raw($object[0]->Errors->LongMessage, function($mail) use ( $to ) {
                $mail->to($to)->subject('系統出錯報告,有單無法寫入');
            });
            exit();            
        } else {
            $sellerId = $object->Item->Seller->UserID;
            return $sellerId;
        }
        
    }

    /**
     * 用商品id 找出圖片
     * @param $itemId Int ebay 商品id
     * @return String 商品的圖片位置
     */
    public function getPicUrlByItemID($itemId) {
        $request = '<?xml version="1.0" encoding="utf-8" ?><GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents"><RequesterCredentials><eBayAuthToken>' . $this->config['userToken_'] . '</eBayAuthToken></RequesterCredentials><DetailLevel>ReturnAll</DetailLevel><ItemID>'.$itemId.'</ItemID><OutputSelector>PictureDetails</OutputSelector></GetItemRequest>';
        $request = trim($request);
        $response = $this->ebaySession->sendHttpRequest($request);
        $object = simplexml_load_string($response);
        if($object->Ack == 'Success') {
            //return $object->Item->PictureDetails->PictureURL;
            if(isset($object->Item->PictureDetails->PictureURL)) {
                return $object->Item->PictureDetails->PictureURL;
            } else {
                return $object->Item->PictureDetails->GalleryURL;
            }
        }
        else {
            return false;
        }
    }


    /**
     * 將商品告術eBay以運輸
     * @param integer       $itemId       商品id
     * @param integer       $ebayTxnId    商品Txn
     * @param string        $trackingNum  商品Tracking Number
     * @param string|bool   $shipDate     商品運送的日期
     * @param string        $carrierName  運送的公司(預設郵局)
     * @return bool|string
     */
    public function makeTransactionAsShipped($itemId , $ebayTxnId , $trackingNum = '' , $shipDate = false ,$carrierName = '') {
        if($shipDate != false) {
            $shippedTime = gmdate($shipDate."\TH:i:s\Z");
        } else {
            $shippedTime = gmdate("Y-m-d\TH:i:s\Z");
        }

        $request = '<?xml version="1.0" encoding="utf-8" ?>';
        $request .= '<CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $request .= '<RequesterCredentials><eBayAuthToken>' . $this->config['userToken_'] . '</eBayAuthToken></RequesterCredentials>';
        $request .= '<ItemID>'.$itemId.'</ItemID>';
        $request .= '<TransactionID>'.$ebayTxnId.'</TransactionID>';
        $request .= '<Shipment>';
        $request .= '<ShipmentTrackingNumber>'.$trackingNum.'</ShipmentTrackingNumber>';
        $request .= '<ShippedTime>'.$shippedTime.'</ShippedTime>';
        if($carrierName != '') {
            $request .= '<ShippingCarrierUsed>'.$carrierName.'</ShippingCarrierUsed>';
        } else {
            $request .= '<ShippingCarrierUsed>Chunghwa Post</ShippingCarrierUsed>';
        }
        $request .= '<ShippedSpecified>FALSE</ShippedSpecified>';
        $request .= '</Shipment>';
        $request .= '<Shipped>TRUE</Shipped>';
        $request .= '</CompleteSaleRequest>';
        $request = trim($request);
        $response = $this->ebaySession->sendHttpRequest($request);
        $object = simplexml_load_string($response);
        \Log::info($request );
        \Log::info($response );
        if($object->Ack == 'Success') {
            return true;
        } else {
            return 'ErrorCode: ' . $object->Errors->ErrorCode .' | '.$object->Errors->LongMessage;
        }

    }

    /**
     * @param OrderList $order
     *
     */
    public function changeOrderItemTxnId($order){
        $startTime = (gmdate("Y-m-d\TH:i:s.00\Z" , strtotime($order->paymentDate.'-30 mins')));
        $endTime = (gmdate("Y-m-d\TH:i:s.00\Z" , strtotime($order->paymentDate.'+30 mins')));

        $request  = '<?xml version="1.0" encoding="utf-8"?>';
        $request .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $request .= '<CreateTimeFrom>'.$startTime.'</CreateTimeFrom>';
        $request .= '<CreateTimeTo>'.$endTime.'</CreateTimeTo>';
        $request .= '<OrderRole>Seller</OrderRole>';
        $request .= '<OrderStatus>Completed</OrderStatus>';
        $request .= '<RequesterCredentials>';
        $request .= '<eBayAuthToken>'.$this->config['userToken_'].'</eBayAuthToken>';
        $request .= '</RequesterCredentials>';
        $request .= '</GetOrdersRequest>';

        $request = trim($request);
        $response = $this->ebaySession->sendHttpRequest($request);
        $object = simplexml_load_string($response);

        if($object->Ack == 'Success') {
            return $object;
            //if($object->ReturnedOrderCountActual > 0 ) {
            //    $lists = $object->OrderArray;
            //    $itemsRecord = [];
            //    foreach($lists as $list) {
            //        if($order->ebayBuyerId == $list->Order->BuyerUserID) { //比對定單買家名稱有沒有一樣
            //            foreach($list->Order->TransactionArray[0] as $item) {
            //                $itemsRecord[] = [
            //                    'itemId' =>    $item->Item->ItemID,
            //                    'txnId'  =>    $item->TransactionID,
            //                ];
            //            }
            //        }
            //    }
            //    $orderItemList = json_decode($order->ebayItemsList ,true);
            //    foreach ($orderItemList['itemsList'] as $key => $item) {
            //        foreach ($itemsRecord as $eItem) {
            //            if($eItem['itemId'] == $item['itemId']) {
            //                $orderItemList['itemsList'][$key]['txnId'] = $eItem['txnId'];
            //            }
            //        }
            //    }
            //    $newOrderItemList = json_encode($orderItemList);
            //    $order->ebayItemsList = $newOrderItemList;
            //    $order->save();
            //    return true;
            //} else { //找不到定單
            //    return false;
            //}
        } else {
            return false;
        }

    }
}