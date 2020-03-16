<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OrderList
 *
 * @mixin \Eloquent
 * @property integer $id 主鑑
 * @property string $ebaySeller
 * @property string $paypalSellerMail
 * @property string $verifyStatus 交易狀態
 * @property string $paymentDate 付款日期台灣時間
 * @property string $paymentDatePDT 付款日期太平洋日光時間
 * @property string $paypalTxnId paypalTxnId
 * @property string $paypalTxnType paypal 交易模式
 * @property string $paypalPayerId paypal 付款人id
 * @property string $paypalPayerMail paypal 付款人Mail
 * @property string $paypalPayerFirstName 姓氏
 * @property string $paypalPayerLastName 名稱
 * @property string $paypalPayerStatus paypal 付款人認證狀態
 * @property string $paypalPaymentType paypal 交易模式
 * @property string $paypalProtectionEligibility 交易保護
 * @property string $paypalPaymentStatus paypal 交易狀態
 * @property string $paypalPendingReason paypal 交易未定理由
 * @property string $paypalVerifySign 認證用
 * @property string $paypalIPNTrackId 追鐘用
 * @property string $paypalPayerAddressConfirmed 住址確認
 * @property string $paypalPayerMemo 買家PS
 * @property string $selfMemo 自己備註
 * @property string $custom ebay EMS
 * @property string $ebayItemsList ebay買家購物清單 Json
 * @property string $ebayTxnList ebay交易id
 * @property string $ebayBuyerId ebay買家id
 * @property string $shippingCountryCode 國家代碼
 * @property string $shippingCountry 寄送國家
 * @property string $shippingAddressCity 寄送國家城市
 * @property string $shippingAddressStreet 寄送地址
 * @property string $shippingAddressZip 郵遞區號
 * @property string $shippingRecipientName 收件人名稱
 * @property string $payerPhone 電話
 * @property float $totalPayment 付款全額
 * @property float $paypalFee paypal手續費
 * @property float $shippingFee 運送費
 * @property float $Tax 稅金
 * @property string $shippingMethod 運送方式
 * @property string $CurrencyCode 金額代碼
 * @property integer $ProcessStatus 處理狀態
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereEbaySeller($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalSellerMail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereVerifyStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaymentDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaymentDatePDT($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalTxnId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalTxnType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPayerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPayerMail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPayerFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPayerLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPayerStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPaymentType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalProtectionEligibility($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPaymentStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPendingReason($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalVerifySign($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalIPNTrackId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPayerAddressConfirmed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalPayerMemo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereSelfMemo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereCustom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereEbayItemsList($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereEbayTxnList($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereEbayBuyerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingCountryCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingAddressCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingAddressStreet($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingAddressZip($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingRecipientName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePayerPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereTotalPayment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalFee($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingFee($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereTax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereCurrencyCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereProcessStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereUpdatedAt($value)
 * @property string $paypalStatusReason paypal 異常狀態理由
 * @property string $shippingAddressState
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OrderShipList[] $hasShip
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaypalStatusReason($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList whereShippingAddressState($value)
 * @property string $paymentUpdate 訂單更新(Refunded or ...)
 * @method static \Illuminate\Database\Query\Builder|\App\OrderList wherePaymentUpdate($value)
 */
class OrderList extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orderList';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ebaySeller',
        'paypalSellerMail',
        'verifyStatus',
        'paymentDate',
        'paymentDatePDT',
        'paypalTxnId',
        'paypalTxnType',
        'paypalPayerId',
        'paypalPayerMail',
        'paypalPayerFirstName',
        'paypalPayerLastName',
        'paypalPayerStatus',
        'paypalPaymentType',
        'paypalProtectionEligibility',
        'paypalPaymentStatus',
        'paypalPendingReason',
        'paypalVerifySign',
        'paypalIPNTrackId',
        'paypalPayerAddressConfirmed',
        'paypalPayerMemo',
        'selfMemo',
        'custom',
        'ebayItemsList',
        'ebayTxnList',
        'ebayBuyerId',
        'shippingCountryCode',
        'shippingCountry',
        'shippingAddressState',
        'shippingAddressCity',
        'shippingAddressStreet',
        'shippingAddressZip',
        'shippingRecipientName',
        'payerPhone',
        'totalPayment',
        'paypalFee',
        'shippingFee',
        'Tax',
        'shippingMethod',
        'CurrencyCode',
        'paymentUpdate',
        'ProcessStatus',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];


    public function hasShip()
    {
        return $this->hasOne('App\OrderShipList' , 'orderId');
    }
}
