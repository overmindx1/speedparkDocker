@extends('mainbase')

@section('dataContainer')
    @verbatim
    <div class="panel panel-default dataContainer">
        <!-- Default panel contents -->
        <div class="panel-heading">
            更新單號TxnId (只有無法上傳的Tracking Number的單適用)
        </div>
        <form class="form-inline padding10" >
            <div class="form-group">
                <label for="">Order ID:</label>
                <input type="text" v-model="orderId" class="form-control" placeholder="Key Word">
            </div>

            <button type="button" @click="getUpdateTxnId()" class="btn btn-default">送出查詢</button>
        </form>

        <div v-if="updateData != ''">
            <table class="table table-bordered table-condensed">
                <tr>
                    <td>Original Data</td>
                    <td>Updated Data</td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group" >
                            <li class="list-group-item" v-for="item in updateData.origin.itemsList">
                                <div>Name:{{item.itemName}}</div>
                                <div>itemId:{{item.itemId}}</div>
                                <div>txnId:{{item.txnId}}</div>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="list-group" >
                            <li class="list-group-item" v-for="item in updateData.new.itemsList">
                                <div>Name:{{item.itemName}}</div>
                                <div>itemId:{{item.itemId}}</div>
                                <div>txnId:<b style="color: #BE5C00">{{item.txnId}}</b></div>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <div class="alert alert-danger" role="alert" v-show="errorMsg != ''" style="width:90%;margin: 0 auto 10px auto; ">{{errorMsg}}</div>



    </div>
    @endverbatim


    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>

    <script>

        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        Vue.component('list-item', {

        });

        new Vue({
            el : '.dataContainer',
            data : {
                orderId     : 0,
                errorMsg    : '',
                updateData  : ''
            },
            methods : {
                getUpdateTxnId : function () {
                    this.errorMsg = '';
                    var url ='/v1/updateEbayItemTxnIdByOrderId';
                    var body = {
                        orderId  : this.orderId,
                    };
                    this.$http.post(url , body).then( function(response) {
                        //console.log(response);
                        var Object = JSON.parse(response.body);
                        if(Object.success) {
                            this.updateData = Object.data;
                        } else {
                            this.errorMsg   = Object.message;
                        }
                        console.log(Object);
                    });
                }
            }
        });
    </script>

@endsection