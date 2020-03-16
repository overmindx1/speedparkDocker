@extends('mainbase')

@section('dataContainer')
    @verbatim
    <div class="panel panel-default dataContainer">
        <!-- Default panel contents -->
        <div class="panel-heading">
            紀錄清單 |
            <button type="button" class="btn btn-default btn-xs glyphicon glyphicon-chevron-left" aria-hidden="true" :disabled="prevDisable" @click="getDutyRecordList(prevPage)"></button>
            Page {{currentPage}} of {{totalPage}}
            <button type="button" class="btn btn-default btn-xs glyphicon glyphicon-chevron-right" aria-hidden="true" :disabled="nextDisable" @click="getDutyRecordList(nextPage)"></button>
            <button type="button" class="btn btn-info btn-xs" @click="showInsertForm">輸入表單</button>
            <!--<button type="button" class="btn btn-info btn-xs" @click="showAllDetailTrigger(false)">全部收起</button>-->

                <div  class="panel-body searchDetail" v-show="showInsert">
                    <form class="form-inline">
                        <div class="form-group">
                            Date : <input type="date" v-model="today" class="recordInput" readonly  />
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-condensed dutyRecordTable"  id="dutyRecordTable">
                        <thead>
                        <tr>
                            <td class="signBlock">#</td>
                            <td class="signBlock">Process </td>
                            <td class="signBlock">Print </td>
                            <td class="signBlock">Tracking Number </td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                Signature
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="processRecord.sign" />
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="printRecord.sign" />
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="trackingRecord.sign" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                OrderStart
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="processRecord.orderStart" />
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="printRecord.orderStart" />
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="trackingRecord.orderStart" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                OrderEnd
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="processRecord.orderEnd" />
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="printRecord.orderEnd" />
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="trackingRecord.orderEnd" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Note
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="processRecord.note" />
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="printRecord.note" />
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="trackingRecord.note" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" @click="insertNewRecord()" >Insert</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

        </div>


    <record-list :record-list="recordList" :show-detail="showDetail" :prev-day="prevDay"></record-list>

    </div>

    <template id="RecordList">
        <div>
            <div  class="panel-body searchDetail" v-for="(record , key) in recordList">
                <form class="form-inline">
                    <div class="form-group">
                        Date : <input type="date" v-model="record.date" class="recordInput" readonly /> |
                        <button type="button" class="btn btn-primary btn-sm"  @click="showDetailTrigger(key)">DetailTrigger</button>
                    </div>
                </form>
                <table class=" table table-striped table-bordered table-hover table-condensed" id="dutyRecordTable" v-show="showDetail[key]" valign="middle">
                    <thead>
                        <tr>
                            <td class="signBlock">#</td>
                            <td class="signBlock">Process </td>
                            <td class="signBlock">Print </td>
                            <td class="signBlock">Tracking Number </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                Signature
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="record.processRecord.sign" />
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="record.printRecord.sign" />
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="record.trackingRecord.sign" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                OrderStart
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="record.processRecord.orderStart" />
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="record.printRecord.orderStart" />
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="record.trackingRecord.orderStart" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                OrderEnd
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="record.processRecord.orderEnd" />
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="record.printRecord.orderEnd" />
                            </td>
                            <td>
                                <input type="number" class="recordInput" v-model="record.trackingRecord.orderEnd" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Note
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="record.processRecord.note" />
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="record.printRecord.note" />
                            </td>
                            <td>
                                <input type="text" class="recordInput" v-model="record.trackingRecord.note" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" >
                                Updated at : {{record.updated_at}}
                            </td>
                            <td><!--可以允許兩天內可以更新-->
                                <button type="button" class="btn btn-primary btn-sm" :disabled="prevDay　> record.date" @click="updateDutyRecord(key , record.id)">Update</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

        Vue.component('record-list' , {
            template    : '#RecordList',
            props       : ['recordList' , 'showDetail' , 'prevDay'],
            methods     : {
                showDetailTrigger : function(key) {
                    this.showDetail[key] = !this.showDetail[key];
                },
                updateDutyRecord : function (key , id) {
                    var object = {
                        id            : id,
                        date          : this.recordList[key].date,
                        processRecord : this.recordList[key].processRecord,
                        printRecord   : this.recordList[key].printRecord,
                        trackingRecord: this.recordList[key].trackingRecord,
                    };
                    var url = "/v1/updateDutyRecord";
                    this.$http.post(url , object).then(function(response) {
                        var data = JSON.parse(response.body);
                        if(data.success) {
                            alert('資料更新成功');
                        } else {
                            alert('資料更新失敗!'+data.message);
                        }
                    });
                }
            }
        });

        new Vue({
            el : '.dataContainer',
            data : {
                prevDisable : false,
                nextDisable : false,
                prevPage : 0,
                nextPage : 2,
                totalPage: 0,
                currentPage : 0,
                recordList : [],
                showDetail : [],
                today : new Date().toISOString().slice(0,10).replace(/-/g,"-"),
                prevDay : '',
                processRecord : {
                    sign:'' ,  orderStart:0 , orderEnd:0 , note : ''
                },
                printRecord : {
                    sign:'' ,  orderStart:0 , orderEnd:0 , note : ''
                },
                trackingRecord : {
                    sign:'' ,  orderStart:0 , orderEnd:0 , note : ''
                },
                showInsert : false
            },
            methods : {
                showInsertForm : function() {
                    this.showInsert = !this.showInsert;
                },
                insertNewRecord : function(){
                    var object = {
                        date          : this.today,
                        processRecord : this.processRecord,
                        printRecord   : this.printRecord,
                        trackingRecord: this.trackingRecord,
                    };
                    var url = "/v1/insertNewDutyRecord";
                    this.$http.post(url , object).then(function(response) {
                        var newRecord = JSON.parse(response.body);
                        if(!newRecord.success) {
                            alert(newRecord.message);
                            return;
                        }
                        newRecord.data.processRecord    = JSON.parse(newRecord.data.processRecord);
                        newRecord.data.printRecord      = JSON.parse(newRecord.data.printRecord);
                        newRecord.data.trackingRecord   = JSON.parse(newRecord.data.trackingRecord);
                        this.recordList.unshift(newRecord.data);
                    });
                },
                getDutyRecordList : function(page)  {
                    var url = '/v1/getDutyRecordByPage/'+page;
                    this.$http.get(url).then(function (reponse) {
                        var object = JSON.parse(reponse.body);
                        //console.log(object);
                        this.prevPage = parseInt(object.page) - 1;
                        this.nextPage = parseInt(object.page) + 1;
                        this.totalPage = object.totalPage;
                        this.currentPage = object.page;

                        this.prevDisable = (this.prevPage == 0 ? true : false);
                        this.nextDisable = (this.currentPage == this.totalPage ? true : false);

                        this.showDetail = {};

                        for(var r in object.recordList) {
                            Vue.set(this.showDetail , r , false);
                            object.recordList[r].processRecord  = JSON.parse(object.recordList[r].processRecord);
                            object.recordList[r].printRecord    = JSON.parse(object.recordList[r].printRecord);
                            object.recordList[r].trackingRecord = JSON.parse(object.recordList[r].trackingRecord);
                        }

                        this.recordList = object.recordList;
                    });
                }
            },
            created : function(){
                this.getDutyRecordList(1);
                var today = new Date();
                today.setDate(today.getDate() - 1);
                this.prevDay = today.toISOString().slice(0,10).replace(/-/g,"-");
                console.log(this.prevDay);
            }

        });

    </script>

@endsection