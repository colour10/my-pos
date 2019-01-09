<?php
    $page_title = '您没有权限访问本页面';
?>

@extends('admin.layout.main')

@section('content')
<div class="container marginTop">

    <style type="text/css">
        *{   
            margin: 0;   
            padding: 0;   
            /* background-color: #EAEAEA;    */
        }   
        html {
            /* overflow-x:hidden; */
        }
        .center-in-center{   
            position: absolute;   
            top: 50%;   
            left: 50%;   
            -webkit-transform: translate(-50%, -50%);   
            -moz-transform: translate(-50%, -50%);   
            -ms-transform: translate(-50%, -50%);   
            -o-transform: translate(-50%, -50%);   
            transform: translate(-50%, -50%);  
            width: 500px;   
            height: 500px;   
            line-height: 500px;
            text-align:center;
            /* background-color: #1E90FF;   */
            font-size:2em; 
            margin-top:-160px;
            margin-left:-100px;
        }   
    </style>

    <div class="center-in-center">
        对不起，您没有权限访问该页面！
    </div> 

</div>

@endsection


