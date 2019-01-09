<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>测试授权</title>
	<link rel="stylesheet" href="/static/css/bootstrap.css">
	<link rel="stylesheet" href="/static/css/public.css">
	<link rel="stylesheet" href="/static/css/login.css">
</head>
<body>
	
	<div class="formBox">
		<div class="formOut">

        <div class="navtab_box">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist" style="position: relative;">
                <li role="presentation" class="active col-sm-3 col-xs-3 tab_item"><a href="#audit" aria-controls="audit" role="tab" data-toggle="tab">注册</a></li>
                <li role="presentation" class="col-sm-3 col-xs-3 tab_item"><a href="#done" aria-controls="done" role="tab" data-toggle="tab">登录</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active auditbox" id="audit">
                    <div class="orderList audit_list">
                        <!-- 注册 start -->
                        <form class="form-horizontal" action="" name="reg_form" method="post">

                            {{ csrf_field() }}

                            <div class="binding_item">
                                <text class="binding_item_name">手机号：</text>
                                <input id="reg_mobile" type="number" placeholder="请输入11位手机号" oninput="if(value.length>11) value=value.slice(0,11)" name="mobile" />
                                <img onclick="deletefunc('reg_mobile')" src="/static/images/delete.png" class="delete" />
                            </div>

                            <div class="binding_item">
                                <input id="vfyCode" type="number" placeholder="请输入验证码" />
                                <input type="button" id="getcode" value="获取验证码" class="hqyzmBtn" />   
                            </div>

                            <div class="binding_item">
                                <text class="binding_item_name">密　码：</text>
                                <input id="reg_password" type="password" placeholder="请输入密码" name="password" />
                                <img onclick="deletefunc('reg_password')" src="/static/images/delete.png" class="delete" />
                            </div>

                            <input type="hidden" name="parentopenid" id="parentopenid">

                            <!-- <div class="section">
                                <span>用户名：</span><input id="reg_mobile"  type="number" placeholder="请输入11位手机号" oninput="if(value.length>11) value=value.slice(0,11)" name="mobile" />
                            </div>

                            <div class="section">
                                <span>验证码：</span><input id="vfyCode" type="number" placeholder="  请输入验证码" />
                                <input type="button" id="getcode" value="获取验证码" class="hqyzmBtn" />   
                            </div>

                            <div class="section">
                                <span>密　码：</span><input id="reg_password"  type="password" placeholder="请输入密码" name="password" />
                            </div> -->

                            <div class="button_area">
                                <button id = "reg" class="button register">注册</button> 
                            </div>
                        </form>
                        <!-- 注册 end -->
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane donebox" id="done">
                    <div class="orderList done_list">
                    <!-- 登录 start -->
                    <form class="form-horizontal" action="" name="login_form" method="post">

                        {{ csrf_field() }}

                        <div class="binding_item">
                            <text class="binding_item_name">手机号：</text>
                            <input id="login_mobile" type="number" placeholder="请输入11位手机号" oninput="if(value.length>11) value=value.slice(0,11)" name="mobile" />
                            <img onclick="deletefunc('login_mobile')" src="/static/images/delete.png" class="delete" />
                        </div>

                        <div class="binding_item">
                            <text class="binding_item_name">密　码：</text>
                            <input id="login_password" type="password" name="password" placeholder="请输入密码" />
                            <img onclick="deletefunc('login_password')" src="/static/images/delete.png" class="delete" />
                        </div>

                        <div class="binding_item yzm" style="display:none;">
                            <input id="login_yzm" type="text" name="yzm" placeholder="请输入验证码" />
                            <img src="{{ URL('/wx/captcha/1') }}" id="captchaid" onclick="javascript:re_captcha();" style="cursor:pointer;">
                        </div>

                        <div class="button_area"> 
                            <button id = "login" class="button login">登录</button>
                        </div>
                    </form>
                    <!-- 登录 end -->
                </div>
            </div>
            </div>

        </div>


	</div>
	
	    
	    <!-- 用户协议 -->
	    <div class="close_" onclick="close_();">
	    	<span>返回</span>
	    </div>
		<iframe src="" class="agreement"></iframe>
		
	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<!-- 微信分享json -->
	<script src="/static/js/jweixin-1.0.0.js"></script>
	<script src="/static/js/common.js"></script>
	<script src="/static/js/rem.js"></script>
	<script src="/static/js/fastclick.js"></script>
	<script src="/static/js/public.js"></script> 
    <script type="text/javascript" src="/backend/js/laravel.js"></script>
	<script type="text/javascript">

        $(function() {
            $.post('/test/is_permit_auth', {
                "_token": "{{ csrf_token() }}",
                'authorization': window.localStorage.getItem('authorization'),
            }, function(response) {
                // 打印结果
                console.log(response);
                if (response.code == '0') {
                    return prompt('合伙人存在');
                } else {
                    return prompt('合伙人不存在');
                }
            });
        });

	</script>
</body>
</html>
