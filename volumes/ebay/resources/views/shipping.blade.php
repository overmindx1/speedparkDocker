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
                <input type="number" v-model="startOrder" class="form-control orderNumber" placeholder="起始單號">
            </div>
            <div class="form-group">
                <label for="">結束單號:</label>
                <input type="number" v-model="endOrder" class="form-control orderNumber" placeholder="結束單號">
            </div>
            <button type="button"  @click="getOrderInfo()" class="btn btn-default">送出查詢</button> |

        </form>

        <div>
            <input-shipping
                    :order-list="orderList"
                    :tracking-number="trackingNumber"
                    :shipping-date="shippingDate"
                    :shipping-charge="shippingCharge"
                    :shipping-logistics="shippingLogistics"
                    :is-shipped="isShipped"
                    :show-update-btn="showUpdateBtn"
                    :error-message = "errorMessage"
                    :get-update="getUpdate"
            ></input-shipping>
        </div>
    </div>

    <template id="inputShipping">
        <div clsaa="trackingNumberBlock" >
            <table class="table table-hover table-condensed table-bordered" v-if="orderList.length">
                <thead>
                <tr class="bg-success">
                    <td>#</td>
                    <td>send</td>
                    <td>Update</td>
                    <td>TrackingNumber</td>
                    <td>ShippingDate</td>
                    <td>ShipCharge</td>
                    <td>shipLogistics</td>
                    <td>Error Message</td>
                </tr>
                </thead>
                <tbody v-for="(order , key) in orderList">
                <tr :class="{ 'bg-danger' : errorMessage[order.id] != ''}">
                    <td>
                        <b style="font-size: 18px">{{order.id}}</b>
                    </td>
                    <td>
                        <span v-if="isShipped[order.id]" class="glyphicon glyphicon-plane" aria-hidden="true"></span>
                        <span v-else class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                        <br />
                        <button type="button" class="btn btn-danger btn-xs" @click="deleteShippingRecord(order.id , key)"> X </button>
                    </td>
                    <td>
                        <input type="checkbox"  v-model="getUpdate[order.id]"    class="form-control ">
                    </td>
                    <td>
                        <input type="text"   @input="checkInput(order.id)"   v-model="trackingNumber[order.id]"  class="form-control form-group-sm inputNext" placeholder="輸入追蹤碼">
                    </td>
                    <td>
                        <input type="date"      v-model="shippingDate[order.id]"    class="form-control form-group-sm" placeholder="輸入日期">
                    </td>
                    <td>
                        <input type="number"    v-model="shippingCharge[order.id]"  class="form-control form-group-sm" placeholder="輸入費用">
                    </td>
                    <td>
                        <input type="text"    v-model="shippingLogistics[order.id]"  class="form-control form-group-sm" placeholder="輸入物流">
                    </td>
                    <td v-html="errorMessage[order.id]">
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr class="bg-info">
                    <td colspan="7"></td>
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




    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.3/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <script>
        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        Vue.component('input-shipping', {
            template: '#inputShipping',
            props: ['orderList' , 'trackingNumber' , 'shippingDate' , 'shippingCharge' , 'shippingLogistics' , 'isShipped' , 'showUpdateBtn' ,'errorMessage','getUpdate' ],
            data : function (){
                return {
                    updateBtn       : false,
                    updateBtnText   : "送出更新",
                }
            },
            methods : {
                deleteShippingRecord : function (orderId , arrayKey){
                    var con = confirm('確定刪除單號: '+orderId+' 的送貨紀錄嗎?');
                    if(con == true) {
                        var url = '/v1/deleteShipRecordByOrderId';
                        var data = { orderId : orderId};
                        this.$http.post(url , data).then(function(response) {
                            var status = JSON.parse(response.body).success;
                            if(status == true) {
                                alert('單號:' +orderId+ ' 的送貨紀錄已刪除!');
                                this.orderList.splice(arrayKey , 1); //刪除畫面上顯示的紀錄
                            } else {
                                alert('單號:' +orderId+ ' 的送貨紀錄刪除處理異常!');
                            }
                        });
                    }
                },
                checkInput : function(id){

                    var check = (this.trackingNumber[id] != '');

                    this.$set(this.getUpdate , id , check);

                    console.log(this.getUpdate[id]);
                },
                sendShipData : function(){
                    var url="/v1/updateOrderShippingData";

                    this.updateBtn = true;
                    this.updateBtnText = "更新中...";
                    var updateTrackingNumber = {};
                    var updateShippingDate = {};
                    var updateShippingCharge = {};
                    var updateShippingLogistics = {};

                    for(var t in this.getUpdate) { //找出需要上傳的單號
                        if(this.getUpdate[t]) {
                            //console.log(t);
                            updateTrackingNumber[t] = this.trackingNumber[t];
                            updateShippingDate[t]   = this.shippingDate[t];
                            updateShippingCharge[t] = this.shippingCharge[t];
                            updateShippingLogistics[t] = this.shippingLogistics[t];
                        }
                    }

                    var body = {
                        trackingNumber  : updateTrackingNumber,
                        shippingDate    : updateShippingDate,
                        shippingCharge  : updateShippingCharge,
                        shippingLogistics : updateShippingLogistics
                    };
                    //console.log(body);
                    //return;
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

        var vm = new Vue({
            el: '.dataContainer',
            data: {
                startOrder: 0,          //開始定單
                endOrder: 0,            //結束定單
                trackingNumber : {},    //tracking number object
                shippingDate: {},       //shippingDate  object
                shippingCharge:{},      //shippingCharge  object
                shippingLogistics:{},   //shippingLogistics Obj
                errorMessage:{},        //判定有沒有Error Message
                isShipped:{},           //判斷是不是已運送
                orderList:[],           //定單列表
                showUpdateBtn:false,
                recordBtnText: '更新',
                recordId : 0,
                getUpdate:{}
            },
            methods: {
                getOrderInfo : function() {
                    var url ='/v1/getOrderDetailForProcess';
                    var body = {
                        startOrder  : this.startOrder,
                        endOrder    : this.endOrder,
                        showImage   : true
                    };
                    var getUpdate = {};

                    this.$http.post(url , body).then( function(response) {
                        var object = JSON.parse(response.body);
                        this.trackingNumber = {};
                        this.shippingDate = {};
                        this.shippingCharge = {};
                        this.isShipped = {};

                        for( var o in object) {
                            getUpdate[object[o].id]=false;
                            object[o].getUpdate = false;
                            if(object[o].has_ship != null) {
                                this.trackingNumber[object[o].id]   = object[o].has_ship.trackingNumber;
                                this.shippingDate[object[o].id]     = object[o].has_ship.shippingDate;
                                this.shippingCharge[object[o].id]   = object[o].has_ship.shippingCharge;
                                this.shippingLogistics[object[o].id]   = object[o].has_ship.shippingLogistics;
                                this.errorMessage[object[o].id]     = object[o].has_ship.errorMsg;
                                this.isShipped[object[o].id]        = true;

                            } else {
                                var date = new Date();
                                this.trackingNumber[object[o].id] = '';
                                this.shippingDate[object[o].id]   = date.toISOString().slice(0,10).replace(/-/g,"-");
                                this.shippingCharge[object[o].id] = 0;
                                this.shippingLogistics[object[o].id] = '';
                                this.errorMessage[object[o].id] = '';
                                this.isShipped[object[o].id]      = false;
                            }
                        }
                        this.orderList = object;
                        this.getUpdate = getUpdate;
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
                },

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

        $('.dataContainer').on('keyup', '.inputNext', function(e) {
             if (e.which == 13) { //enter
                e.preventDefault();
                var inputs = $('.inputNext'); //找出所有
                inputs.eq( inputs.index(this)+ 1 ).focus(); //跳到下一個
            }
        });

        $('.form-inline').on('keyup' , '.orderNumber' , function(e){
            if (e.which == 13) { //enter
                e.preventDefault();
                var orderNumber = $('.orderNumber'); //找出所有
                console.table(orderNumber);
                if(orderNumber.index(this) != (orderNumber.length -1) ) {
                    orderNumber.eq(orderNumber.index(this) + 1 ).val('').focus();
                } else {
                    vm.getOrderInfo();
                }
            }
        });
    </script>
@endsection