<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	@include('agent.layout.subscribe-meta')

	@include('agent.layout.csrf')

	@include('agent.layout.write-openid')

	@include('agent.layout.identity-meta')

	@include('agent.layout.share-meta')

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>分享</title>
	<link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/apply_card.css') }}">
	<style>
		body{
			background: #f6f7f9;
		}
	</style>
</head>
<body>
	<div class="share">
		<div class="share_top">
			<div>
				<div class="share_title"></div>
				<div class="share_copy" data-clipboard-action="copy" data-clipboard-target="#foo">一键复制</div>
			</div>
			
			<input id="foo" type="text" value="" readonly="readonly" />
		</div>
		<div class="share_QRcode">
			<center>
				<div id="savaImg" class="download_img"></div>
				<a id="downloadLink" style="display:none"></a>
			</center>
			<div class="share_QRcodeImg">
				<!-- 用于生成二维码的容器 -->
				<div class="share_QRcodeText">
					扫码&nbsp;&nbsp;>&nbsp;&nbsp;填写信息&nbsp;&nbsp;>&nbsp;&nbsp;极速办卡
				</div>
				<div class="share_saveImg" id="btnsavaImg">长按保存二维码，分享办卡如此简单。</div>
			</div>
		</div>
	</div>

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/clipboard.min.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/qrcode.js') }}"></script>
	<script>
        // 首先判断系统是否存在这个用户，如果不存在，就提示注册为合伙人
        // 微信openid
        var openid = window.localStorage.getItem('openid');

		// 获取参数并拼接链接部分
		var bankId = GetQueryString("bankId");
        
        // 分别跳转到不同的页面
        if (bankId === null) {
            var tarUrl = 'http://' + document.domain + '/agent/wx?invite_openid='+openid+'&authflag=authflag';
        } else {
            var tarUrl = 'http://' + document.domain + '/agent/wx/applycard/'+bankId+'?invite_openid='+openid+'&authflag=authflag';
        }


		$('#foo').val(tarUrl);
		// 生成二维码部分
		var qrcode = new QRCode(document.getElementById("savaImg"), {
		    text: tarUrl,
		    width: 200,
		    height: 200,
		    colorDark : "#333366",   
		    colorLight : "#ffffff",
		    correctLevel : QRCode.CorrectLevel.H
		});
		/*一键下载*/
		function downloadClick() {
			// 获取base64的图片节点
			var img = document.getElementById('savaImg').getElementsByTagName('img')[0];
			// 构建画布
			var canvas = document.createElement('canvas');
			canvas.width = img.width;
			canvas.height = img.height;
			canvas.getContext('2d').drawImage(img, 0, 0);
			// 构造url
			url = canvas.toDataURL('image/png');
			// 构造a标签并模拟点击
			var downloadLink = document.getElementById('downloadLink');
			downloadLink.setAttribute('href', url);
			downloadLink.setAttribute('download', 'myqrcode.png');
			downloadLink.click();
		}
		
		var clipboard = new ClipboardJS('.share_copy');
	
	    clipboard.on('success', function(e) {
	        console.log(e);
            prompt("已粘贴到剪切板");
            return;
	    });
	
	    clipboard.on('error', function(e) {
	        console.log(e);
	    });
	    
	</script>
</body>
</html>