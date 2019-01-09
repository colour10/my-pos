<!DOCTYPE html>
<html>
<head>
	<meta name="yiopay" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{{ $page_title }} - 意远合伙人管理系统</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
	<script type="text/javascript" src="/backend/js/jquery-1.12.3.min.js"></script>
	<link type="text/css" href="/backend/css/main.min814b.css" rel="stylesheet" />
	<link href="/backend/css/acount6aa4.css" rel="stylesheet" />
	<link rel="shortcut icon" href="/backend/images/favicon.ico" type="image/x-icon"/>
	<style>
		#login label {
			cursor:pointer;
		}
		#admin {
			margin-left:10px;
		}
        .login .form-group ul, .login .form-group ul li {
            width:100%;
            float:none;
        }
        .login .form-group ul {
            padding-left: 0;
        }
        .login .form-group ul li {
            text-align:left;
            margin:0;
        }
	</style>
    <script src="/backend/layer/layer.js"></script>
	<!-- <script type="text/javascript" src="/backend/js/style.js"></script> -->
</head>
<!--/************************************************************
 *																*
 * 						      意远							*
 *                    							*
 *       	           	  努力创建完善中		*
 * 																*
**************************************************************-->
<body style="background: #f6f6f6">

<div class="wrapper" id="login_head" style="display:">
	<div class="log_head">
	  <h1 class="log_logo left"><a href="javascript:;"><span>合伙人管理系统</span></a></h1>
	</div>
</div>


<div class="login_wrap" style="width:; background:#fff url(/backend/images/20161209115754_5628.jpg) no-repeat center top; padding:40px 0;">
	<div class="wrapper" id="login_body">
		<div class="log_ad" style="display:"></div>
		<div class="login_border" style="padding:8px;">
			<div class="login" style="display: block;">
				<div style="position:absolute; right:30px; top:14px;">
				<!-- 
				*********************************这个是注册模块 ****************************	-->
				
					<!-- <a href="javascript:;" target="_blank">账号注册
						<em style="width:16px; height:16px; background:#999; float:right; color:#fff; border-radius:100%; text-align:center; line-height:16px; margin:1px 0 0 5px; font-family:'宋体'; font-weight:bold;">&gt;</em>
					</a> -->
				
				</div>
				<ul class="login-tab">
					<li class="login-on">普通登录</li>
					<!-- <li>验证码登录</li> -->
				</ul>
				
			 <div class="login-body">

				<form action="/admin/logindo" name="lonin1" method="post" id="login">

                    @csrf

					<div class="login-style" style="display: block;">

						<dl>
							<dd><input name="mobile" type="text" id="txtUser" placeholder="手机号" /></dd>
						</dl>

						<dl>
							<dd><input type="password" id="Userpwd" name="password" placeholder="请输入您的密码" /></dd>
						</dl>

						<dl id="yz-code" style="display:none;">
						    <dd>
                                <input type="text" id="txtCode2" name="txtCode2" style="width: 133px; margin-right: 10px;" placeholder="验证码" />
                                <img id="Img1" src="{{ URL('/pc/captcha/1') }}" width="80" height="34" title="点击换一个" style="vertical-align: middle; margin-top: -4px; cursor:pointer" onclick="javascript:re_captcha();" />
                            </dd>
						</dl>

						<dl>
							<dd>
								您的身份：
                                <!--
								<label for="agent"><input type="radio" name="identity" id="agent" checked="checked" value="agent"> 合伙人</label>  
                                -->
								<label for="admin"><input type="radio" name="identity" id="admin" value="admin" checked="checked"> 管理员</label>
							</dd>
						</dl>




						<!-- <dl id="logincode" style="display: none;">
                            <dd>
                                <input type="text" id="login_yzm" name="yzm" style="width: 133px; margin-right: 10px;" placeholder="验证码" />
                                <img src="{{ URL('/code/captcha/1') }}" id="captchaid" width="90" height="34" title="点击换一个" style="vertical-align: middle; margin-top: -4px;" onclick="javascript:re_captcha();" />
                            </dd>
						</dl> -->



						<!-- 
						 **************以下是忘记密码模块****************************************
						 -->
						
						<!-- <div class="psword" style="margin-top:15px;"><a href="javascript:void(0);" onclick="zhaohui(this)" tabindex="-1" class="right" target="_blank">忘记密码?</a></div> -->
						
						 <!--***** 复选框************ -->
						 
						 
						<div class="remember">
							<input type="checkbox" name="is_remember" id="issave" value="1" checked="checked" /><label for="issave">下次自动登录</label>     
						</div>
						<!-- <div class="tishi"></div> -->


                        @include('admin.layout.error')


						<button id="logbtn" style="outline:none">登 录</button>
					 </div>
					</form>
		
					<!-- <div class="login-style">
						<dl><dd><input name="userphone" type="text" id="userphone" placeholder="您的手机号码" /></dd></dl> -->



						<!-- <dl id="yz-code" style="display: none;">
						<dd><input type="text" id="txtCode2" name="txtCode2" style="width: 133px; margin-right: 10px;" placeholder="验证码" /><img id="Img1" src="" width="90" height="34" title="点击换一个" style="vertical-align: middle; margin-top: -4px;" onclick="this.src='/ImgCode.aspx?t='+Math.random()*100" /></dd>
						</dl> -->



						<!-- <dl>
							<dd><input type="text" id="dynamicPWD" onkeydown="enterHandler(event)" style="width: 133px;" placeholder="短信验证码" /><input type="button" id="btn" class="btn_mfyzm" value="获取动态密码" onclick="Sendpwd(this)" /></dd>
						</dl>

						<div class="remember">
							<input name="is_remember" type="checkbox" id="issave1" checked /><label for="issave1">下次自动登录</label>
						</div>

						<div class="tishi"></div>
						<button type="submit" id="dynamicLogon" style="outline:none">登 录</button> -->


					</div>
				</div>
				<div class="qiehuan"></div>

				<div id="zhishi" style="position:absolute; right:-185px; bottom:0; cursor:pointer;"><img src="/backend/images/zhishi2.png" /></div>

			</div>
			<div class="login" style="display: none;">
				<i class="qiehuan" style="background-position:left bottom;"></i>
				<div class="app_login">
					<h1><i>-</i>登录失败，请刷新二维码后重试！</h1>
					<h2>使用微信扫码安全登录</h2>
				</div>

				<!-- <div class="app_code"><img id="appLoginCode" src="http://pan.baidu.com/share/qrcode?w=155&h=155&url=http://www.sucaihuo.com" /></div> -->

				<div class="shuaxin">
					<span>刷新二维码</span>
					<p><a href="javascript:;" target="_blank">查看使用帮助</a></p>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>
<div class="wxlogma">
	<a class="close" onclick="closewx()"></a>
	<h3>微信扫一扫二维码登录</h3>
	<iframe width="200" height="200"  id="weixinCode"></iframe>
</div>
<div id="bindweixin" style="display:none;">
	<div class="bindWeixin">
		<p class="login-success">登录成功！</p>
		<div class="login-tips">为了您的帐号安全，建议绑定微信号</div>
		<img id="twocodetemp" src="#" />
	</div>
</div>
<div class="bottom">
	<div class="wrapper">
	
		<div class="copy">
			<p> 全国服务热线：400-042-1110 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 工作时间：周一至周五 09:00-17:30</p>
			<p>ICP备案号：津ICP备14003495号-1 &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; </p>
			<p>Copyright © 2012 - 2018 意远 All Rights Reserved</p>
		</div>
	</div>
</div>
</body>
	<script type="text/javascript">

        // 初始化
        $(function() {
            // 验证码初始化
            if (window.localStorage.getItem('error_login_count') > 2) {
                $('#yz-code').show();
            }
        });

        // 刷新验证码
        function re_captcha() {
            $url = "{{ URL('/pc/captcha') }}";
            $url = $url + "/" + Math.random();
            document.getElementById('Img1').src = $url;
        }

        // 登录
		$("#logbtn").click(function() {

            //  手机号
            var mobile = $("#txtUser").val();
            // var capt = $("#capt").val();
            if(!/^1[3|4|5|6|7|8|9]\d{9}$/.test(mobile)) {
                layer.msg('请输入11位正确的手机号码', { icon: 2 });
                return false;
            }

            // 密码
            if ($('#Userpwd').val() == "") {
                layer.msg('请输入密码！', { icon: 2 });
                $('#Userpwd').focus();
                return false;
            }

            // 判断验证码是否已经打开
            if (window.localStorage.getItem('error_login_count') > 2) {
                if ($('#txtCode2').val() == "") {
                    layer.msg('请输入验证码！', { icon: 2 });
                    $('#txtCode2').focus();
                    return false;
                }
            }

            // 如果都通过了，那么就ajax提交
            var form = document.getElementsByName("lonin1")[0];
            var fd = new FormData(form);

            $.ajax({
                type: 'post',
                url: "/admin/logindo",
                data: fd,
                dataType: 'json',
                timeout: 99999,
                processData: false,
                contentType: false,
                success: function(data) {

                    // 测试数据
                    // console.log(data);

                    if (data.code == 0) {

                        // 使用localStorage存储用户信息
                        // 如果有登录错误的记录，那么就清除
                        var storage = window.localStorage;
                        if (storage.getItem('error_login_ip') !== null) {
                            storage.removeItem('error_login_ip');
                        }
                        if (storage.getItem('error_login_count') !== null) {
                            storage.removeItem('error_login_count');
                        }

                        // 成功返回
                        layer.msg(data.msg, { icon: 1 });

                        // 3000毫秒后跳转
                        setTimeout("window.location.href='{{ route("index") }}'", 3000);

                    } else {
                        // 如果登录失败，那么就限制其登录次数，然后加验证码
                        // console.log('当前登录ip地址：' + data.login_ip);
                        // console.log('当前登录次数：' + data.login_count);
                        // 用户手机记录登录错误次数
                        window.localStorage.setItem('error_login_ip', data.login_ip);
                        window.localStorage.setItem('error_login_count', data.login_count);
                        // 验证码刷新，防止缓存污染
                        re_captcha();
                        $('#txtCode2').val('');

                        // 失败返回
                        layer.msg(data.msg, { icon: 2 });

                        // 如果超过3次，就调出验证码
                        if (data.login_count > 2) {
                            $('#yz-code').show();
                        }
                    }
                },
                error: function(data) {
                    // 捕捉错误
                    if (data.status == 422) {
                        var jsonObj = JSON.parse(data.responseText);
                        var errors = jsonObj.errors;
                        for (var item in errors) {
                            for (var i=0, len=errors[item].length; i<len; i++) {
                                layer.msg(errors[item][i]);
                                return false;
                            }
                        }
                    } else {
                        var jsonObj = JSON.parse(data.responseText);
                        layer.msg('错误代码：'+jsonObj.code+'，错误类型：'+jsonObj.msg);
                        return false;
                    }
                },
            });

            return false;

            });

    </script>
</html>
