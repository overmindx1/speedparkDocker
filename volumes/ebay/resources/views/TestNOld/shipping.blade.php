@extends('mainbase')

@section('dataContainer')

    @verbatim
    <div class="panel panel-default dataContainer">
        <!-- Default panel contents -->
        <div class="panel-heading">
            輸入TrackingNumber |
            紀錄送貨單號 :<input type="number" class="recordInput" v-model="recordId"  >
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
            <button type="button"  @click="getOrderInfo()" class="btn btn-default">送出查詢</button> |

        </form>

        <div>
            <input-shipping
                    :order-list="orderList"
                    :tracking-number="trackingNumber"
                    :shipping-date="shippingDate"
                    :shipping-charge="shippingCharge"
                    :is-shipped="isShipped"
                    :show-update-btn="showUpdateBtn"
                    :ignore-update="ignoreUpdate"
                    :error-message = "errorMessage"
            ></input-shipping>
        </div>
    </div>

    <template id="inputShipping">
        <div clsaa="trackingNumberBlock" style="">
            <table class="table table-hover table-condensed table-bordered" v-if="orderList.length">
                <thead>
                    <tr class="bg-success">
                        <td>#</td>
                        <td>send</td>
                        <td>ignoreUpdate</td>
                        <td>TrackingNumber</td>
                        <td>ShippingDate</td>
                        <td>ShipCharge</td>
                        <td>Error Message</td>
                    </tr>
                </thead>
                <tbody v-for="order in orderList">
                    <tr :class="{'bg-danger' : order.ignoreUpdate}">
                        <td>{{order.id}} </td>
                        <td>
                            <span v-if="isShipped[order.id]" class="glyphicon glyphicon-plane" aria-hidden="true"></span>
                            <span v-else class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                        </td>
                        <td>
                            <input type="checkbox"  v-model="ignoreUpdate[order.id]"    class="form-control ">
                        </td>
                        <td>
                            <input type="text"      v-model="trackingNumber[order.id]"  class="form-control form-group-sm" placeholder="輸入追蹤碼">
                        </td>
                        <td>
                            <input type="date"      v-model="shippingDate[order.id]"    class="form-control form-group-sm" placeholder="輸入日期">
                        </td>
                        <td>
                            <input type="number"    v-model="shippingCharge[order.id]"  class="form-control form-group-sm" placeholder="輸入費用">
                        </td>
                        <td>
                            {{errorMessage[order.id]}}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-info">
                        <td colspan="6"></td>
                        <td colspan="1">
                            <button
                                    class="btn btn-default btn-xs"
                                    type="button"
                                    @click="sendShipData()"
                                    :disabled="updateBtn"
                                    v-if="showUpdateBtn">
                                    {{updateBtnText}}
                            </button>

                        </td>
                    </tr>
                </tfoot>
            </table>
            <!--<div class="list-group" v-for="order in orderList">
                <form class="form-inline padding10" >
                    訂單 : #{{order.id}}  | <span  :class="isShipped[order.id] ? 'label label-success' : 'label label-warning'">{{ isShipped[order.id] ? '以經輸入過' : '尚未輸入過'}}</span>
                    <div class="form-group">
                        <label for="">TrackingNumber:</label>
                        <input type="text" v-model="trackingNumber[order.id]" class="form-control" placeholder="輸入追蹤碼">
                    </div>
                    <div class="form-group">
                        <label for="">ShippingDate:</label>
                        <input type="date" v-model="shippingDate[order.id]" class="form-control" placeholder="輸入日期">
                    </div>
                    <div class="form-group">
                        <label for="">ShipCharge:</label>
                        <input type="number" v-model="shippingCharge[order.id]" class="form-control" placeholder="輸入費用">
                    </div>
                </form>
            </div>-->

        </div>
    </template>
    @endverbatim




    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>

    <script>
        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        Vue.component('input-shipping', {
            template: '#inputShipping',
            props: ['orderList' , 'trackingNumber' , 'shippingDate' , 'shippingCharge' , 'isShipped' , 'showUpdateBtn','ignoreUpdate' ,'errorMessage' ],
            data : function (){
                return {
                    updateBtn       : false,
                    updateBtnText   : "送出更新",
                }
            },
            methods : {

                sendShipData : function(){
                    var url="/v1/updateOrderShippingData";
                    for(var t in this.trackingNumber) { //檢查看看有沒有不要上傳卻沒打勾的
                        if(this.trackingNumber[t] == '') {
                            if(!this.ignoreUpdate[t]) {
                                alert('單號:' + t + ' 尚未輸入TrackingNumber !!!')
                                return;
                            }
                        }
                    }

                    this.updateBtn = true;
                    this.updateBtnText = "更新中...";
                    var updateTrackingNumber = {};
                    var updateShippingDate = {};
                    var updateShippingCharge = {};

                    for(var t in this.ignoreUpdate) { //找出需要上傳的單號
                        if(!this.ignoreUpdate[t]) {
                            //console.log(t);
                            updateTrackingNumber[t] = this.trackingNumber[t];
                            updateShippingDate[t]   = this.shippingDate[t];
                            updateShippingCharge[t] = this.shippingCharge[t];
                        }
                    }

                    var body = {
                        trackingNumber  : updateTrackingNumber,
                        shippingDate    : updateShippingDate,
                        shippingCharge  : updateShippingCharge
                    };
                    this.$http.post(url , body).then(function(response){
                        var errorMsg = '';
                        this.updateBtn = false;
                        this.updateBtnText = "送出更新";
                        //console.log(response.body);
                        var orders = JSON.parse(response.body);
                        for(var o in orders) {
                            if(orders[o] != true) {
                                console.log(orders[o]);
                                errorMsg += '單號:' + o + '處理異常 ! ===';
                            }
                        }

                        if(errorMsg != '') {
                            alert(errorMsg);
                        } else {
                            alert('訂單TrackingNumber新增完畢!');
                        }
                    });
                }
            }
        });

        new Vue({
            el: '.dataContainer',
            data: {
                startOrder: 0,          //開始定單
                endOrder: 0,            //結束定單
                trackingNumber : {},    //tracking number object
                shippingDate: {},       //shippingDate  object
                shippingCharge:{},      //shippingCharge  object
                errorMessage:{},        //判定有沒有Error Message
                isShipped:{},           //判斷是不是已運送
                orderList:[],           //定單列表
                showUpdateBtn:false,
                recordBtnText: '更新',
                recordId : 0,
                ignoreUpdate : {}
            },
            methods: {
                getOrderInfo : function() {
                    var url ='/v1/getOrderDetailForProcess';
                    var body = {
                        startOrder  : this.startOrder,
                        endOrder    : this.endOrder,
                        showImage   : true
                    };
                    this.$http.post(url , body).then( function(response) {
                        var object = JSON.parse(response.body);
                        this.trackingNumber = {};
                        this.shippingDate={};
                        this.shippingCharge={};
                        this.isShipped={};
                        this.ignoreUpdate = {};
                        for( var o in object) {
                            this.ignoreUpdate[object[o].id] = false;
                            if(object[o].has_ship != null) {
                                this.trackingNumber[object[o].id]   = object[o].has_ship.trackingNumber;
                                this.shippingDate[object[o].id]     = object[o].has_ship.shippingDate;
                                this.shippingCharge[object[o].id]   = object[o].has_ship.shippingCharge;
                                this.errorMessage[object[o].id] = object[o].has_ship.errorMsg;
                                this.isShipped[object[o].id]        = true;

                            } else {
                                var date = new Date();
                                this.trackingNumber[object[o].id] = '';
                                this.shippingDate[object[o].id]   = date.toISOString().slice(0,10).replace(/-/g,"-");
                                this.shippingCharge[object[o].id] = 0;
                                this.errorMessage[object[o].id] = '';
                                this.isShipped[object[o].id]      = false;
                            }
                        }
                        this.orderList = object;
                        this.showUpdateBtn = (this.orderList.length);
                    });
                },
                updateRecord :function() {
                    this.recordBtnText = '處理中...';
                    var url = "/v1/updateRecord";
                    var body = { type : "tracking" , recordId : this.recordId};
                    this.$http.post(url , body).then( function(response) {
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
                var body = { type : "tracking"};
                this.$http.post(url , body).then( function(response) {
                    var data = JSON.parse(response.body);
                    this.recordId = data.record;

                });
            }
        });
    </script>
@endsection