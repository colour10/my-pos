<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">    
    <title>我的订单</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/dropload.css') }}">
    <style>
        body{
            background: #f4f6f8;
        }
        .tab-content{
            padding-top: 2rem;
        }
    </style>
</head>
<body>
	
	<div class="navtab_box">

	  <!-- Nav tabs -->
	  	<ul class="nav nav-tabs" role="tablist" style="position: relative;">
		    <li role="presentation" class="active col-sm-3 col-xs-3 tab_item"><a href="#audit" aria-controls="audit" role="tab" data-toggle="tab">审核中</a></li>
		    <li role="presentation" class="col-sm-3 col-xs-3 tab_item"><a href="#done" aria-controls="done" role="tab" data-toggle="tab">已完成</a></li>
		    
	  	</ul>

	  <!-- Tab panes -->
	  	<div class="tab-content">
		    <div role="tabpanel" class="tab-pane active auditbox" id="audit">
		    	<div class="orderList audit_list">
		      		
		      	</div>
		      	<!--  
		      	<div class="understroke loading-hook">
		      		<text class="understroke_text">下划加载更多</text>
		      		<img  class="understroke_img" src="/static/images/more.png" />
		      	</div>
		      	-->
		    </div>
		    <div role="tabpanel" class="tab-pane donebox" id="done">
		    	<div class="orderList done_list">
		      		
		      	</div>
		      	<!--  
		      	<div class="understroke">
		      		<text class="understroke_text ">下划加载更多</text>
		      		<img  class="understroke_img" src="/static/images/more.png" />
		      	</div>
		      	-->
		    </div>
	  	</div>

	</div>

    @include('agent.layout.floatbtn')

	<script src="/static/js/jquery.js"></script>
	<script src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/dropload.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/bankinfo.js') }}"></script>
	<!-- <script src="/static/js/order.js"></script> -->
	<!-- <script src="/static/js/dropload-function.js"></script> -->
    <script type="text/javascript">

        //下拉刷新回调函数
        var html = ''

        // 填充数据
        $(function() {
            // 审核中订单列表
            $.post('{{ route("wxrevieworders") }}', {
                'user_openid': "{{ $user['id'] }}",
                '_token': "{{ csrf_token() }}",
            }, function(response) {
                console.log('---------- 打印审核中订单列表 Start ----------');
                console.log(response);
                console.log('---------- 打印审核中订单列表 End ----------');
                
                var len = response.length;
                var html = '';
                // 判断是否为空，如果为空
                if (len == '0') {
                    var html = "<div class='data_null'><img src='/static/images/nodata.png' /></div>";
                    $(".audit_list").append(html);
                    html = "";
                } else {
                    // 如果不为空则填充数据
                    for (var i = 0; i < len; i++) {
                        html += '<div class="order_item">';
                        html += '<div class="order_no">';
                        html += '<text class="order_item_title order_no_title">订单编号</text>';
                        html += '<text class="order_item_detail">' + response[i].order_id +'</text>';
                        html += '</div>';
                        html += '<div class="card_detail">';
                        html += '<div class="order_img">';
                        html += '<img src="'+response[i].cardbox.merCardOrderImg+'" />';
                        html += '</div>';
                        html += '<div class="order_info">';
                        html += '<div class="order_info_item">';	
                        html += '<text class="order_item_title">申请人：</text>';
                        html += '<text class="order_item_detail">'+response[i].agent.name+'</text>';
                        html += '</div>';
                        html += '<div class="order_info_item">';
                        html += '<text class="order_item_title">手机号：</text>';
                        html += '<text class="order_item_detail">'+response[i].agent.mobile
                        +'</text>';
                        html += '</div>';
                        html += '<div class="order_info_item">';
                        html += '<text class="order_item_title">申请时间：</text>';
                        html += '<text class="order_item_detail">'+response[i].created_at+'</text>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    }
                    $(".audit_list").append(html);
                    html = "";
                }
            });

            // 已完成订单列表
            $.post('{{ route("wxfinishorders") }}', {
                'user_openid': "{{ $user['id'] }}",
                '_token': "{{ csrf_token() }}",
            }, function(response) {
                console.log('---------- 打印已完成订单列表 Start ----------');
                console.log(response);
                console.log('---------- 打印已完成订单列表 End ----------');
                
                var len = response.length;
                // 判断是否为空，如果为空
                if (len == '0') {
                    var html = "<div class='data_null'><img src='/static/images/nodata.png' /></div>";
                    $(".done_list").append(html);
                    html = "";
                } else {
                    // 如果不为空则填充数据
                    var html = '';
                    for (var i = 0; i < len; i++) {
                        html += '<div class="order_item">';
                        html += '<div class="order_no">';
                        html += '<text class="order_item_title order_no_title">订单编号</text>';
                        html += '<text class="order_item_detail">' + response[i].order_id +'</text>';
                        html += '</div>';
                        html += '<div class="card_detail">';
                        html += '<div class="order_img">';
                        html += '<img src="'+response[i].cardbox.merCardOrderImg+'" />';
                        html += '</div>';
                        html += '<div class="order_info">';
                        html += '<div class="order_info_item">';	
                        html += '<text class="order_item_title">申请人：</text>';
                        html += '<text class="order_item_detail">'+response[i].agent.name+'</text>';
                        html += '</div>';
                        html += '<div class="order_info_item">';
                        html += '<text class="order_item_title">手机号：</text>';
                        html += '<text class="order_item_detail">'+response[i].agent.mobile+'</text>';
                        html += '</div>';
                        html += '<div class="order_info_item">';
                        html += '<text class="order_item_title">申请时间：</text>';
                        html += '<text class="order_item_detail">'+response[i].created_at+'</text>';
                        html += '</div>';
                        html += '<div class="order_info_item">';
                        html += '<text class="order_item_title">申请状态：</text>';
                        html += '<text class="order_item_detail">'+response[i].status_name+'</text>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    }
                    $(".done_list").append(html);
                    html = "";
                }
            });
        });
    </script>
</body>
</html>