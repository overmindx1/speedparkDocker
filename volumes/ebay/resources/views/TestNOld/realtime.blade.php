@extends('mainbase')

@section('dataContainer')

    <div class="panel panel-default listDataTable" xmlns="http://www.w3.org/1999/html">
        <!-- Default panel contents -->
        <div class="panel-heading">
            即時訂單(定時更新) | 詳細資料 <button type="button" @click="showAllDetailTrigger()" class="btn btn-info btn-xs" >全部展開/關閉</button>
        </div>


        <table class="table table-striped table-bordered datalist">
            <tr>
                <th>#</th>
                <th>eBay帳號</th>
                <th>付款時間</th>
                <th>
                    <a href="{{url('https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/#ipn-transaction-types')}}" target="_blank">
                        交易類別
                    </a>
                </th>
                <th>交易狀態</th>
                <th>eBay買家id</th>
                <th>交易金額</th>
                <th>國家</th>
                <th>相關資料</th>

            </tr>
            @verbatim
            <tbody v-for="order in orderList" :style="(order.showDetail ? {border : '1px solid red'} : {} )">
            <tr >
                <td>
                    {{order.id}} | <button type="button" class="btn btn-info btn-xs" @click="triggerDetail(order.id)">詳細/簡易</button>
                </td>
                <td>{{order.ebaySeller}}</td>
                <td>{{order.paymentDate}}</td>
                <td>
                    <!--<img :src="order.payTypeIcon" alt="交易方式" />-->
                    {{order.paypalTxnType}}
                </td>
                <td>
                    <!--<img :src="order.payStatusIcon" alt="交易狀態" />-->
                    {{order.paypalPaymentStatus}}
                </td>
                <td><a target="_blank" href="http://cgi6.ebay.com/ws/eBayISAPI.dll?ViewBidItems&all=1&_rdc=1&userid={{order.ebayBuyerId}}&&rows=25&completed=1&sort=3&guest=1">{{order.ebayBuyerId}}</a></td>
                <td>{{order.totalPayment}} | {{order.CurrencyCode}}</td>
                <td>{{order.shippingCountry}}</td>
                <td> 
                    <img :src="order.payerIcon" alt="付款者狀態" />
                    <img :src="order.addressIcon" alt="地址狀態" />
                    <img v-if="order.payerMemoIcon" :src="order.payerMemoIcon" alt="買家備註" />
                </td>
             </tr>
             <tr v-if="order.showDetail">
                 <td colspan="11" class="searchDetail">

                     <div class="page-header">
                         <h4>購買物品</h4>
                     </div>
                     <div class="row orderItemsBlack " v-for="item in order.ebayItemsList.itemsList">
                         <div class="col-md-2">
                             <div  class="thumbnail">
                                 <img  :src="order.imagePath[item.itemId][0]" />
                             </div>
                         </div>

                         <div class="col-md-9 itemDescBlock">
                             <div class="row ">
                                 <div class="col-md-2">
                                     品名 :
                                 </div>
                                 <div class="col-md-10 ">
                                     {{item.itemName}}
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-2">
                                     價格:
                                 </div>
                                 <div class="col-md-10">
                                     {{item.itemPrice}}
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-2">
                                     數量 :
                                 </div>
                                 <div class="col-md-10" :style="(item.quantity > 1 ? {color: 'red'} : {}) ">
                                     {{item.quantity}}
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-2">
                                     Item Id :
                                 </div>
                                 <div class="col-md-10">
                                     <a target="_blank" :href="'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&rd=1&item=' + item.itemId + '&ssPageName=STRK:MESE:IT'">
                                         {{item.itemId}}
                                     </a>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="page-header">
                         <h4>其他資訊:</h4>
                     </div>
                     <div class="row page-header">
                         <div class="col-sm-4 col-md-2">送貨資訊:</div>
                         <div class="col-sm-8 col-md-10" v-html="order.addressDetail"></div>
                     </div>
                     <div class="row page-header">
                         <div class="col-sm-4 col-md-2">賣家備註:</div>
                         <div class="col-sm-8 col-md-10" >
                             <div class="input-group">
                                 <input class="form-control" v-model="order.selfMemo" :disabled="order.disableMemoInput" aria-label="Text input with multiple buttons">
                                 <div class="input-group-btn">
                                     <button type="button" class="btn btn-default" @click="canWriteSelfMemo(order.id)" aria-label="Help">
                                         <span class="glyphicon glyphicon-pencil"></span>
                                     </button>
                                     <button type="button" class="btn btn-default" @click="updateOrderSelfMemo(order.id , order.selfMemo)" :disabled="order.disableMemoBtn">更新</button>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="row page-header" v-if="order.payerMemoIcon">
                         <div class="col-sm-4 col-md-2">買家備註:</div>
                         <div class="col-sm-8 col-md-10" >{{order.paypalPayerMemo}}</div>
                     </div>

                     <!--{{{order.itemsDetail }}} <br /> {{{order.fullAddress}}} <br /> 電話 : {{{order.payerPhone}}}-->
                 </td>

             </tr>
            </tbody>
            @endverbatim
        </table>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.28/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
    <script src="https://cdn.bootcss.com/vue-strap/1.1.29/vue-strap.js"></script>

    <script>
        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        new Vue({
            el:'.listDataTable',
            data: {
                showDetail : false,
                setOrderId : 0,
                isInit     : false, //判定是不是已經讀取最前面五筆
                orderList : []
            },
            methods : {
                canWriteSelfMemo    : function(orderId) {
                    for(var o in this.orderList) {
                        if(this.orderList[o].id == orderId) {
                            this.orderList[o].disableMemoInput  = false;
                            this.orderList[o].disableMemoBtn    = false;
                        }
                    }
                },
                updateOrderSelfMemo : function(orderId , memo) {
                    var body = {
                        orderId : orderId ,
                        memo    : memo
                    };
                    var  url = '/v1/updateOrderSelfMemo';

                    this.$http.post(url , body).then(function(response) {
                        var order = JSON.parse(response.body);
                        if(order.id == false) {
                            alert("更新失敗 , 請在試一次看看!");
                        } else {
                            //更新成功
                            for(var o in this.orderList) {
                                if(this.orderList[o].id == orderId) {
                                    this.orderList[o].disableMemoInput  = true;
                                    this.orderList[o].disableMemoBtn    = true;
                                }
                            }
                        }
                    });

                },
                showAllDetailTrigger : function(){
                    this.showDetail = (this.showDetail ? false : true);
                    for(var o in this.orderList) {
                        this.orderList[o].showDetail = (this.showDetail ? true : false);
                    }
                },
                triggerDetail : function(orderId) {
                    //this.showDetails[orderId] = (this.showDetails[orderId] ? false : true);
                    for(var o in this.orderList) {
                        if(this.orderList[o].id == orderId) {
                            this.orderList[o].showDetail = (this.orderList[o].showDetail ? false : true);
                            console.log(this.orderList[o].id);
                            break;
                        }
                    }
                },
                listOrderItem : function(listObj) { //列出詳細物品
                    //console.log(listObj);
                    var list = JSON.parse(listObj);
                    var returnData = '購買物品<br />';
                    for(var o in list.itemsList) {
                        returnData += '     物品 : '+ list.itemsList[o].itemName+ '<br />';
                        returnData += '     數量 : '+ list.itemsList[o].quantity+ '<br />';
                    }
                    return returnData;
                },
                formatAddress : function(obj) { //排列地址資料
                    var address  = obj.shippingRecipientName + '<br />';
                    address     += obj.shippingAddressStreet + '<br />';
                    address     += obj.shippingAddressCity +' '+ obj.shippingAddressState +' '+ obj.shippingAddressZip + '<br />';
                    address     += obj.shippingCountry + '<br />';
                    address     += 'TEL:' + obj.payerPhone;
                    return address;
                },
                determineIcon : function(obj) { // 列出訂單狀態
                    var icons = {
                        addressConfirmed    : '/images/icon/addressConfirm.png',
                        addressUnconfirmed  : '/images/icon/addressConfirm.png',
                        payerVerified       : '/images/icon/payerVerified.png',
                        payerUnverified     : '/images/icon/payerUnverified.png',
                        cart                : '/images/icon/cart.png',
                        sendMoney           : '/images/icon/sendMoney.png',
                        cashier             : '/images/icon/cashier.png',
                        refund              : '/images/icon/refund.png',
                        payerMemo              : '/images/icon/payerMemo.png'
                    };
                    obj.payerMemoIcon= (obj.paypalPayerMemo != null ? icons.payerMemo : false);
                    obj.addressIcon = (obj.paypalPayerAddressConfirmed == 'confirmed' ? icons.addressConfirmed : icons.addressUnconfirmed);
                    obj.payerIcon   = (obj.paypalPayerStatus == 'verified' ? icons.payerVerified : icons.payerUnverified);
                    obj.payTypeIcon = (obj.paypalTxnType == 'cart' ? icons.cart : icons.sendMoney);
                    obj.payStatusIcon= (obj.paypalPaymentStatus == 'Completed' ? icons.cashier : icons.refund);
                },
                getRealTimeOrderById : function() {
                    var url ='';
                    if(this.setOrderId == 0) {
                         url = '/v1/getRealTimeOrder';
                    } else {
                         url = '/v1/getRealTimeOrder/'+this.setOrderId;
                    }
                    this.$http.get(url).then( function(response) {
                        var orderlist = JSON.parse(response.body);
                        for(var o in orderlist) {
                            orderlist[o].itemsDetail = this.listOrderItem(orderlist[o].ebayItemsList);
                            orderlist[o].ebayItemsList = JSON.parse(orderlist[o].ebayItemsList);
                            orderlist[o].addressDetail = this.formatAddress(orderlist[o]);
                            orderlist[o].showDetail = false;
                            orderlist[o].disableMemoInput = true;
                            orderlist[o].disableMemoBtn   = true;
                            this.determineIcon(orderlist[o]);
                            if(orderlist[o].id > this.setOrderId) {
                                this.setOrderId = orderlist[o].id;
                            }
                        }
                        if(this.isInit) {
                            var orders = JSON.parse(response.body);
                            if(orders.length > 0) {
                                for(var o in orders) {
                                    orders[o].itemsDetail = this.listOrderItem(orders[o].ebayItemsList);
                                    orders[o].ebayItemsList = JSON.parse(orders[o].ebayItemsList);
                                    orders[o].addressDetail = this.formatAddress(orders[o]);
                                    orders[o].showDetail = false;
                                    orders[o].disableMemoInput = true;
                                    orders[o].disableMemoBtn   = true;
                                    this.determineIcon(orders[o]);
                                    this.orderList.unshift(orders[o]);
                                }
                                //推播
                                if(Notification.permission === "granted") {
                                    var notification = new Notification("有新的單進來囉!" , {});
                                } else {
                                    Notification.requestPermission(function (permission) {
                                        // If the user accepts, let's create a notification
                                        if (permission === "granted") {
                                            var notification = new Notification("有新的單進來囉!" , {});
                                        }
                                    });
                                }

                            }
                        } else {
                            console.log(orderlist);
                            this.orderList = orderlist;
                        }
                        console.log(this.setOrderId);

                        this.isInit = true;
                    });

                }
            },
            created : function(){
                this.getRealTimeOrderById();
                setInterval( this.getRealTimeOrderById , 180000);
            }
        });
    </script>
@endsection