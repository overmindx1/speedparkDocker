@extends('mainbase')

@section('dataContainer')
    @verbatim
    <div class="panel panel-default dataContainer">
        <!-- Default panel contents -->
        <div class="panel-heading">
            訂單搜尋
        </div>
        <form class="form-inline padding10" >
            <div class="form-group">
                <label for="">搜尋方式:</label>
                <select class="selectStyle" v-model="searchType" >
                    <option v-for="opt in orderSearchOpts" :value="opt.key">{{opt.text}}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="">Key Word:</label>
                <input type="text" v-model="keyword" class="form-control" placeholder="Key Word">
            </div>
            <div class="form-group">
                <label for="">Start Date:</label>
                <input type="date" v-model="startDate" class="form-control"  placeholder="Key Word">
            </div>
            <div class="form-group">
                <label for="">End Date:</label>
                <input type="date" v-model="endDate" class="form-control" placeholder="Key Word">
            </div>
            <button type="button" @click="getSearch()" class="btn btn-default">送出查詢</button>
        </form>


        <list-item :order-list="orderList"></list-item>

    </div>


    <template id="orderList">
        <div>
        <div class="panel panel-primary" v-for="order in orderList">
            <div class="panel-heading">
                <h3 class="panel-title">訂單列表:#{{order.id}}</h3>
            </div>
            <div class="panel-body searchDetail">
                <div class="page-header">
                    <h1>賣家資訊</h1>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">ebay帳號:</div>
                    <div class="col-sm-8 col-md-10">{{order.ebaySeller}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal帳號:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalSellerMail}}</div>
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
                <div class="page-header">
                    <h1>交易資訊</h1>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">交易時間(台灣):</div>
                    <div class="col-sm-8 col-md-10">{{order.paymentDate}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">交易時間(Globe):</div>
                    <div class="col-sm-8 col-md-10">{{order.paymentDatePDT}}</div>
                </div>
                <!--<div class="row">
                    <div class="col-sm-4 col-md-2">交易認證:</div>
                    <div class="col-sm-8 col-md-10">{{order.verifyStatus}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">交易狀態:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPaymentStatus}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">交易保護:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalProtectionEligibility}}</div>
                </div>-->
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal Payment Type:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPaymentType}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal 交易模式:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalTxnType}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal Transaction Id:</div>
                    <div class="col-sm-8 col-md-10">
                        <a :href="'https://www.paypal.com/us/vst/id=' +  order.paypalTxnId" target="_blank">{{order.paypalTxnId}}</a>
                    </div>
                </div>
                <!--<div class="row">
                    <div class="col-sm-4 col-md-2">Paypal Payer Status:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPayerStatus}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal Payer Id:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPayerId}}</div>
                </div>-->
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal Payer Mail:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPayerMail}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal 交易者名字:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPayerFirstName}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal 交易者姓氏:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPayerLastName}}</div>
                </div>
                <!--<div class="row">
                    <div class="col-sm-4 col-md-2">Paypal 地址確認:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPayerAddressConfirmed}}</div>
                </div>-->
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal 交易者備註:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalPayerMemo}}</div>
                </div>

                <div class="page-header">
                    <h1>eBay資訊</h1>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">eBay 帳號:</div>
                    <div class="col-sm-8 col-md-10">
                        <a target="_blank" :href="'http://cgi6.ebay.com/ws/eBayISAPI.dll?ViewBidItems&all=1&_rdc=1&userid=' +order.ebayBuyerId+ '&&rows=25&completed=1&sort=3&guest=1'">
                            {{order.ebayBuyerId}}
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">eBay 購買物品:</div>
                    <div class="col-sm-8 col-md-10">
                        <div class="list-group ">
                            <div class="row" v-for="item in order.ebayItemsList.itemsList">
                                <div class="col-md-2">
                                    <div  class="thumbnail">
                                        <img  :src="order.imagePath[item.itemId][0]" />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-2">
                                            品名 :
                                        </div>
                                        <div class="col-md-10">
                                            {{item.itemName}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            價格 :
                                        </div>
                                        <div class="col-md-10">
                                            {{item.itemPrice}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            數量 :
                                        </div>
                                        <div class="col-md-10">
                                            {{item.quantity}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            Item Id :
                                        </div>
                                        <div class="col-md-10">
                                            <a :href="'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&rd=1&item=' + item.itemId +'&ssPageName=STRK:MESE:IT'" target="_blank">{{item.itemId}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-header">
                    <h1>送貨資料</h1>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">送貨資訊:</div>
                    <div class="col-sm-8 col-md-10" v-html="order.addressDetail"></div>
                </div>
                <!--<div class="row">
                    <div class="col-sm-4 col-md-2">寄送省州:</div>
                    <div class="col-sm-8 col-md-10">{{order.shippingAddressState}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">寄送城市:</div>
                    <div class="col-sm-8 col-md-10">{{order.shippingAddressCity}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">寄送街道地址:</div>
                    <div class="col-sm-8 col-md-10">{{order.shippingAddressStreet}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">寄送郵遞區號:</div>
                    <div class="col-sm-8 col-md-10">{{order.shippingAddressZip}}</div>
                </div>-->
                <div class="row">
                    <div class="col-sm-4 col-md-2">TrackingNumber:</div>
                    <div class="col-sm-8 col-md-10">
                        {{(order.has_ship ? order.has_ship.trackingNumber : '尚未輸入')}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">運送方式:</div>
                    <div class="col-sm-8 col-md-10">{{order.shippingMethod}}</div>
                </div>
                <div class="page-header">
                    <h1>相關費用</h1>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">付款全額:</div>
                    <div class="col-sm-8 col-md-10">{{order.totalPayment}} | {{order.CurrencyCode}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">Paypal 手續費:</div>
                    <div class="col-sm-8 col-md-10">{{order.paypalFee}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">貨運費用:</div>
                    <div class="col-sm-8 col-md-10">{{order.shippingFee}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">稅金:</div>
                    <div class="col-sm-8 col-md-10">{{order.Tax}}</div>
                </div>
                <div class="page-header">
                    <h1>其他資訊</h1>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">建立時間:</div>
                    <div class="col-sm-8 col-md-10">{{order.created_at}}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">更新時間:</div>
                    <div class="col-sm-8 col-md-10">{{order.updated_at}}</div>
                </div>
            </div>
        </div>
        </div>
    </template>
    @endverbatim


    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>

    <script>

        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        Vue.component('list-item', {
            template: '#orderList',
            props: ['orderList'],
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

            }
        });

        new Vue({
            el : '.dataContainer',
            data : {
                keyword : '',
                searchType : 1,
                orderSearchOpts : orderSearchOpt,
                orderList : [],
                startDate : '',
                endDate : new Date().toLocaleDateString()
            },
            methods : {

                getSearch : function () {
                    var url ='/v1/getOrderBySearch';
                    var body = {
                        searchType  : this.searchType,
                        keyword     : this.keyword,
                        startDate   : this.startDate,
                        endDate     : this.endDate
                    };
                    this.$http.post(url , body).then( function(response) {
                        var object = JSON.parse(response.body);
                        for(var o in object) {
                            object[o].ebayItemsList = JSON.parse(object[o].ebayItemsList);
                            object[o].addressDetail = this.formatAddress(object[o]);
                            object[o].disableMemoInput = true;
                            object[o].disableMemoBtn   = true;
                        }
                        console.log(object);
                        this.orderList = object;

                    });
                },
                formatAddress : function(obj) { //排列地址資料
                    var address  = obj.shippingRecipientName + '<br />';
                    address     += obj.shippingAddressStreet + '<br />';
                    address     += obj.shippingAddressCity +' '+ obj.shippingAddressState +' '+ obj.shippingAddressZip + '<br />';
                    address     += obj.shippingCountry + '<br />';
                    address     += 'TEL:' + obj.payerPhone;
                    return address;
                }
            },
            created: function () {
                //myDate.getMonth() + 1) + "-" + myDate.getDate() + "-" + myDate.getFullYear()
                var date = new Date();
                this.endDate = date.toISOString().slice(0,10).replace(/-/g,"-");
                date.setMonth(date.getMonth()-1);
                this.startDate = date.toISOString().slice(0,10).replace(/-/g,"-");
            }
        });


    </script>

@endsection