<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">       
    <title>修改登录密码</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div class="set_modify_pwd">
		<div class="modify_part1">

            <div class="binding_item">
                <text class="binding_item_name">手 机 号：<span id="mobile"></span></text>
            </div>

            <!-- <div class="binding_item yzm">
                <input id="login_yzm" type="text" name="yzm" placeholder="请输入验证码" />
                <img src="{{ URL('/wx/captcha/1') }}" id="captchaid" onclick="javascript:re_captcha();" style="cursor:pointer; padding-left: 1.05rem;">
            </div> -->

            <!-- <div class="binding_item yzm">
                <input id="login_yzm" type="text" name="yzm" placeholder="请输入手机验证码" />
                <img src="{{ URL('/wx/captcha/1') }}" id="captchaid" onclick="javascript:re_captcha();" style="cursor:pointer; width:50%; height:100%">
            </div> -->

            <div class="binding_item">
                <text class="binding_item_name">尊敬的用户，您好，由于系统检测到您的初始密码还未修改，为了账户安全，请务必在第一时间修改~</text>
            </div>

            <div class="binding_item">
                <input id="vfyCode" type="number" placeholder="请输入验证码" />
                <input type="button" id="getcode" value="获取验证码" class="hqyzmBtn" />   
            </div>

            <div class="binding_item">
                <text class="binding_item_name">密　　码：</text>
                <input id="password" type="password" name="password" placeholder="请输入密码" />
                <img onclick="deletefunc('password')" src="/static/images/delete.png" class="delete" />
            </div>

            <div class="binding_item">
                <text class="binding_item_name">确认密码：</text>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="请再输入一次密码" />
                <img onclick="deletefunc('password_confirmation')" src="/static/images/delete.png" class="delete" />
            </div>

			<!-- <div class="section">
				<input type="password" id="pwd" placeholder="请输入新的登录密码" style="padding-left: 1.05rem;" name="password" />
			</div>

			<div class="section">
				<input type="password" id="repwd" placeholder="请再输入一次新的登录密码" style="padding-left: 1.05rem;" name="password_confirmation" />
			</div> -->

			<div class="nextBtn" id="next">确认修改</div>
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
        // 初始化
        $(function() {
            $.post('{{ route("wxpostbyopenid") }}', {
                'openid': window.localStorage.getItem('authorization'),
                '_token': "{{ csrf_token() }}",
            }, function(response) {
                if (response.code == '0') {
                    // 拿到手机号
                    mobile = response.agent.mobile;
                    $('#mobile').text(mobile);
                }
            });
        });


        // 获取验证码逻辑
        $('#getcode').click(function() {
            // 发送验证码短信
            $.get("{{ route('wxcreatecode') }}", function(response) {
                // 打印验证码
                console.log(response);
                // 取出验证码保存在缓冲区
                current_yzm = response
                // 发送验证码，使用2002模板
                sendmsg('2002', response);
                // 前端状态显示倒计时
                sendemail();
            });

            // 然后进行倒计时300秒
            var countdown = 120;
            function sendemail() {
                var obj = $("#getcode");
                settime(obj);
            }
            // 发送验证码倒计时
            function settime(obj) { 
                if (countdown == 0) { 
                    obj.attr('disabled', false);
                    //obj.removeattr("disabled"); 
                    obj.val("获取验证码");
                    countdown = 60;
                    return;
                } else { 
                    obj.attr('disabled',true);
                    obj.val("重新发送(" + countdown + ")");
                    countdown--; 
                } 
                setTimeout(function() {
                    settime(obj) 
                }, 1000)
            }
        });


        $('#next').click(function() {
            // 判断验证码是否正确
            // 验证码逻辑
            if ($('#vfyCode').val() == '') {
                prompt('请输入验证码');
                $('#vfyCode').focus();
                return false;
            }
            if (current_yzm != $('#vfyCode').val()) {
                prompt('验证码不正确，请重新输入!');
                $('#vfyCode').focus();
                return false;
            } else {
                // 验证码输入正确，测试用
                console.log('验证码输入正确，当前值为：' + current_yzm);
                // 提交逻辑
                $.post("{{ route('AgentauthChangepassdo') }}", {
                    "_token" : "{{ csrf_token() }}",
                    "_method" : "PUT",
                    "password" : $('#password').val(),
                    "password_confirmation" : $('#password_confirmation').val(),
                    'openid': window.localStorage.getItem('authorization'),
                }, function(response) {
                    if (response.code == '0') {
                        // 成功返回
                        prompt(response.msg);
                        // 3000毫秒后跳转
                        setTimeout("window.location.href = '{{ route('wxsetting') }}'", 3000);
                    } else {
                        // 失败返回
                        prompt(response.msg);
                    }
                }).error(function(response) {
                    if (response.status == '422') {
                        var jsonObj = JSON.parse(response.responseText);
                        var errors = jsonObj.errors;
                        for (var item in errors) {
                            for (var i=0, len=errors[item].length; i<len; i++) {
                                prompt(errors[item][i]);
                                return;
                            }
                        }
                    } else {
                        prompt('服务器连接失败');
                        return;
                    }
                });
                // 禁止自动跳转
                return false;
            }
        });

        // 发送短信
        function sendmsg(id, msg) {
            // 发送短信
            $.post("{{ route('wxsendmsg') }}", {
                'tel': mobile,
                'sendid': id,
                'sendmsg': msg,
                "_token": "{{ csrf_token() }}",
            }, function(response) {
                // 数据返回测试
                console.log(response);
                // 逻辑
                if (response.errcode == '0') {
                    console.log('短信发送成功');
                } else {
                    console.log('短信发送失败');
                }
            }).error(function(response) {
                console.log(response);
            });
        }

	</script>
</body>
</html>