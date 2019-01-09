<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Laravel
                </div>

                <div class="links">
                    <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="/static/js/jquery.js"></script>
        <script src="/backend/layer/layer.js"></script>
        <script type="text/javascript" src="/backend/js/laravel.js"></script>

        <!-- 测试验证码 start -->
        <form method="post" name="login_form" style="width:500px; margin:0 auto;">
            @csrf
            <input name="captcha" type="text" placeholder="验证码">
            <a onclick="javascript:re_captcha();">  
            <img src="{{ URL('/logcode/captcha/1') }}" id="captchaid">
            <input type="submit" value="提交" id="btn-submit">
            <br><br><br>
        </form>

        <script type="text/javascript">  
            function re_captcha() {  
                $url = "{{ URL('/logcode/captcha') }}";
                $url = $url + "/" + Math.random();
                document.getElementById('captchaid').src = $url;
                // 取出验证码
                $.get('/code/getcaptcha', function(response) {
                    console.log('当前系统保存的验证码为：' + response);
                });
            }

            // 取出验证码
            $.get('/code/getcaptcha', function(response) {
                console.log('当前最新保存的验证码为：' + response);
            });

            // 如果都通过了，那么就ajax提交
		    $("#btn-submit").click(function() {
                var form = document.getElementsByName("login_form")[0];
                var fd = new FormData(form);
                // 处理逻辑
                $.ajax({
                    type: 'post',
                    url: '/checkcaptcha',
                    data: fd,
                    dataType: 'json',
                    timeout: 99999,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        index = layer.load(1, {
                            shade: [0.5,'#fff'] //0.1透明度的白色背景
                        });
                    },
                    complete: function () {
                        layer.close(index);
                    },
                    success: function(data) {
                        if (data.code == 0) {
                            layer.msg(data.msg, { icon: 1 });
                            // 3000毫秒后跳转
                            // setTimeout("document.location.reload()", 3000);
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                            // 如果验证码错误，那么就重新刷新
                            console.log('即将开始刷新验证码，请稍候...');
                            re_captcha();
                            // 3000毫秒后跳转
                            // setTimeout("document.location.reload()", second*1000);
                        }
                    },
                    error: function(data) {
                        if (data.status == 422) {
                            var jsonObj = JSON.parse(data.responseText);
                            var errors = jsonObj.errors;
                            for (var item in errors) {
                                for (var i=0, len=errors[item].length; i<len; i++) {
                                    layer.msg(errors[item][i], { icon: 2 });
                                    return;
                                }
                            }
                        } else {
                            layer.msg('服务器连接失败', { icon: 2 });
                            return;
                        }
                    },
                });
                return false;
            });
        </script>
        <!-- 测试验证码 end -->

    </body>
</html>
