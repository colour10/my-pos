
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">       
    <title>申请提现</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/incentive.css') }}">
</head>
<body>
	<div class="reflectInfo">
		<img src="/static/images/tixianjilu_bg.png" class="reflectBg nopoint" />
		<div class="reflectDetail">
			<text class="reflectDes">可提现金额(元)</text>
			
			<div class="reflectMoney">
				<span class="reflect_money_num"></span>
				<span class="reflect_btn" onclick="checkCash()">提现</span>
			</div>
			
		</div>
		
	</div>
	<div class="reflectList">
		<div class="reflectList_item reflectDetailTitle">
			<span class="reflect_title_money">提现金额</span>
			<span class="reflect_title_date">提现时间</span>
			<span class="reflect_title_state">提现状态</span>
		</div>
	</div>

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
    <script type="text/javascript">
    
        // 赋值
        var openid = window.localStorage.getItem('openid');

        // 请求提现记录
        var withdraws = getWithdraws(openid);
        if (!withdraws) {
            var html = "<div class='data_null'><img src='/static/images/nodata.png' /></div>";
        } else {
            var html = htmlString(withdraws);
        }
        // 动态添加dom
        $(".reflectList").append(html);

        // 提现记录按照特定格式输出
        function htmlString(data) {
            var html = '';
            var len = data.data.length;
            
            for(var i=0; i<len; i++) {
                html += '<div class="reflectList_item reflect_item" data-index="'+i+'"  onclick="goshow(this);">';
                html += '<span class="item_money lineHeight">'+ data.data[i].sum.toFixed(2) +'</span>';
                html += '<span class="item_date lineHeight">'+ data.data[i].updated_at +'</span>';
                html += '<span class="item_state lineHeight status_name">'+data.data[i].status_name+'</span>';
                // html += '<div class="item_right lineHeight">';
                // html += '   <img src="/static/images/you.png" />';
                // html += '</div>';
                html += '</div>';
                html += '<div class="reflectList_item content">';
                if (data.data[i].err_msg) {
                    html += '<span class="item_money lineHeight">失败原因：'+data.data[i].err_msg+'</span>';
                }
                html += '</div>';
            }
            return html;
        }

        // 提现
        // 判断是否绑卡
        function checkCash() {
            if (!checktiecard(openid)) {
                window.location.href = "{{ route('wxaddcard') }}";
            } else {
                setting();
            }
        }

        // 判断是否已经绑卡及设置交易密码
		function setting() {
            var agentAccountResult = getAgentAccount(openid);
            if (agentAccountResult) {
                // 未设置提现密码
                if(!agentAccountResult.agent.cash_password) {
                    // 提示
                    prompt('您需要先设置提现密码');
                    // 未设置提现密码
                    setTimeout(function() {
                        window.location.href = '/agent/wx/'+ agentAccountResult.agent.id +'/setpwd'
                    }, 3000);
                } else {
                    // 已经设置了提现密码就跳转到提现逻辑页面
                    window.location.href = '{{ route("wxdrawcash") }}';                    
                }
            } else {
                // 否则返回首页进行授权
                prompt('请从首页授权访问');
                // 未设置提现密码
                setTimeout(function() {
                    window.location.href = '/agent/wx'
                }, 3000);
            }
        }
        
        // 结算失败原因显示
        function goshow(indexNode) {
            var ns = indexNode.nextSibling;
            if (ns) {
                if (ns.style.display == 'block') {
                    ns.style.display = 'none';
                } else {
                    ns.style.display = 'block';
                }
            }
        }

	</script>
</body>
</html>