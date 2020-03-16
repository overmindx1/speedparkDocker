@extends('mainbase')

@section('dataContainer')

    @verbatim
    <div class="panel panel-default dataContainer">
        <!-- Default panel contents -->
        <div class="panel-heading">
            處理訂單撿貨 |
            紀錄檢貨單號 :<input type="number" class="recordInput" v-model="recordId"  >
            <button type="button" class="btn btn-xs btn-success" @click="updateRecord()">{{recordBtnText}}</button>
        </div>
        <form class="form-inline padding10" >
            <div class="form-group">
                <label for="">起始單號:</label>
                <input type="number" v-model="startOrder" class="form-control" placeholder="起始單號">
            </div>
            <div class="form-group">
                <label for="">結束單號:</label>
                <input type="number" v-model="endOrder" class="form-control" placeholder="結束單號">
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" v-model="showImage"> 顯示圖片 |
                </label>
            </div>
            <button type="button" @click="getOrderDetailForProcess()" class="btn btn-default">送出查詢</button>
        </form>
        <list-item :order-list="orderList" :show-image="showImage"  ></list-item>
    </div>


    <template id="orderDataList">
        <div>
            <div v-for="order in orderList" class="orderProcessBlock">
                <div class="bg-success padding10 process-orderHead" >
                    <span ># {{order.id}}</span>
                    <div class="processOrderAccount" >
                        <span>eBay賣家: {{order.ebaySeller}}</span> |
                        <span>eBay買家 :
                            <a target="_blank" :href="'http://cgi6.ebay.com/ws/eBayISAPI.dll?ViewBidItems&all=1&_rdc=1&userid=' +order.ebayBuyerId+ '&&rows=25&completed=1&sort=3&guest=1'">
                                {{order.ebayBuyerId}}
                            </a>
                        </span>
                    </div>
                </div>
                <div class="row page-header">
                    <div v-if="order.paypalPayerMemo != null">
                        <div class="col-sm-3 col-md-2">買家備註:</div>
                        <div class="col-sm-9 col-md-10" >
                            {{order.paypalPayerMemo}}
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-2">賣家備註:</div>
                    <div class="col-sm-9 col-md-10" >
                        <div class="input-group">
                            <input class="form-control" v-model="order.selfMemo"  :disabled="order.disableMemoInput" aria-label="Text input with multiple buttons">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default" @click="canWriteSelfMemo(order.id)"  aria-label="Help">
                                <span class="glyphicon glyphicon-pencil"></span>
                                </button>
                                <button type="button" class="btn btn-default" @click="updateOrderSelfMemo(order.id , order.selfMemo)" :disabled="order.disableMemoBtn" >更新</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row orderItemsBlack" v-for="item in order.ebayItemsList.itemsList">
                    <div class="col-md-2">
                        <div  class="thumbnail">
                           <img  :src="(showImage ? order.imagePath[item.itemId][0] : '' )" />
                        </div>
                    </div>

                    <div class="col-md-9 itemDescBlock">
                        <div class="row ">
                            <div class="col-md-12 ">
                                <a target="_blank" :href="'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&rd=1&item=' + item.itemId + '&ssPageName=STRK:MESE:IT'">
                                    {{item.itemName}}
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{item.itemPrice}} <span v-if="order.shippingFee != 0"> - ShippingFee : {{order.shippingFee}} |{{order.CurrencyCode}}</span>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-12" :style="(item.quantity > 1 ? {color: 'red'} : {}) ">
                               x {{item.quantity}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    @endverbatim

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
    <!--<script src="https://cdn.bootcss.com/vue-strap/1.1.29/vue-strap.js"></script>-->

    <script>

        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        Vue.component('list-item', {
            template: '#orderDataList',
            props: ['orderList' , 'showImage' ],
            data : function() {
              return {
                  processTypeStatus : processTypeStatus
              }
            },
            methods: {
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
                }
            }
        });

        new Vue({
            el:'.dataContainer',
            data : {
                startOrder  : 0,
                endOrder    : 0,
                showImage   : true,
                orderList   : [],
                recordId    : 0, //紀錄檢貨的id
                recordBtnText : '更新'
            },
            methods : {
                getOrderDetailForProcess : function () {
                    var url ='/v1/getOrderDetailForProcess';
                    var body = {
                        startOrder  : this.startOrder,
                        endOrder    : this.endOrder,
                        showImage   : this.showImage
                    };
                    this.$http.post(url , body).then( function(response) {
                        var orderList = JSON.parse(response.body);
                        for(var o in orderList) {
                            //o.itemsDetail = this.listOrderItem(o.ebayItemsList);
                            orderList[o].ebayItemsList = JSON.parse(orderList[o].ebayItemsList);
                            orderList[o].disableMemoInput = true;
                            orderList[o].disableMemoBtn   = true;
                            //this.processStatus[o.id] = o.ProcessStatus; //處理狀態
                        }

                        this.orderList = orderList;
                        console.log(this.orderList);
                    });
                },
                updateRecord : function() {
                    this.recordBtnText = '處理中...';
                    var url = "/v1/updateRecord";
                    var body = { type : "process" , recordId : this.recordId};
                    this.$http.post(url , body).then( function(response){
                        var data = JSON.parse(response.body);
                        if(!data.complete) {
                            alert("更新失敗!請在試試!");
                        }
                        this.recordBtnText = '更新';
                    });
                }
            },
            created : function() {
                var url = "/v1/getRecord";
                var body = { type : "process"};
                this.$http.post(url , body).then( function(response)  {
                    var data = JSON.parse(response.body);
                    this.recordId = data.record;

                });
            }
        });

    </script>


@endsection