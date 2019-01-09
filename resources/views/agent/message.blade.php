<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	@include('agent.layout.csrf')

	@include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')	

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
	<title>我的信息</title>
	<link rel="stylesheet" href="/static/css/bootstrap.css">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div class="msgBack">
		<img src="/static/images/bgbg.png" class="msgBg nopoint" />
		<img class='headimgurl userAvatar' src="{{ $user['original']['headimgurl'] }}" />
	</div>
	<div class="msgList">
		<div class="msg_item">
			<text class="item_title">姓名</text>
			<text class="item_detail msg_name"></text>
		</div>
		<div class="msg_item">
			<text class="item_title">级别</text>
			<text class="item_detail msg_level"></text>
		</div>
		<div class="msg_item">
			<text class="item_title four_item_title">手机号码</text>
			<text class="item_detail msg_mobile"></text>
		</div>
		<!-- <div class="msg_item">
			<text class="item_title four_item_title">身份证号</text>
			<text class="item_detail msg_identity">--</text>
		</div> -->
	</div>

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<!-- 微信分享json -->
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/bankinfo.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
</body>
</html>