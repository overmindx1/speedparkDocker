@extends('mainbase')

@section('dataContainer')

    <div class="panel panel-default listDataTable" xmlns="http://www.w3.org/1999/html">
        <!-- Default panel contents -->
        <div class="panel-heading">
           即時訂單(Periodic Update) | Detail <button type="button" @click="showAllDetailTrigger()" class="btn btn-info btn-xs" >All Expand/Close</button>
        </div>

        <list-item :order-list="orderList">

        </list-item>

    </div>
    @verbatim
    <template id="orderList">
        <div>
            
            <div class="panel-body searchDetail" v-for="order in orderList">

                <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                    <thead>
                    <tr>
                        <td class="fstImgBlock">Image</td>
                        <td class="fstInformation">Item Information</td>
                        <td class="fstInformation">Main Information</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item , key) in order.ebayItemsList.itemsList" v-if="order.ebayItemsList.itemsList.length > 0"><!--相關物品列表 一般單用-->
                        <td>
                            <img class="imgWidth" v-if="Object.keys(order.imagePath).length" :src="order.imagePath[item.itemId][0]" />
                        </td>
                        <td class="fstInformationFont">
                            <div><!--物品名稱跟連結-->
                                <div class="tableTitleBlock2">Item:</div>
                                <a target="_blank" :href="'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&rd=1&item=' + item.itemId + '&ssPageName=STRK:MESE:IT'">
                                    {{item.itemName}}
                                </a>

                            </div>
                            <!--物品價格跟運費-->
                            <div>
                                <div class="tableTitleBlock2">Price:</div>
                                {{item.itemPrice}} <span v-if="order.shippingFee != 0"> - ShippingFee : {{order.shippingFee}} |{{order.CurrencyCode}}</span></div>
                            <div >
                                <div class="tableTitleBlock2">Qty: </div>
                                <span :style="(item.quantity > 1 ? {color: 'red'} : {}) ">{{item.quantity}}</span>
                            </div>
                            <!--Speed Park 客制功能-->                            
                            <div v-if="local[order.shippingCountry] !== undefined">
                                <div class="tableTitleBlock2">Locat:  </div>
                                <span class="label" :class="[local[order.shippingCountry]]">{{order.shippingCountry}}</span>                                
                            </div>
                        </td>
                        <td v-if="key == 0" :rowspan="order.ebayItemsList.itemsList.length"><!--顯示主要買賣家資訊跟相關備註-->
                            <div>
                                <span class="orderIdFontSize"><b># {{order.id}}</b></span> |
                                <button type="button" class="btn btn-primary btn-xs" @click="showDetail(order.id)">展開 / 收起</button>
                                <!--如果單目前是Pending-->
                                <span  class="label label-warning" v-if="order.paypalPaymentStatus == 'Pending'" >{{order.paypalPaymentStatus}}</span>
                                <!--如果Refund等異常-->
                                <span  class="label label-danger" v-if="order.paymentUpdate != null" v-for="spec in order.paymentUpdate">{{spec.paymentStatus}}</span>
                            </div>
                            <div>eBay賣家: {{order.ebaySeller}}</div>
                            <div>eBay買家 :
                                <a target="_blank" :href="'http://cgi6.ebay.com/ws/eBayISAPI.dll?ViewBidItems&all=1&_rdc=1&userid=' +order.ebayBuyerId+ '&&rows=25&completed=1&sort=3&guest=1'">
                                    {{order.ebayBuyerId}}
                                </a>
                            </div>
                            <div v-if="order.paypalPayerMemo != null" class="buyerMemoBlock">
                                買家備註 : {{order.paypalPayerMemo}}
                            </div>
                            <!--賣家備註-->
                            <textarea class="form-control" :class="{selfMemoUpdate : (order.selfMemo != null && order.selfMemo != '')}" rows="2" v-model="order.selfMemo" placeholder="賣家備註" :disabled="order.disableMemoInput" ></textarea>
                            <button type="button" class="btn btn-default" @click="canWriteSelfMemo(order.id)"  aria-label="Help">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <button type="button" class="btn btn-default" @click="updateOrderSelfMemo(order.id , order.selfMemo)" :disabled="order.disableMemoBtn" >更新</button>
                        </td>
                    </tr>

                    <tr v-if="order.ebayItemsList.itemsList.length == 0"><!--相關物品列表 sendMoney用 , 沒有物品-->
                        <td>
                            <img class="imgWidth"  src="/images/icon/send_money.jpg" />
                        </td>
                        <td class="fstInformationFont">
                            <div><!--物品名稱跟連結 send money 沒有-->

                            </div>
                            <!--物品價格跟運費 send money 沒有物品價格-->
                            <div>
                                <div class="tableTitleBlock2">Price:</div>
                                <span v-if="order.shippingFee != 0"> - ShippingFee : {{order.shippingFee}} |{{order.CurrencyCode}}</span></div>
                            <div >
                                <div class="tableTitleBlock2">Qty: </div>
                            </div>
                            <!--Speed Park 客制功能-->                            
                            <div v-if="local[order.shippingCountry] !== undefined">
                                <div class="tableTitleBlock2">Locat:  </div>
                                <span class="label" :class="[local[order.shippingCountry]]">{{order.shippingCountry}}</span>                                
                            </div>
                        </td>
                        <td ><!--顯示主要買賣家資訊跟相關備註-->
                            <div>
                                <span class="orderIdFontSize"><b># {{order.id}}</b></span> |
                                <button type="button" class="btn btn-primary btn-xs" @click="showDetail(order.id)">展開 / 收起</button>
                                <!--如果單目前是Pending-->
                                <span  class="label label-warning" v-if="order.paypalPaymentStatus == 'Pending'" >{{order.paypalPaymentStatus}}</span>
                                <!--如果Refund等異常-->
                                <span  class="label label-danger" v-if="order.paymentUpdate != null" v-for="spec in order.paymentUpdate">{{spec.paymentStatus}}</span>
                            </div>
                            <div>eBay賣家: {{order.ebaySeller}}</div>
                            <div>eBay買家 :
                                <a target="_blank" :href="'http://cgi6.ebay.com/ws/eBayISAPI.dll?ViewBidItems&all=1&_rdc=1&userid=' +order.ebayBuyerId+ '&&rows=25&completed=1&sort=3&guest=1'">
                                    {{order.ebayBuyerId}}
                                </a>
                            </div>
                            <div v-if="order.paypalPayerMemo != null" class="buyerMemoBlock">
                                買家備註 : {{order.paypalPayerMemo}}
                            </div>
                            <!--賣家備註-->
                            <textarea class="form-control" :class="{selfMemoUpdate : (order.selfMemo != null && order.selfMemo != '')}" rows="2" v-model="order.selfMemo" placeholder="賣家備註" :disabled="order.disableMemoInput" ></textarea>
                            <button type="button" class="btn btn-default" @click="canWriteSelfMemo(order.id)"  aria-label="Help">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <button type="button" class="btn btn-default" @click="updateOrderSelfMemo(order.id , order.selfMemo)" :disabled="order.disableMemoBtn" >更新</button>
                        </td>
                    </tr>

                    </tbody>
                </table>

                <table class="table table-bordered table-condensed" style="margin-bottom: 0;" v-show="order.showDetail" ><!--展開資訊-->
                    <thead>
                    <tr>
                        <td class="sndInformation">Trade Information</td>
                        <td class="sndInformation">Shipping Information</td>
                        <td class="sndInformation">Fee Information</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><!--Paypal Information-->
                            <div>
                                <div class="tableTitleBlock">PayTime:</div>
                                {{order.paymentDate}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">PDTTime:</div>
                                {{order.paymentDatePDT}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">PayStatus:</div>
                                {{order.paypalPaymentStatus}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">PayType:</div>
                                {{order.paypalPaymentType}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">Type:</div>
                                {{order.paypalTxnType}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">TransID:</div>
                                <a target="_blank" :href="'https://www.paypal.com/us/vst/id='+order.paypalTxnId">{{order.paypalTxnId}}</a>
                            </div>
                            <div>
                                <div class="tableTitleBlock">PayerMail:</div>
                                {{order.paypalPayerMail}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">FirstName:</div>
                                {{order.paypalPayerFirstName}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">LastName:</div>
                                {{order.paypalPayerLastName}}
                            </div>
                        </td>
                        <td><!--Shipping Information-->
                            <div>
                                <div class="tableTitleBlock">Recipient:</div>
                                {{order.shippingRecipientName}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">Street:</div>
                                {{order.shippingAddressStreet}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">City:</div>
                                {{order.shippingAddressCity}} {{order.shippingAddressZip}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">State:</div>
                                {{order.shippingAddressState}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">Country:</div>
                                {{order.shippingCountry}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">CountryCode:</div>
                                {{order.shippingCountryCode}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">TrackingNum:</div>
                                {{(order.has_ship ? order.has_ship.trackingNumber : 'Not Yet!')}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">shippingDate:</div>
                                {{(order.has_ship ? order.has_ship.shippingDate : 'Not Yet!')}}
                            </div>
                            <div v-if="order.has_ship">
                                <div class="tableTitleBlock" >UpdateStatus:</div>
                                {{(order.has_ship.errorMsg == '' ? 'Successful' : 'Failed')}}
                            </div>
                            <div v-else>
                                <div class="tableTitleBlock" >UpdateStatus:</div>
                                Not Yet!
                            </div>
                            <div>
                                <div class="tableTitleBlock">Method:</div>
                                {{(order.shippingMethod)}}
                            </div>
                        </td>
                        <td ><!--Fee Information-->
                            <div>
                                <div class="tableTitleBlock">Total:</div>
                                {{order.totalPayment}} | {{order.CurrencyCode}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">PaypalFee:</div>
                                {{order.paypalFee}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">shippingFee:</div>
                                {{order.shippingFee}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">Tax:</div>
                                {{order.Tax}}
                            </div>
                            <div>
                                <div class="tableTitleBlock">Tax:</div>
                                {{order.Tax}}
                            </div>

                            <div class="specData" v-if="order.paymentUpdate != null" v-for="spec in order.paymentUpdate">
                                <div>
                                    <div class="tableTitleBlock">Status:</div>
                                    {{spec.paymentStatus}}
                                </div>
                                <div>
                                    <div class="tableTitleBlock">TransID:</div>
                                    <a target="_blank" :href="'https://www.paypal.com/us/vst/id='+spec.paypalTxnId">{{spec.paypalTxnId}}</a>
                                </div>
                                <div>
                                    <div class="tableTitleBlock">Money:</div>
                                    {{spec.money}}
                                </div>
                                <div>
                                    <div class="tableTitleBlock">Fee:</div>
                                    {{spec.fee}}
                                </div>
                                <div>
                                    <div class="tableTitleBlock">Update:</div>
                                    {{spec.update}}
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </template>
    @endverbatim

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>


    <script>
        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        Vue.component('list-item', {
            template: '#orderList',
            props: ['orderList'],
            methods : {
                showDetail : function (orderId) {
                    for(var o in this.orderList) {
                        if(this.orderList[o].id == orderId) {
                            this.orderList[o].showDetail  = !this.orderList[o].showDetail;
                        }
                    }
                },
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
            }
        });

        new Vue({
            el:'.listDataTable',
            data: {
                isInit     : false, //判定是不是已經讀取最前面五筆
                setOrderId : 0,
                orderList : [],
                showDetail:false
            },
            methods : {
                showAllDetailTrigger:function(){
                    this.showDetail = !this.showDetail;
                    for(var o in this.orderList) {
                        this.orderList[o].showDetail = this.showDetail;
                    }
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
                            orderlist[o].ebayItemsList     = JSON.parse(orderlist[o].ebayItemsList);
                            // console.log(orderlist[o].ebayItemsList);
                            if(orderlist[o].paymentUpdate != null ) {
                                orderlist[o].paymentUpdate = JSON.parse(orderlist[o].paymentUpdate);
                            }
                            orderlist[o].disableMemoInput  = true;
                            orderlist[o].disableMemoBtn    = true;
                            orderlist[o].showDetail        = false;
                            if(orderlist[o].id > this.setOrderId) {
                                this.setOrderId = orderlist[o].id;
                            }
                        }
                        if(this.isInit) {
                            var orders = JSON.parse(response.body);
                            if(orders.length > 0) {
                                for(var o in orders) {
                                    orders[o].ebayItemsList     = JSON.parse(orders[o].ebayItemsList);
                                    if(orders[o].paymentUpdate != null ) {
                                        orders[o].paymentUpdate = JSON.parse( orders[o].paymentUpdate);
                                    }
                                    orders[o].disableMemoInput  = true;
                                    orders[o].disableMemoBtn    = true;
                                    orders[o].showDetail        = false;

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
                            // console.log(orderlist);
                            this.orderList = orderlist;
                        }

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