<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
</head>
<style type="text/css">
    body{
        font-family: 'Roboto Condensed', sans-serif;
    }
    .m-0{
        margin: 0px;
    }
    .p-0{
        padding: 0px;
    }
    .pt-5{
        padding-top:5px;
    }
    .mt-10{
        margin-top:10px;
    }
    .text-center{
        text-align:center !important;
    }
    .w-100{
        width: 100%;
    }
    .w-50{
        width:50%;   
    }
    .w-85{
        width:85%;   
    }
    .w-15{
        width:15%;   
    }
    .logo img{
        width:200px;
        height:60px;        
    }
    .gray-color{
        color:#5D5D5D;
    }
    .text-bold{
        font-weight: bold;
    }
    .border{
        border:1px solid black;
    }
    table tr,th,td{
        border: 1px solid #d2d2d2;
        border-collapse:collapse;
        padding:7px 8px;
    }
    table tr th{
        background: #F4F4F4;
        font-size:15px;
    }
    table tr td{
        font-size:13px;
    }
    table{
        border-collapse:collapse;
    }
    .box-text p{
        line-height:10px;
    }
    .float-left{
        float:left;
    }
    .total-part{
        font-size:16px;
        line-height:12px;
    }
    .total-right p{
        padding-right:20px;
    }
    .use-name{
        color: #ff0000;
    }
</style>
<body>
<div class="head-title">
    <h1 class="text-center m-0 p-0">Invoice</h1>
</div>
<div class="add-detail mt-10">
    <div class="w-50 float-left mt-10">
        <p class="m-0 pt-5">Invoice Renter</p>
        <p class="m-0 pt-5 text-bold use-name">{{!empty($rentItem->users) ? $rentItem->users->full_name : ''}}</p>
        <p class="m-0 pt-5">{{!empty($rentItem->rentAddress) ? $rentItem->rentAddress->address : ''}}</p>
        <p class="m-0 pt-5 text-bold">{{!empty($rentItem->users) ? $rentItem->users->mobile : ''}}</p>
    </div>
    <div style="clear: both;"></div>
</div>
{{-- rentItem --}}
<div class="table-section bill-tbl w-100 mt-10">
    <table class="table w-100 mt-10">
        <thead class="thead-dark">
            <tr>
                <th class="w-20">No</th>
                <th class="w-70">Product Description</th>
                <th class="w-10">Start & End Date</th>
            </tr>
        </thead>
        <tbody>
            <tr align="center">
                <td>{{!empty($rentItem->id) ? $rentItem->id : ''}}</td>
                <td>{{!empty($rentItem->ads) ? $rentItem->ads->title : ''}}</td>
                @php
                $startEndDate = '';
                    if($rentItem->start && $rentItem->end){
                        $startEndDate = \Carbon\Carbon::parse($rentItem->start)->format('d M') .' To '.\Carbon\Carbon::parse($rentItem->end)->format('d M Y');
                    }
                @endphp
                <td>{{ $startEndDate }}</td>
            </tr>
            <tr>
                <td colspan="7">
                    <div class="total-part">
                        <div class="total-left w-85 float-left" align="right">
                            <p>Sub Total</p>
                            <p>Deposite Amount</p>
                        </div>
                        <div class="total-right w-15 float-left text-bold" align="right">
                            <p>{{!empty($rentItem->price) ? $rentItem->price : ''}}</p>
                            <p>{{!empty($rentItem->deposite_amount) ? $rentItem->deposite_amount : ''}}</p>
                        </div>
                        <div style="clear: both;"></div>
                    </div> 
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="add-detail mt-10">
    <div class="w-50 float-left mt-10">
        <p class="m-0 pt-5">Invoice Owner</p>
        <p class="m-0 pt-5 text-bold use-name">{{!empty($rentItem->owners) ? $rentItem->owners->full_name : ''}}</p>
        <p class="m-0 pt-5">{{!empty($rentItem->rentAddress) ? $rentItem->rentAddress->address : ''}}</p>
        <p class="m-0 pt-5 text-bold">{{!empty($rentItem->owners) ? $rentItem->owners->mobile : ''}}</p>
    </div>
    <div style="clear: both;"></div>
</div>
</html>