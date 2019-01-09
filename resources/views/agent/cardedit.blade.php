<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">        
    <title>卡修改</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div id="binding">

        <div class="binding_item changecard_text">
            <text class="binding_item_name">真实姓名：<span id="cardAcctName">{{ $card->agent->name }}</span></text>
        </div>

        <div class="binding_item changecard_text">
            <text class="binding_item_name">身份证号：<span id="cardCredNo">{{ $card->agent->id_number }}</span></text>
        </div>

        <div class="binding_item">
            <text class="binding_item_name">银行卡号</text>
            <input id="card_number" type="number" placeholder="请填写本人的银行卡号码" />
            <img onclick="deletefunc('card_number')" src="/static/images/delete.png" class="delete" />
        </div>

        <div class="binding_item">
            <text class="binding_item_name">手机号码</text>
            <input id="phone_number" type="number" placeholder="请填写银行预留手机号码" oninput="if(value.length>11) value=value.slice(0,11)" />
            <img onclick="deletefunc('phone_number')" src="/static/images/delete.png" class="delete" />
        </div>

        <div class="binding_item">
            <input id="vfyCode" type="number" placeholder="请输入验证码" />
            <input type="button" id="getcode" value="获取验证码" class="hqyzmBtn" />   
        </div>

        <input type="hidden" name="old_card_number" id="old_card_number" value="{{ $card->card_number }}">
        <div id="confirm_binding" type="submit">更换绑卡</div>

	</div>

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>

    <!-- ajax封装函数 Start -->
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/ajax.js') }}"></script>
    <!-- ajax封装函数 End -->

    <script src="/backend/layer/layer.js"></script>
	<script type="text/javascript">
        // 获取验证码逻辑
        $('#getcode').click(function() {

            // 验证银行卡和手机号
            // 银行卡卡号
            if ($('#card_number').val()=="") {
                prompt('请填写银行卡卡号！');
                $('#card_number').focus();
                return false;
            }
            var ifcard = /^(\d{16}|\d{19}|\d{17})$/;
            if (!ifcard.test($('#card_number').val())) {
                prompt('卡号格式不正确，请重新填写！');
                $('#card_number').focus();
                return false;
            }
            // 如果新卡和原来的卡一样，那么就无需修改
            if ($('#card_number').val() == $('#old_card_number').val()) {
                prompt('您输入的卡号已被绑定，请重新输入！');
                $('#card_number').focus();
                return false;
            }
            // 银行预留手机号
            var tel = $('#phone_number').val();
            if ($('#phone_number').val()=="") {
                prompt('请填写银行预留手机号！');
                $('#phone_number').focus();
                return false;
            }

            // 发送验证码短信，并且启用倒计时
            wxcreatecode($('#phone_number').val(), $("#getcode"));
        });
        // 表单提交
        $('#confirm_binding').click(function() {

            // 验证银行卡和手机号
            // 银行卡卡号
            if ($('#card_number').val()=="") {
                prompt('请填写银行卡卡号！');
                $('#card_number').focus();
                return false;
            }
            var ifcard = /^(\d{16}|\d{19}|\d{17})$/;
            if (!ifcard.test($('#card_number').val())) {
                prompt('卡号格式不正确，请重新填写！');
                $('#card_number').focus();
                return false;
            }
            // 如果新卡和原来的卡一样，那么就无需修改
            if ($('#card_number').val() == $('#old_card_number').val()) {
                prompt('您输入的卡号已被绑定，请重新输入！');
                $('#card_number').focus();
                return false;
            }
            // 银行预留手机号
            if ($('#phone_number').val()=="") {
                prompt('请填写银行预留手机号！');
                $('#phone_number').focus();
                return false;
            }

            // 验证码逻辑
            // 验证码不能为空
            var capcha = $('#vfyCode').val();
            if (capcha == '') {
                prompt('请输入验证码');
                $('#vfyCode').focus();
                return false;
            }
            // 判断验证码是否正确
            if (!checkwxcode(capcha)) {
                prompt('验证码不正确');
                return false;
            }

            // 上面不出错，进行四要素认证
            // 否则就进行四要素认证
            // 四要素认证
            var name = "{{ $card->agent->name }}";
            var idcardno = "{{ $card->agent->id_number }}";
            var bankcardno = $('#card_number').val();
            var tel = $('#phone_number').val();
            var openid = window.localStorage.getItem('openid');
            if (!checkbankcard(name, idcardno, bankcardno, tel, openid)) {
                prompt('四要素核查不一致，绑卡失败！');
                return false;
            }

            // 如果核查一致，那么逻辑往下进行换卡绑定
            // ajax提交
            defaultajax('post', "{{ route('wxupdatecard', ['id' => $card->id]) }}", {
                "card_number": bankcardno,
                "_token": "{{ csrf_token() }}",
                "id": "{{ $card->id }}",
                "_method": "PUT",
            }, "{{ route('wxrankcard') }}");
            // 返回假，禁止自动跳转
            return false;
        });
	</script>
</body>
</html>