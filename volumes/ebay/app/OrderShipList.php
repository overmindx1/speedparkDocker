<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OrderShipList
 *
 * @property integer $id
 * @property integer $orderId
 * @property string $trackingNumber
 * @property float $shippingCharge
 * @property string $shippingDate
 * @property string $shippingEntryTime
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\OrderList $belongOrder
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereTrackingNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereShippingCharge($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereShippingDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereShippingEntryTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $itemList eBay物品相關資料
 * @property string $errorMsg 如果有錯誤訊息
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereItemList($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderShipList whereErrorMsg($value)
 */
class OrderShipList extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orderShipList';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'orderId' , 'trackingNumber' , 'itemList' , 'errorMsg', 'shippingCharge' , 'shippingDate' , 'shippingLogistics'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongOrder()
    {
        return $this->belongsTo('App\OrderList' , 'orderId');
    }
}