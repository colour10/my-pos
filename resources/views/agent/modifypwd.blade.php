<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">        
    <title>修改提现密码</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div class="set_modify_pwd">
		<div class="modify_part1 w100">
			<div class="section">
				<input id="mobile" type="number" placeholder="注册手机号" class="tel_register" name="mobile" />
			</div>

	        <div class="section">
	        	<input id="password" type="password" placeholder="请输入原提现密码（6位数字）" style="padding-left: 1.05rem;" name="password" />
	        </div>

			<div class="section">
				<input type="password" id="pwd" placeholder="请输入新提现密码（6位数字）" style="padding-left: 1.05rem;" name="cash_password" />
			</div>

			<div class="section">
				<input type="password" id="repwd" placeholder="请再输入一次新提现密码（6位数字）" style="padding-left: 1.05rem;" name="cash_password_confirmation" />
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
        $('#next').click(function() {

            // 手机号码
            if ($('#mobile').val()=="") {
                prompt('请填写手机号码！');
                $('#mobile').focus();
                return false;
            }
            var mob = /^1[3,4,5,6,7,8]\d{9}$/;
            if (!mob.test($('#mobile').val())){
                prompt('手机格式不正确，请重新填写！');
                $('#mobile').focus();
                return false;
            }

            // 验证提现密码
            if ($('#password').val()==""){
                prompt('请填写原提现密码！');
                $('#password').focus();
                return false;
            }

            // 验证新提现密码
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

            // 确认提交
            $.post("{{ route('wxupdatepwd', ['id' => $id]) }}", {
                "_token" : "{{ csrf_token() }}",
                "_method" : "PUT",
                "mobile" : $('#mobile').val(),
                "password" : $('#password').val(),
                "cash_password" : $('#pwd').val(),
                "cash_password_confirmation" : $('#repwd').val(),
            }, function(response) {
                if (response.code == '0') {
                    // 成功返回
                    prompt(response.msg);
                    // 3000毫秒后跳转
                    setTimeout("window.location.href = '{{ route('wxsetting') }}'", 3000);
                } else {
                    // 失败返回
                    return prompt(response.msg);
                }
            }).error(function(response) {
                if (response.status == '422') {
                    var jsonObj = JSON.parse(response.responseText);
                    var errors = jsonObj.errors;
                    for (var item in errors) {
                        for (var i=0, len=errors[item].length; i<len; i++) {
                            return prompt(errors[item][i]);
                        }
                    }
                } else {
                    return prompt('服务器连接失败，请重试...');
                }
            });
            // 禁止自动跳转
            return false;
        });
	</script>
</body>
</html>