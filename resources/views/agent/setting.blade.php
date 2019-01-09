<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>设置</title>
	<link rel="stylesheet" href="/static/css/bootstrap.css">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div>
		<ul class="setting_part">
		</ul>
	</div>

    @include('agent.layout.floatbtn')

	<script src="/static/js/jquery.js"></script>
	<script src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
	<!-- 微信分享json -->
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript">
        // 初始化
        $(function() {
            // 逻辑
            $.post('{{ route("wxmy") }}', {
                "_token" : "{{ csrf_token() }}",
                'openid': window.localStorage.getItem('openid'),
            }, function(response) {

                // 打印结果
                console.log(response);

                //判断是否绑卡
                $.post('{{ route("wxfirstcard") }}', {
                    "_token" : "{{ csrf_token() }}",
                    'openid': window.localStorage.getItem('openid'),
                }, function(data) {
                    // 打印结果
                    console.log(data);
                    // 修改登录密码永远有
                    var html = '';
                    // Changing 登录暂时不需要了，改成授权登录
                    // html += '<li id = "setloginpass"><span>修改登录密码</span></li>';
                    // 如果没有绑卡
                    if (data.length == 0) {
                        // 显示绑卡
                        html += '<li id = "tiecard"><span>我要绑卡</span></li>';
                    } else {
                        // 如果已经绑卡了，那么就可以判断是否设置了提现密码
                        if (response.agent.cash_password == null) {
                            html += '<li id = "set"><span>设置提现密码</span></li>';
                        } else {
                            html += '<li id = "update"><span>修改提现密码</span></li>';
                            html += '<li id = "reset"><span>忘记提现密码</span></li>';
                        }
                    }
                    // 写入dom节点
                    $(".setting_part").html(html);

                    // 绑卡跳转
                    $("#tiecard").click(function() {
                        window.location.href = "{{ route('wxaddcard') }}";
                    });

                    // 设置提现密码
                    $("#set").click(function() {
                        window.location.href = '/agent/wx/'+ response.agent.id +'/setpwd';
                    });

                    // 修改提现密码
                    $("#update").click(function() {
                        window.location.href = '/agent/wx/'+ response.agent.id +'/modifypwd';
                    });

                    // // 修改登录密码
                    // $("#setloginpass").click(function() {
                    //     window.location.href = '/agent/wx/'+ response.agent.id +'/modifyloginpwd';
                    // });

                    // 忘记提现密码
                    $("#reset").click(function() {
                        window.location.href = '/agent/wx/'+ response.agent.id +'/resetpwd';
                    });
                });
                // 禁止往下执行
                return false;
            });
            // 禁止往下执行
            return false;
        });
	</script>
</body>
</html>