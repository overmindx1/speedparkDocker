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

class AjaxAddressController extends Controller
{
    /**
     * 取得即時訂單
     * @param null $id  如果為空就抓取最後五筆
     * @return string   JSON String
     */
    public function getRealTimeIpnOrder() {
        $select = ['id' , 'shippingCountry' , 'shippingAddressState' , 'shippingAddressCity' , 'shippingAddressStreet' , 'shippingAddressZip' , 'shippingRecipientName'];
        $orderList = OrderList::select($select)->where('id' , '>=' , 34363)->where('id' , '<=' ,'34425')->get();
        return $orderList->toJson();
    }

    public function getUpdateAddress(){
        $post = \Request::all();
        $order = OrderList::find($post['id']);
        $order->update($post);
        return json_encode($order);
    }




}