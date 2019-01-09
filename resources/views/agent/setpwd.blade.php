<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">        
    <title>设置提现密码</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
    <style>
        .section input {
            width:100%;
        }
    </style>
</head>
<body>
	<div class="set_modify_pwd">
		<div class="modify_part1">
			<div class="section">
				<input type="password" id="pwd" placeholder="请输入提现密码（6位数字）" style="padding-left: 1.05rem;" name="cash_password" />
			</div>

			<div class="section">
				<input type="password" id="repwd" placeholder="请再输入一次提现密码（6位数字）" style="padding-left: 1.05rem;" name="cash_password_comfirmation" />
			</div>

			<div class="nextBtn" id="next">确认设置</div>
		</div>
	 
	</div>

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
    <script type="text/javascript">

        // 赋值
        // 微信openid
        var openid = window.localStorage.getItem('openid');
        // 合伙人id
        var id = {{ $id }};
        // 判断上一个页面的来源
        var refurl = document.referrer;

        // 判断是否绑卡
        // 如果没有绑定，那么就先把卡绑定上再进行操作
        if (!checktiecard(openid)) {
            window.location.href = "{{ route('wxaddcard') }}";
        }

        // 设置逻辑
        $('#next').click(function() {

            // 验证提现密码
            if ($('#pwd').val()==""){
                prompt('请填写提现密码！');
                $('#pwd').focus();
                return false;
            }

            // 验证确认提现密码
            if ($('#repwd').val()==""){
                prompt('请填写确认提现密码！');
                $('#repwd').focus();
                return false;
            }

            // 验证是否一致
            if ($('#repwd').val() != $('#pwd').val()) {
                prompt('两次密码输入不一致');
                $('#pwd').focus();
                return false;
            }

            // 设置提现密码逻辑
            if (setPwd(id, $('#pwd').val(), $('#repwd').val())) {
                // 成功返回
                prompt('提现密码设置成功');
                // 3000毫秒后跳转
                // 如果来自提现，那么就跳转到提现页面
                if (refurl.indexOf("withdraw") > -1) {
                    setTimeout("window.location.href = '/agent/wx/withdraw'", 3000);
                } else {
                    // 否则就跳转到设置页面
                    setTimeout("window.location.href = '/agent/wx/setting'", 3000);
                }
            } else {
                prompt('提现密码设置失败');
            }

            // 禁止自动跳转
            return false;
        });
	</script>
</body>
</html>