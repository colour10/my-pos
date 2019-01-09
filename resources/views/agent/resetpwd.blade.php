<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">     
    <title>忘记提现密码</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
    <style>
        .w100 .section {
            width:80%;
            margin:0 auto;
        }
    </style>
</head>
<body>
	<div class="set_modify_pwd">
		<div class="modify_part1 w100">
			<div class="section">
                <text class="binding_item_name"><span>{{ $agent->mobile }}</span></text>
			</div>

            <div class="section">
                <input id="captcha" type="number" placeholder="请输入手机验证码" />
                <input type="button" id="getcode" value="获取验证码" class="hqyzmBtn" />   
            </div>

	        <div class="section">
	        	<input id="id_number" type="text" placeholder="请输入身份证号" name="id_number" />
	        </div>

			<div class="section">
				<input type="password" id="pwd" placeholder="请输入新提现密码（6位数字）" name="cash_password" />
			</div>

			<div class="section">
				<input type="password" id="repwd" placeholder="请再输入一次新提现密码（6位数字）" name="cash_password_confirmation" />
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

        // 发送验证码逻辑
        $('#getcode').click(function() {
            wxcreatecode({{ $agent->mobile }}, $('#getcode'));
        });

        // 提交逻辑
        $('#next').click(function() {

            // 验证码逻辑
            if ($('#captcha').val() == '') {
                prompt('请输入验证码');
                $('#captcha').focus();
                return false;
            }

            // 验证提现密码
            if ($('#password').val()==""){
                prompt('请填写原提现密码！');
                $('#password').focus();
                return false;
            }

            // 身份证号
            if ($('#id_number').val() == '') {
                prompt('请输入身份证号');
                $('#id_number').focus();
                return false;
            }

            var ifid = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if (!ifid.test($('#id_number').val())){
                prompt('身份证号码格式不正确，请重新填写！');
                $('#id_number').focus();
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

            // 判断重置密码验证码是否正确
            if (!checkwxcode($('#captcha').val())) {
                prompt('验证码不正确');
                $('#captcha').focus();
                return false;
            } else {
                // 确认提交
                $.post("{{ route('wxupdateresetpwd', ['id' => $id]) }}", {
                    "_token" : "{{ csrf_token() }}",
                    "_method" : "PUT",
                    'id_number' : $('#id_number').val(),
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
                        prompt(response.msg);
                        return false;
                    }
                }).error(function(response) {
                    if (response.status == '422') {
                        var jsonObj = JSON.parse(response.responseText);
                        var errors = jsonObj.errors;
                        for (var item in errors) {
                            for (var i=0, len=errors[item].length; i<len; i++) {
                                prompt(errors[item][i]);
                                return false;
                            }
                        }
                    } else {
                        prompt('修改失败');
                        return false;
                    }
                });
                // 禁止自动跳转
                return false;
            }
        });

	</script>
</body>
</html>