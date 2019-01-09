<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

	@include('agent.layout.write-openid')

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
	<title>我的客服</title>
	<link rel="stylesheet" href="/static/css/bootstrap.css">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div class="serviceBg">
		<img src="/static/images/banner.png" class="sBanner nopoint" />
		<!-- 
		<img src="/static/images/logo.png" class="sLogo" />
		<img src="/static/images/logotwo.png" class="sLogoTwo" />
		-->
	</div>
	<div class="serviceInfo">
		<div class="info_item">
            <a href="tel:4000421110">
                <span class="info_item_title">客服电话</span>
                <span class="info_item_detail">400-042-1110</span>
                <span class="info_item_btn">立即拨打</span>
            </a>
		</div>
		<!--  
		<div class="info_item_erweima">
			<span class="info_item_title">微信客服：</span>
			<div class="info_item_detail">
				<img class="service_erweima" src="/static/images/erwm.png" />
			</div>
		</div>
		-->
	</div>

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
</body>
</html>