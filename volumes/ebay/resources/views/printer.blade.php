@extends('mainbase')

@section('dataContainer')
    @verbatim
    <div class="panel panel-default dataContainer">
        <!-- Ajax Loading -->
        <div class="loader loader-default" :class="{ 'is-active' : isLoading }" data-text></div>
        <!-- Default panel contents -->
        <div class="panel-heading">
            運送標簽列印 |
            Record Order Id :<input type="number" class="recordInput" v-model="recordId"  >
            <button type="button" class="btn btn-xs btn-success" @click="updateRecord()">{{recordBtnText}}</button>
        </div>
        <form class="form-inline padding10" >
            <div class="form-group">
                <label for="">Start Order:</label>
                <input type="number" v-model="startOrder" class="form-control orderNumber" placeholder="起始單號">
            </div>
            <div class="form-group">
                <label for="">End Order:</label>
                <input type="number" v-model="endOrder" class="form-control orderNumber" placeholder="結束單號">
            </div>
            <label>
                <input type="checkbox" v-model="sameAddressPrint"> Combinded Print
            </label>
            <button type="button" @click="openNewPrintWindows()" class="btn btn-default">New Window Print</button>
            <button type="button" @click="getAddressInfo()" class="btn btn-default">Submit</button> |
            <button type="button" @click="printOrderList()" class="btn btn-default">Print</button>
        </form>

        <div class="panel-footer">
            Select Sellers :
            <span v-for="(seller, index) in sellers" style="margin: 0 5px 0 5px;">
                <input type="checkbox" :id="seller" :value="seller" v-model="selectedSeller" >
                <label :for="seller" style="vertical-align: top">{{seller}}</label>
            </span>
        </div>

        <div>
            <printer-area :order-list="orderList" :store-info="storeInfo"></printer-area>
        </div>
    </div>

    <template id="printerArea">
        <div style="width: 11cm; height: auto" id="printAreaBlock">
            <div class="page" v-for="order in orderList">
                <div class="addressOrder" style="">#{{order.id}}</div>
                <div class="addressStore">
                    <div class="sendAndReceiverLabel storeHeight" style="">
                        FROM:
                    </div>
                    <div class="sendAndReceiverContent storeHeight">
                        {{storeInfo.name}}<br />
                        {{storeInfo.address}}
                        {{storeInfo.city}} {{storeInfo.country}} {{storeInfo.zip}}<br />
                        TEL: {{storeInfo.TEL}}
                    </div>
                </div>

                <div class="addressOrderClient">
                    <div class="sendAndReceiverLabel clientHeight" >
                        TO:
                    </div>
                    <div class="sendAndReceiverContent clientHeight" >
                        {{order.shippingRecipientName}} <br />
                        {{order.shippingAddressStreet}} <br />
                        {{order.shippingAddressCity}} , {{order.shippingAddressState}} <br />
                        {{order.shippingAddressZip}}<br />
                        {{order.shippingCountry}} <br />
                        TEL: {{order.payerPhone}}
                    </div>
                </div>
            </div>
        </div>
    </template>
    @endverbatim
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://underscorejs.org/underscore-min.js"></script>
    <script>
        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        Vue.component('printer-area', {
            template: '#printerArea',
            props: ['orderList' , 'storeInfo' ]
        });

        var vm = new Vue({
            el: '.dataContainer',
            data: {
                startOrder: 0,
                endOrder: 0,
                storeInfo : {},
                orderList: [],
                sellers : sellers,
                selectedSeller : sellers,
                sameAddressPrint : true, //是否合併訂單
                recordBtnText: '更新',
                recordId : 0,
                isLoading: false    //Ajax Loading
            },
            methods: {
                openNewPrintWindows : function(){
                    var isCombinded = (this.sameAddressPrint ? 1:0);
                    var url = `/printTemplate?startOrder=${this.startOrder}&endOrder=${this.endOrder}&sameAddressPrint=${isCombinded}`;
                    var win = window.open(url, '_blank');
                    if (win) {
                        //Browser has allowed it to be opened
                        win.focus();
                    } else {
                        //Browser has blocked it
                        alert('Please allow popups for this website');
                    }
                },
                getAddressInfo : function() {
                    this.isLoading = true;
                    var url ='/v1/getAddressInfoByIds';
                    var body = {
                        startOrder  : this.startOrder,
                        endOrder    : this.endOrder,
                        sellers     : this.selectedSeller
                    };
                    this.$http.post(url , body).then( function(response) {
                        var object = JSON.parse(response.body);
                        //console.log(object);
                        if(this.sameAddressPrint) { //如果要合併訂單列印
                            var group = _.groupBy(object.orderList , 'ebayBuyerId'); //找出相同帳號的訂單
                            var orders = {};
                            for(var i in group) {
                                if(group[i].length > 1)  {
                                    orders[group[i][0]['id']] = [];
                                    var searchObj = {
                                        "shippingCountry"       : group[i][0]['shippingCountry'],
                                        "shippingAddressZip"    : group[i][0]['shippingAddressZip'],
                                        "shippingAddressStreet" : group[i][0]['shippingAddressStreet'],
                                        "shippingAddressState"  : group[i][0]['shippingAddressState'],
                                        "shippingAddressCity"   : group[i][0]['shippingAddressCity']
                                    };
                                    var same = _.where(group[i] , searchObj); //找出相同運送的地址
                                    //console.log(same);
                                    for(var s in same) {
                                        orders[group[i][0]['id']].push(same[s]['id']); //相同地址的訂單號碼記錄起來
                                    }
                                }
                            }
                            var unprint = []; //記住不要列印的被合併的訂單
                            for(var o in object.orderList) {
                                for(var j in orders) {
                                    if(object.orderList[o].id == j) {
                                        var newid = '';
                                        for(var k in orders[j]) {
                                            if(newid != '') {
                                                unprint.push(orders[j][k]); // 記住不要被列印的單
                                            }
                                            newid += (newid == '' ? orders[j][k] : ','+orders[j][k]); //將相同地址的單 id 合併
                                        }
                                        object.orderList[o].id = newid; //新 id 寫入
                                    }
                                }
                            }
                            var newList = [];
                            for( var o in object.orderList) {
                                var isDelete = false;
                                for(var k in unprint) {
                                    if(object.orderList[o].id == unprint[k]) {//如果是不要被列印的單
                                        isDelete = true;
                                    }
                                }
                                if(!isDelete) { //如果是要列印的單 將 order 寫入新陣列
                                    newList.push(object.orderList[o]);
                                }
                            }
                            object.orderList = newList; //新的要被列印的陣列取代舊的陣列
                            this.orderList = object.orderList;
                            this.storeInfo = object.storeInfo;
                        } else {
                            this.orderList = object.orderList;
                            this.storeInfo = object.storeInfo;
                        }
                        this.isLoading = false;
                    });
                },
                printOrderList : function(){
                    var printContents = document.getElementById("printAreaBlock").innerHTML;
                    var originalContents = document.body.innerHTML;

                    document.body.innerHTML = printContents;

                    window.print();

                    document.body.innerHTML = originalContents;
                },
                updateRecord : function () {
                    this.recordBtnText = '處理中...';
                    var url = "/v1/updateRecord";
                    var body = { type : "shipping" , recordId : this.recordId};
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
                var body = { type : "shipping"};
                this.$http.post(url , body).then( function(response) {
                    var data = JSON.parse(response.body);
                    this.recordId = data.record;

                });
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
                    vm.getAddressInfo();
                }
            }
        });
    </script>

@endsection