<?php
namespace App\Repository;

use App\OrderList;
use App\OrderShipList;
use App\Ebay\Query;
use Illuminate\Support\Collection;

class OrderShipListRepos {


    /**
     * @var OrderShipList
     */
    public $shipList;


    /**
     * @param OrderShipList $shipList
     */
    public function __construct(OrderShipList $shipList)
    {
        $this->shipList = $shipList;
    }

    /**
     * @param $orderId
     * @return \Illuminate\Database\Eloquent\Model|null|static|OrderShipList
     */
    public function findShippingRecord($orderId) {
        $shipData = $this->shipList->where('orderId' , '=' , $orderId)->first();
        return $shipData;
    }

    /**
     * 依Tracking Number 找出相關資料
     * @param $trackNumber
     * @return  OrderShipList|null
     */
    public function findShippingRecordByTrackingNumber($trackNumber) {
        $shipData = $this->shipList->where('trackingNumber' , '=' , $trackNumber)->first();
        return $shipData;
    }

    /**
     * 新增送貨資料
     * @param array $shipData
     * @return OrderShipList
     */
    public function insertShipRecord(array $shipData) {
        $insertRecord = $this->shipList->create($shipData);
        return $insertRecord;
    }

    /**
     * 依訂單id刪除掉送貨紀錄
     * @param INT   $orderId
     * @return bool             如果找到跟並刪除回true ,除外false
     */
    public function deleteShipRecordByOrderId($orderId) {
        $record = $this->shipList->where('orderId' , $orderId)->first();
        if($record != null) {
            $record->delete();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 依上傳單號的日期 來看當日共上傳幾張
     * @param $date string 日期
     * @return int  上傳幾張
     */
    public function getUploadRecordCountByDate($date) {
        return $this->shipList->where('shippingDate' , $date)->count();
    }




}