<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>邀请好友</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/share.css') }}">

</head>

<body>
    <div class="main">

        <!-- <div class="banner"><img src="/static/images/banner.jpg" alt="" onclick="toShare();"></div> -->

        <div class="banner"><img src="/static/images/banner.jpg" alt="" onclick="toShare();"></div>

        <dl class="bankLi"></dl>

        <div class="tit"><span>佣金说明</span><i></i></div>
        <p class="wz">
            1.各等级合伙人直推信用卡佣金结算标准:<br>
            <span>【直接合伙人】标准佣金*100% ;</span><br>
            <span>【间接合伙人】5元 ;</span><br>
            2.部分银行核卡需要面签。
        </p>

        <!-- <a class="tg" onclick="toShare();">一键推广</a> -->

        <a class="tg" onclick="toShare();">一键推广</a>

    </div>

    <!-- 分享 Start -->
	<div id="toshare">
		<img src="/static/images/shareimg1.png" class="toshareimg nopoint" />
		<div class="tosharetext">
			<div>点击右上角</div>
			<div>开始分享锁粉吧！</div>
		</div>
		<div class="tosharebtnbox">
			<img src="/static/images/button.png" class="tosharebtn" onclick="sharehidden();" />
		</div>
    </div>
    <!-- 分享 End -->

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script>
        var openid = "<?php echo $user['id'] ?>";
		window.addEventListener('load', function () {
			FastClick.attach(document.body);
			//判断机型为iPhone X
			if(/iphone/gi.test(navigator.userAgent) && (screen.height == 812 && screen.width == 375)){
			}
		}, false);
		function toShare() {
            // 原来的分享逻辑作废
			// document.getElementById("toshare").style.display = "block";
            // document.getElementById("invitation").style.position = "fixed";
            // 判断用户是否已经关注了
            // 如果没有关注
            if (!subscribe(openid)) {
                // 出现弹窗
                popupForbidClose('意远合伙人', '/static/images/qrcode_344.jpg');
            } else {
                // 打开分享
                document.getElementById("toshare").style.display = "block";
            }
		}
		function sharehidden(){
			document.getElementById("toshare").style.display = "none";
			// document.getElementById("invitation").style.position = "relative";
		}

        // 银行列表及佣金
	</script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
	<!-- 微信分享json -->
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
    <script type="text/javascript">

        // 拿到参数
        var appuuid = "<?php echo $app_id ?>";
        var wxurlsecret = "<?php echo $secret ?>";
        var openid = "<?php echo $user['id'] ?>";

        /*
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
        wx.config({
            // 测试时打开，正式上线后关闭
            // debug: true,
            debug: false,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: <?php echo $signPackage["timestamp"];?>,
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
                link: "http://" + document.domain + "/agent/wx?wxshare=wxshare"+"&appuuid=" + appuuid + "&parentopenId=" + openid,
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
                link: "http://" + document.domain + "/agent/wx?wxshare=wxshare"+"&appuuid=" + appuuid + "&parentopenId=" + openid,
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

</body>

</html>
