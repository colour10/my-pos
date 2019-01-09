<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>我的银行卡</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
    <style>
        /* 新增css */
        .card_number {
            font-size: .98rem;
            position: absolute;
            right: 1.6rem;
            top: 2.69rem;
            color: #fff;
            text-align:right;
            /* left:0;
            top:0; */
        }
        .card_box em {
            position: absolute;
            left: 1.2rem;
            top: 0.4rem;
            z-index: 9999;
        }
        .card_box em img {
            display:block;
            width: 350%;
            margin:0 auto;
        }
        .card_box em span {
            font-size: .98rem;
            color: #fff;
            font-style:normal;
            font-family: "微软雅黑";
        }
    </style>
</head>
<body>
	<div id="rankcard">

        <!--无银行卡 start-->
		<div id="card_non_existe">
		</div>
        <!--无银行卡 end-->

        <!--有银行卡 start-->
		<div id="card_exist">
		</div>
        <!--有银行卡 end-->

	</div>

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
    <script type="text/javascript">
        // 逻辑
        var openid = window.localStorage.getItem('openid');
        // 取出默认卡模型
        var response = getFirstCard(openid);
        if (!response) {
            var html = "";
            html+='<img class="non_img" src="/static/images/wuyinhang.png"/>';
            html+='<div class="changeCard_box">';
            html+='<div class="changeCardBtn">';
            html+='<div class="change_item addCard">';
            html+='<img src="/static/images/add.png" class="changeImg"></img>';
            html+='<span class="change_text">添加银行卡</span>';
            html+='</div></div></div>';
            $("#card_non_existe").html(html);
            $(".addCard").click(function() {
                window.location.href='{{ route("wxaddcard") }}';
            });
        } else {
            var html = "";
            html+='<div class="card_box">';
            html+='<img class="cardimg" src="/static/images/defaultcard.png" />';
            html+='<em>';
            html+='<span class="bankname">'+response.data.bankName+'</span>';
            html+='</em>';
            html+='<span class="card_number">'+response.data.new_card_number+'</span>';
            html+='</div>';
            html+='<div class="changeCard_box">';
            html+='<div class="changeCardBtn">';
            html+='<div class="change_item" id ="changebank" >';
            html+='<img src="/static/images/change.png" class="changeImg"></img>';
            html+='<span class="change_text" onclick="window.location.href=\'/agent/wx/'+ response.data.id +'/wxeditcard\'">更换银行卡</span>';
            html+='</div></div></div>';
            $("#card_exist").html(html);
        }
    </script>
</body>
</html>