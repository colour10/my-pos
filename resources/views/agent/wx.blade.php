<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    @include('agent.layout.subscribe-meta')

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.share-meta')

	<meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 是否首页标记 Start -->
    <meta name="is_index" content="1" />
    <!-- 是否首页标记 End -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />    
	<title>意远</title>
	<link rel="stylesheet" href="/static/css/bootstrap.css">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/main.css') }}">
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/swiper.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/weui.css') }}">
	<style>
		.blankbox{
			margin-top: 6.3rem;
		}
        #homepage {
            padding-bottom: 0;
        }
	</style>
</head>
<body>

	<div id="homepage">
		<!-- 基本信息 --> 
		<div class="blankbox"></div>
		<div class="info infoFixed">
			<div class="bg">
				<img src="/static/images/card_bg.png" class="infoBg nopoint" />
				<img src="/static/images/jbxx_card.png" class="info_card nopoint" />
			</div>
			<div class="info_detail">
				<div class="info_avatar" onclick="{{ route('wxmine') }}">
					<img class='headimgurl' src="{{ $user['original']['headimgurl'] }}" />
				</div>
				<span class="info_name nickname" onclick="window.location.href='{{ route('wxmine') }}'">{{ $user['original']['nickname'] }}</span>
				<span class="info_grade"></span>
			</div>

            <div class="infoNumber">
                <div class="infoJlj" id="jilijin">
                    <div class="text_center money-div" data-href="/agent/wx/mysum" onclick="identityForReal(this);">
                        <span class="info_num num1"></span>
                        <div class="info_title">
                            激励金<span class="title_danwei">(元)</span>
                            <img src="/static/images/up_youjian.png" class="up_youjian">
                        </div>
                    </div>
                </div>
                <div class="infoJlj" id="tuandui">
                    <div class="text_center team-div" onclick="window.location.href='/agent/wx/myteam'">
                        <span class="info_num num2"></span>
                        <div class="info_title">
                            我的团队<span class="title_danwei">(人)</span>
                            <img src="/static/images/up_youjian.png" class="up_youjian">
                        </div>
                    </div>
                </div>
            </div>
		</div>

		<!-- 银行中心 -->
		<div class="cardBox">
			<div class="types">
				<span class="typename">推荐银行</span>
			</div>

			<div class="card_lists"></div>

			<div class="more_card">
				<img src="/static/images/more.png" />
			</div>
		</div>
		
		
		<!-- 用卡攻略 -->
		<div class="ykgl">
			<div class="types">
				<span class="typename">用卡攻略</span>
			</div>
			<img src="/static/images/gonglue.png" class="pthd_img" onclick="window.location.href='{{ route("wxstrategy") }}'">
		</div>
		
		
		
		
		<!-- 平台活动 -->
		<div class="pthd">
			<div class="types">
				<span class="typename">平台活动</span>
			</div>
			<img src="/static/images/banner_two.png" class="pthd_img" onclick="window.location.href='{{ route("wxshare") }}'" data-href="{{ route('wxshare') }}" onclick="identityForReal(this);" />
		</div>
		
		
		<div class="QRcode">
			<!-- <img  class="qrcodewx" src="" /> -->
			<span class="qrcodecontent"></span>
		</div>
		
        @extends('agent.layout.main')

	</div>

    @include('agent.layout.identity-dialog')

    <script src="/static/js/jquery.js"></script>
	<script src="/static/js/preload.js"></script>
	<script>
        var imgs = [
            '/static/images/jbxx_card.png',
            '/static/images/apply.jpg',
            '/static/images/close.png',
            '/static/images/syy.png',
            '/static/images/jlj.png',
            '/static/images/wd.png',
            '/static/images/gonglue.png',
			'/static/images/banner_two.png',
			'/static/images/qrcode_for_gh_7ee7e8b0fa71_860.jpg'
        ];
        //图片预加载
        $.preload(imgs, {
            // 是否有序加载
            order: true,
            minTimer: 3000,
            //每加载完一张执行的方法
            each: function (count) {
               //TODO
            },
            // 加载完所有的图片执行的方法
            end: function () {
              //TODO
            }
        });
	</script>
	<script src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script src="/static/js/swiper.js"></script>
	<script>
		var swiper = new Swiper('.swiper-container', {
			autoplay: true,
	        loop: true,
	        autoplay: {
	            disableOnInteraction: false,
	            autoplayDisableOnInteraction: false
	        }
	        
	    });
		var swiper1 = new Swiper('.swiper-container1', {
			autoplay: true, 
	        loop: true,
	        autoplay: {
	            disableOnInteraction: false,
	            autoplayDisableOnInteraction: false
	        }
	        
	    });
	</script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/main.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/bankinfo.js') }}"></script>
	<!-- 微信分享json -->
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
    <!-- Layer -->
    <script src="/static/layer_mobile/layer.js"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
    <script type="text/javascript">
        /*
        * 意远首页分享页面
        * 注意：
        * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
        * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
        * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
        *
        * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
        * 邮箱地址：weixin-open@qq.com
        * 邮件主题：【微信JS-SDK反馈】具体问题
        * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
        */

        // 拿到参数
        var share_appuuid = "<?php echo $app_id ?>";
        var share_wxurlsecret = "<?php echo $secret ?>";
        var share_openid = "<?php echo $user['id'] ?>";

        wx.config({
            // 测试时打开，正式上线后关闭
            // debug: true,
            debug: false,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: '<?php echo $signPackage["timestamp"];?>',
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            jsApiList: [
                // 所有要调用的 API 都要加到这个列表中
                'onMenuShareAppMessage',
                'onMenuShareTimeline',
                'hideAllNonBaseMenuItem',
                'showMenuItems'
            ]
        });

        // config配置成功后进入
        wx.ready(function () {
            wx.hideAllNonBaseMenuItem();
            wx.showMenuItems({
                menuList: [
                    'menuItem:share:appMessage',
                    'menuItem:share:timeline'
                ]
            });
            // 分享给好友
            wx.onMenuShareAppMessage({
                title: "在线办理信用卡，让我们有福一起享，有财一起发！", // 分享标题
                desc: "我在意远办卡方便快捷，还有钱拿，快来和我一起发财吧~", // 分享描述
                link: "http://" + document.domain + "/agent/wx?wxshare=wxshare"+"&appuuid=" + share_appuuid + "&parentopenId=" + share_openid,
                imgUrl: "http://" + document.domain + "/static/images/icon-logo.png", // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                success: function () {
                    // 成功之后的回调
                    // alert("分享成功");
                    // 如果不为空，说明找不到合伙人，就提示注册
                    if (window.localStorage.getItem('not_agent_errmsg')) {
                        // Layer信息框
                        layer.open({
                            content: window.localStorage.getItem('not_agent_errmsg'),
                            btn: '我知道了'
                        });
                    } else {
                        prompt("分享成功");
                    }
                }
            });
            
            // 分享到朋友圈
            wx.onMenuShareTimeline({
                title: "在线办理信用卡，让我们有福一起享，有财一起发！", // 分享标题
                desc: "我在意远办卡方便快捷，还有钱拿，快来和我一起发财吧~", // 分享描述
                link: "http://" + document.domain + "/agent/wx?wxshare=wxshare"+"&appuuid=" + share_appuuid + "&parentopenId=" + share_openid,
                imgUrl: "http://" + document.domain + "/static/images/icon-logo.png", // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                success: function () {
                    // 成功之后的回调
                    // alert("分享成功");
                    // 如果不为空，说明找不到合伙人，就提示注册
                    if (window.localStorage.getItem('not_agent_errmsg')) {
                        // Layer信息框
                        layer.open({
                            content: window.localStorage.getItem('not_agent_errmsg'),
                            btn: '我知道了'
                        });
                    } else {
                        prompt("分享成功");
                    }
                }
            });
        });
        wx.error(function (res) {
            //打印错误消息。及把 debug:false,设置为debug:true就可以直接在网页上看到弹出的错误提示
        });
    </script>

    @include('agent.layout.footer')

</body>
</html>
