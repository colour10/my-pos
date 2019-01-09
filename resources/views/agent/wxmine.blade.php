<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>我的</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div class="myInfo">
		<div class="myInfoBg">
			<img src="/static/images/bgbg.png" />
		</div>
		<div>
			<div class="myAvatar">
				<img class='txbg' src="/static/images/txbg.png" class="txbg" />
				<img  class='headimgurl touxiang' src="{{ $user['original']['headimgurl'] }}" class="touxiang" />
			</div>
			<div class="myInfoText">
				<text class="infoText">您好，<text class="infoName nickname">{{ $user['original']['nickname'] }}</text></text>
				<text class="infoText">会员级别：<text class="hyjb"></text></text>
			</div>
		</div>
	</div>
	<div class="list_box">
		<div class="list_item" data-toage="progress" onclick="location.href='{{ route('wxprogress') }}'">
	 		<img src="/static/images/tuandui.png" class="item_left_img" />
	 		<text class="item_left">进度查询</text>
	 		<img src="/static/images/youjiantou.png" class="item_right" />
	 	</div>
	 	<div class="list_item" data-toage="order" data-href="{{ route('wxorder') }}" onclick="identityForReal(this);">
	 		<img src="/static/images/dingdan.png" class="item_left_img" />
	 		<text class="item_left">我的订单</text>
	 		<img src="/static/images/youjiantou.png" class="item_right" />
	 	</div>
	 	<div class="list_item" data-toage="message" data-href="{{ route('wxmessage') }}" onclick="identityForReal(this);">
	 		<img src="/static/images/xinxi.png" class="item_left_img" />
	 		<text class="item_left">我的信息</text>
	 		<img src="/static/images/youjiantou.png" class="item_right" />
	 	</div>

        <!-- Changing Start -->
        <div class="list_item" data-toage="rankcard" data-href="{{ route('wxrankcard') }}" onclick="identityForReal(this);">
        <!-- Changing end -->
	 		<img src="/static/images/yhk.png" class="item_left_img" />
	 		<text class="item_left">我的银行卡</text>
	 		<img src="/static/images/youjiantou.png" class="item_right" />
	 	</div>


	 	<div class="list_item" data-toage="invitation" data-href="{{ route('wxinvitation') }}" onclick="identityForReal(this);">
	 		<img src="/static/images/yongjin.png" class="item_left_img" />
	 		<text class="item_left">邀请好友</text>
	 		<img src="/static/images/youjiantou.png" class="item_right" />
	 		<text class="item_right_text">邀请狂赚激励金</text>
	 	</div>
	 	<div class="list_item"  data-toage="customerService" onclick="location.href='{{ route('wxcustomerService') }}'">
	 		<img src="/static/images/kefu.png" class="item_left_img" />
	 		<text class="item_left">我的客服</text>
	 		<img src="/static/images/youjiantou.png" class="item_right" />
	 	</div>

        <!-- changing Start -->
        <div class="list_item" data-toage="rankcard" data-href="{{ route('wxsetting') }}" onclick="identityForReal(this);">
        <!-- Changing end -->
	 		<img src="/static/images/setting.png" class="item_left_img" />
	 		<text class="item_left">设置（提现密码&绑卡）</text>
	 		<img src="/static/images/youjiantou.png" class="item_right" />
	 	</div>

        <!-- 退出功能 Start -->
	 	<!-- <div class="list_item logout" data-toage="setting" onclick="logout();">
            <img src="/static/images/setting.png" class="item_left_img" />
            <text class="item_left">退出</text>
            <img src="/static/images/youjiantou.png" class="item_right" />
	 	</div> -->
        <!-- 退出功能 End -->

        <!-- 注册&登录功能 Start -->
        <!-- <div class="list_item reg_login" data-toage="setting" onclick="reg_login();">
            <img src="/static/images/setting.png" class="item_left_img" />
            <text class="item_left">注册&登录</text>
            <img src="/static/images/youjiantou.png" class="item_right" />
	 	</div> -->
        <!-- 注册&登录功能 End -->

	</div>
	
	<div class="logo">
		<!-- <img src="/static/images/logo.png" /> -->
	</div>
	
    @extends('agent.layout.main')

    @include('agent.layout.floatbtn')

    @include('agent.layout.identity-dialog')

    <script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
</body>
</html>