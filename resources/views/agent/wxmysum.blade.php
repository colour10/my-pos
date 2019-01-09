<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	@include('agent.layout.csrf')

	@include('agent.layout.write-openid')

	@include('agent.layout.identity-meta')

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">		
	<title>激励金</title>
	<link rel="stylesheet" href="/static/css/bootstrap.css">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/incentive.css') }}">
</head>
<body>
	<div class="myInfo">
		<div class="myInfoBg">
			<img class="nopoint" src="/static/images/bgbg.png" />
		</div>
		<div>
			<div class="myAvatar">
				<img src="/static/images/txbg.png" class="txbg" />
				<img class='headimgurl touxiang' src="{{ $user['original']['headimgurl'] }}" class="touxiang" />
			</div>
			<div class="myInfoText">
				<text class="infoText">您好，<text class="infoName nickname">{{ $user['original']['nickname'] }}</text></text>
				<text class="infoText">会员级别：<text class="hyjb"></text></text>
			</div>
		</div>
	</div>
	<div class="jljInfo">
		<div class="topLine"></div>
		<div class="jljInfoBg">
			<img class="nopoint" src="/static/images/bgtwo.png" />
		</div>
		<div class="jljTotal">
			<text class="jljTotalName">账户余额(元)</text>
			<text class="jljTotalNum"></text>
		</div>
		<div class="moneyJlj">
			<div class="txJlj">
				<text class="txJljName">可提现金额(元)</text>
				<text class="txJljNum txANum"></text>
			</div>
			<div class="moneyJljLine"></div>
			<div class="txJlj">
				<text class="txJljName">提现中(元)</text>
				<text class="txJljNum txDNum"></text>
			</div>
		</div>
		<div class="jljBtn">
			<div class="jijBtn_item" data-topage="reflect" onclick="window.location.href='{{ route("wxwithdraw") }}'">申请提现</div>
			<div class="jijBtn_item" data-topage="incentiveDetail" onclick="window.location.href='{{ route("wxincentivedetail") }}'">激励金明细</div>
		</div>
	</div>
	<div class="promptBox">
		<div class="prompt">
			<text>温馨提示：</text>
			<text>①信用卡确认通过审核1天后，激励金可提现；</text>
			<text>②激励金满50元后方可提现。</text>
		</div>
	</div>
	<!-- 激励金排行榜 -->
	<div class="jljphb">
		<div class="jljphbTitle">
			<img src="/static/images/phb.png" />
			<text>激励金排行榜</text>
		</div>
		<div class="rankingList">
			 <div class="userinfo rangking_item" style="display: none;">
				<div class="userinfo_top">
					<img src="/static/images/down_touxiang.png" class="info_avatar" />
				</div>
				<text class="user_name"></text>
				<text class="rangking_item_text user_text user_text1">暂无排名</text>
				<div class="rangking_item_text user_text user_text2"  onclick="location.href='../mine/invitation.html'">
					<span class="wycb">我要冲榜</span>
					<img src="/static/images/jiantou.gif" class="wycb_img" />
				</div>
			</div>
			<div class="rangking_item">
				<text class="rangking_item_text">排名</text>
				<text class="rangking_item_text">金主</text>
				<text class="rangking_item_text">额度</text>
			</div>
			
		</div>
	</div>

    @extends('agent.layout.main')

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<!-- 微信分享json -->
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
</body>
</html>