@extends('mainbase')

@section('dataContainer')
@verbatim
    <div class="panel panel-default listDataTable" xmlns="http://www.w3.org/1999/html">
        <div class="loader loader-default" :class="{ 'is-active' : isLoading }" data-text></div>
        <!-- Default panel contents -->
        <div class="panel-heading">
           國家訂單趨勢圖表(Country Order Count Chart Report) | Detail 
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
            <button type="button" @click="getCountryData()">Send</button>
        </form>
        <div class="panel-footer">
            Select Sellers :
            <span v-for="(seller, index) in sellers" style="margin: 0 5px 0 5px;">
                <input type="checkbox" :id="seller" :value="seller" v-model="selectedSeller" >
                <label :for="seller" style="vertical-align: top">{{seller}}</label>
            </span>
        </div>
        <div v-if="chartData.rows.length > 0">
            Date Range : {{startDate}} ~ {{endDate}} | Total Order Count : {{totalCount}}
            
            <table class="table table-bordered" style="width:100%;text-align: center; vertical-align: middle">
                <thead>                
                    <tr style="background:#dedede">
                        <td >Country</td>
                        <td >Count</td>                    
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(country , key) in countryList" :key="key">
                        <td >{{country.Country}}</td>
                        <td >{{country.Count}}</td>                    
                    </tr>
                </tbody>            
            </table>
            <ve-bar :data="chartData"  :height="chartHeight"></ve-bar>
        </div>
        
        
    </div>
@endverbatim   
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/v-charts/lib/index.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/v-charts/lib/style.min.css">
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> --}}
<script>    
"use strict";
        Vue.config.debug = true;
        Vue.config.devtools = true;
new Vue({
    el : '.listDataTable',
    // components: { VeBar },
    data : {
        isLoading: false,   //Ajax Loading
        startDate : '',
        endDate : '',
        sellers : sellers,
        selectedSeller : sellers,
        countryList : [],
        totalCount : 0,
        chartData : {
            columns: ['Country', 'Count'],
            rows: []
        }      
    },
    computed :{
        chartHeight(){
            let height = this.chartData.rows.length * 24;
            return `${height}px`; 
        }
    },
    methods : {
        getCountryData(){
            const payload = {
                startDate : this.startDate,
                endDate : this.endDate,
                sellers : this.selectedSeller,
            }
            this.isLoading = true;
            fetch('/v1/getBuyCountryCountByDateRange' , {
                body: JSON.stringify(payload), // must match 'Content-Type' header
                cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached                
                headers: {                
                    'content-type': 'application/json'
                },
                method: 'POST', // *GET, POST, PUT, DELETE, etc.
                mode: 'cors', // no-cors, cors, *same-origin               
            }).then(resopnse => resopnse.json()).then( data => {                
                data.sort((a,b) => {
                    return a.Count - b.Count;
                }); 
                this.chartData.rows = data;               
                let countryList = JSON.parse(JSON.stringify(data));
                countryList.sort((a,b) => {
                    return b.Count - a.Count;
                }); 
                this.countryList = countryList;
                this.totalCount = 0;
                countryList.map( c => {
                    this.totalCount += c.Count;
                })
                this.isLoading = false;
            }).catch(err => {console.log(err); this.isLoading = true;})
            
        }
    },
    mounted(){        
        this.startDate = new Date().toISOString().slice(0,10).replace(/-/g,"-");
        this.endDate = new Date().toISOString().slice(0,10).replace(/-/g,"-");
        
    }
});
</script>
@endsection  