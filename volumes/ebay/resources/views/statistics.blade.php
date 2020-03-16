@extends('mainbase')

@section('dataContainer')
    @verbatim
    <div class="panel panel-default dataContainer">
        <!-- Ajax Loading -->
        <div class="loader loader-default" :class="{ 'is-active' : isLoading }" data-text></div>
        <!-- Default panel contents -->
        <div class="panel-heading">
            Statistics
        </div>
        <form class="form-inline padding10" >
            <div class="form-group">
                <label for="">StartDate :</label>
                <input type="date" v-model="startDate" class="form-control orderNumber" placeholder="Date">
            </div>
            <div class="form-group">
                <label for="">EndDate :</label>
                <input type="date" v-model="endDate" class="form-control orderNumber" placeholder="Date">
            </div>
            <button type="button" @click="getStatistics()">Send</button>
        </form>

        <div>
            <statistics-block :data="data"></statistics-block>
        </div>
    </div>

    <template id="statisticsBlock">
        <div v-show="data">
            <table class="table table-bordered" style="width:75%;text-align: center; vertical-align: middle">
                <thead>
                    <tr>
                        <td rowspan="2">
                            Date
                        </td>
                        <td colspan="2">Orders</td>
                        <td >TrackNumbers</td>
                    </tr>
                    <tr>
                        <td >Order Range</td>
                        <td >That Day Orders</td>
                        <td >That Day Upload</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="day in data.data">
                        <td >{{day.date | getWeekday}}</td>
                        <td >{{day.order.firstId}} ~ {{day.order.lastId}}</td>
                        <td >{{day.order.count}}</td>
                        <td >{{day.trackingNumber}}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-info">
                        <td colspan="2">
                            共計:
                        </td>
                        <td >{{data.countOrder}}</td>
                        <td >{{data.countUpload}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </template>
    @endverbatim
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.2.0/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://underscorejs.org/underscore-min.js"></script>
    <script>
        "use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;

        Vue.component('statistics-block', {
            template: '#statisticsBlock',
            props: [ 'data' ],
			filters:{
				getWeekday : function(date) {
					console.log(date);
					var day = new Date(date).getDay();
					switch (day) {
					  case  0:
						return date + '(Sun)';
						break;
					  case  1:
						return date + '(Mon)';
						break;
					  case  2:
						return date + '(Tue)';
						break;
					  case  3:
						return date + '(Wed)';
						break;
					  case  4:
						return date + '(Thu)';
						break;
					  case  5:
						return date + '(Fri)';
						break;
					  case  6:
						return date + '(Sat)';
						break;
					}
				}
			}
        });

        var vm = new Vue({
            el: '.dataContainer',
            data: {
                startDate: '',
                endDate: '',
                data: false,
                isLoading: false    //Ajax Loading
            },
			
            methods: {
                getStatistics : function (){
                    this.isLoading = true;
                    var url = "/v1/getStatisticsByDate";
                    var body = {
                        startDate   : this.startDate,
                        endDate     : this.endDate
                    };
                    this.$http.post(url , body).then(function(response) {
                        this.data = JSON.parse(response.body);
                        this.isLoading = false;
                    });
                }
            },
            created : function() {
                this.startDate = new Date().toISOString().slice(0,10).replace(/-/g,"-");
                this.endDate = new Date().toISOString().slice(0,10).replace(/-/g,"-");
            }
        });


    </script>

@endsection