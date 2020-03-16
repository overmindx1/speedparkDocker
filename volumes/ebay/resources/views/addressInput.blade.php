<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Document</title>
</head>
<body>
<div class="app">
<table class="table table-hover table-bordered" style="width:100%">
    <tr>
        <td>OrderId</td>
        <td>shippingCountry</td>
        <td>shippingAddressState</td>
        <td>shippingAddressCity</td>
        <td>shippingAddressStreet</td>
        <td>shippingAddressZip</td>
        <td>shippingRecipientName</td>
        <td>Function</td>
    </tr>
    @verbatim
    <tr v-for="(order , id) in orderList">
        <td>{{order.id}}</td>
        <td>
            <input type="text" class="form-control"  v-model="order.shippingCountry">
        </td>
        <td>
            <input type="text" class="form-control"  v-model="order.shippingAddressState">
        </td>
        <td>
            <input type="text" class="form-control"  v-model="order.shippingAddressCity">
        </td>
        <td>
            <input type="text" class="form-control"  v-model="order.shippingAddressStreet">
        </td>
        <td>
            <input type="text" class="form-control"  v-model="order.shippingAddressZip">
        </td>
        <td>
            <input type="text" class="form-control"  v-model="order.shippingRecipientName">
        </td>
        <td>
            <button type="button" class="btn btn-primary" @click="updateAddress(id)">更新</button>
        </td>
    </tr>
    @endverbatim

</table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.js"></script>
<script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>

<script>

    "use strict";
    Vue.config.debug = true;
    Vue.config.devtools = true;

    var app = new Vue({
        el :'.app',
        data : {
            orderList : []
        },
        methods: {
            getData : function() {
                this.$http.get('/addressInputData' ).then(function(data) {
                    console.log(data);
                    this.orderList = JSON.parse(data.body);
                });
            },
            updateAddress : function(id) {
                this.$http.post('/updateAddress' , this.orderList[id]).then(function(data) {
                    alert('已更新')
                });

            }
        },
        created(){
            this.getData();
        }
    })

</script>

</body>
</html>