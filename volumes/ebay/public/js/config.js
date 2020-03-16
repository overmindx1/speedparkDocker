/**
 * Created by user on 2016/11/17.
 */
var processTypeStatus = [
    {id : 0 , text : '尚未處理'},
    {id : 1 , text : '已撿貨'},
    {id : 2 , text : '以送貨完成'}
];

var orderSearchOpt = [
    {key : 1 , text : 'Order Id'},
    {key : 2 , text : 'Item Id*'},
    {key : 3 , text : 'Item Title*'},
    {key : 4 , text : 'Paypal Txn Id'},
    {key : 5 , text : 'Paypal Payment Status*'},
    {key : 6 , text : 'eBay Buyer Id*'},
    {key : 7 , text : 'eBay Buyer Mail'},
    {key : 8 , text : 'Ship Recipient Name*'},
    {key : 9 , text : 'Ship Tracking Number'},
    {key : 10 , text : 'Shipping Country*'},
    {key : 11 , text : 'eBay Sellers(Empty Keyword)*'}
];

var sellers = [
    'joeyangair2010' , 'joeyangair2011' , 'joeyangair2012' , 'speedpark.bici' , 'speedpark.velo' , 'rapido.ltd' , 'marktsp' , 'send_money'
];

var icons = {
    addressConfirmed    : '/images/icon/addressConfirm.png',
    addressUnconfirmed  : '/images/icon/addressConfirm.png',
    payerVerified       : '/images/icon/payerVerified.png',
    payerUnverified     : '/images/icon/payerUnverified.png',
    cart                : '/images/icon/cart.png',
    sendMoney           : '/images/icon/sendMoney.png',
    cashier             : '/images/icon/cashier.png',
    refund              : '/images/icon/refund.png'
};


var local = {
    'United States' : 'label-primary',
    'Canada'        : 'label-success',
    'United Kingdom': 'label-success',
    'Australia'     : 'label-info',
    'Brazil'        : 'label-purple',
    'Germany'       : 'label-warning'  
}