<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">        
    <title>激励金明细</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/incentive.css') }}">
    <style>
        .understroke{
            display: none;
        }
        #wrapper{
            /* margin-top: 2.13rem; */
        }
        .incentiveList_item span, .pullUpLabel {
            font-size: .64rem;
        }
    </style>
</head>
<body>
	<div id="incentivedetail">
		<div class="incentiveList">
			<!-- <img src="/static/images/erji_bgbg.png" /> -->
			<div class="incentiveList_item incentive_detail_title">
				<span class="col-sm-3 col-xs-3">姓名</span>
				<span class="col-sm-2 col-xs-2">金额</span>
				<span class="col-sm-3 col-xs-3">时间</span>
				<span class="col-sm-4 col-xs-4">来源</span>
			</div>
			<div id="wrapper">
				<div id="scroller">
					<ul id="thelist"></ul>
				</div>
			</div>
		</div>
	</div>

    @include('agent.layout.floatbtn')

	<div id="test"></div>
	<script src="/static/js/jquery.js"></script>
	<script src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/iscroll.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
    <script type="text/javascript">
        // 获得数据列表
        function getTeamData() {
            $.post('{{ route("wxgetincent") }}', {
                    'openid': "{{ $user['original']['openid'] }}",
                    '_token': "{{ csrf_token() }}",
                }, function(response) {

                // 取出结果
                console.log('-------取出激励金明细 start-------');
                console.log(response);
                console.log('-------取出激励金明细 end-------');

                // 逻辑
                var code = response.code;
                var len = response.count;
                var agent = response.data.agent;
                var data = response.data.finances;
                // 判断是否为空
                if (len == '0') {
                    var html = "<div class='data_null'><img src='/static/images/nodata.png' /></div>";
                    $("body").append(html);
                    document.getElementById("wrapper").style.height = '0';
                    $("#pullUp").hide();
                    html = "";
                } else {
                    // 如果不为空则填充数据
                    var html = '';
                    for (var i=0; i<len; i++) {
                        html += '<div class="incentiveList_item">';
                        html += '<span class="col-sm-3 col-xs-3">'+agent.name+'</span>';
                        html += '<span class="incentiveList_item_money col-sm-2 col-xs-2">'+data[i].amount+'</span>';
                        html += '<span class="col-sm-3 col-xs-3">'+data[i].format_created_at+'</span>';
                        html += '<span class="col-sm-4 col-xs-4">'+data[i].source+'</span>';
                        html += '</div>';
                    }
                    // 首先清空原来的dom
                    $("#thelist").empty();
                    // 写入dom，先隐藏，功能还没有做好
                    $("#thelist").append(html);
                }
            });
        }
    </script>
</body>
</html>