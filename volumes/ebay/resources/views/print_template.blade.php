<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css?{{time()}}">
    <title>Print Template</title>

</head>
<body style="margin: 0">
@verbatim

<div style="width: 11cm;" id="printAreaBlock">
    <div class="page"  v-for="order in orderList">
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

@endverbatim

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.js"></script>
<script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
<script src="http://underscorejs.org/underscore-min.js"></script>
<script>
    "use strict";
    Vue.config.debug = true;
    Vue.config.devtools = true;

    let printBlock = new Vue({
        el: '#printAreaBlock',
        data: {
            startOrder: 0,
            endOrder: 0,
            storeInfo : {},
            orderList: [],
            sameAddressPrint : true, //是否合併訂單
            recordBtnText: '更新',
            recordId : 0
        },
        methods:{
            getAddressInfo : function() {
                var url ='/v1/getAddressInfoByIds';
                var body = {
                    startOrder  : this.startOrder,
                    endOrder    : this.endOrder
                };
                this.$http.post(url , body).then( function(response) {
                    var object = JSON.parse(response.body);
                    //console.log(object);
                    if(this.sameAddressPrint) { //如果要合併訂單列印
                        var group = _.groupBy(object.orderList , 'paypalPayerMail'); //找出相同帳號的訂單
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
                });
            },
        },
        created : function() {
            var strUrl = location.search;
            var getPara, ParaVal;
            var aryPara = [];

            if (strUrl.indexOf("?") != -1) {
                var getSearch = strUrl.split("?");
                getPara = getSearch[1].split("&");

                //console.log(getSearch);
                for (var i = 0; i < getPara.length; i++) {
                    ParaVal = getPara[i].split("=");
                    console.log(ParaVal);
                    if(ParaVal[0] == 'startOrder') {
                        this.startOrder = parseInt(ParaVal[1]);
                    }
                    if(ParaVal[0] == 'endOrder') {
                        this.endOrder = parseInt(ParaVal[1]);
                    }
                    if(ParaVal[0] == 'sameAddressPrint') {
                        this.sameAddressPrint = (parseInt(ParaVal[1]) == 1 ? true : false) ;
                    }
                    //aryPara.push(ParaVal[0]);
                    //aryPara[ParaVal[0]] = ParaVal[1];
                }
                //console.log(aryPara);
            }
            this.getAddressInfo();
        }
    });
</script>

</body>
</html>