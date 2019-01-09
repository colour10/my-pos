<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>提现绑卡</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div id="binding">

		<div class="binding_item">
			<text class="binding_item_name">真实姓名</text>
			<input id="realName" type="text" placeholder="请填写本人真实姓名" readonly="readonly" />
        </div>
        
		<div class="binding_item">
			<text class="binding_item_name">身份证号</text>
			<input id="IDnumber" type="text" placeholder="请填写本人身份证号码" readonly="readonly" />
        </div>

		<!-- <div class="binding_item">
			<text class="binding_item_name">身份证号</text>
			<input id="IDnumber" type="text" onkeyup="value=value.replace(/[^\w\.\/]/ig,'')" oninput="identityCheck('IDnumber',value);"  placeholder="请填写本人身份证号码"  />
			<img onclick="deletefunc('IDnumber')" src="/static/images/delete.png" class="delete" name="id_number" />
        </div> -->
        
		<div class="binding_item">
			<text class="binding_item_name">银行卡号</text>
			<input id="card_number" type="number" 
			onkeyup="this.value=this.value.replace(/[^\d]/g,'') " 
			onafterpaste="this.value=this.value.replace(/[^\d]/g,'') " 
			placeholder="请填写本人的银行卡号码" />
			<img onclick="deletefunc('card_number')" src="/static/images/delete.png" class="delete" name="card_number" />
        </div>
        
		<div class="binding_item">
			<text class="binding_item_name">手机号码</text>
			<input id="phone_number" type="number" 
			onkeyup="this.value=this.value.replace(/[^\d]/g,'') " 
			onafterpaste="this.value=this.value.replace(/[^\d]/g,'') " 
			placeholder="请填写银行预留号码"  oninput="if(value.length>11) value=value.slice(0,11)" name="mobile" />
			<img onclick="deletefunc('phone_number')" src="/static/images/delete.png" class="delete" />
		</div>
        
        <div id="confirm_binding">确认绑卡</div>

    </div>

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/vue.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>

    <!-- ajax封装函数 Start -->
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/ajax.js') }}"></script>
    <!-- ajax封装函数 End -->

    <script type="text/javascript" src="/backend/layer/layer.js"></script>
    <script type="text/javascript">
        // 绑卡
        // 如果已经绑定，那么就拒绝重复提交
        // 存入openid
        var openid = window.localStorage.getItem('openid');
        if (checktiecard(openid)) {
            prompt('您已经绑卡了，请不要重复操作！');
            // 跳转到卡列表页面
            setTimeout('location.href="{{ route("wxrankcard") }}"', 3000);
        }

        // 表单提交
        $('#confirm_binding').click(function() {

            // 验证姓名
            if ($('#realName').val()==""){
                prompt('请填写姓名！');
                $('#realName').focus();
                return false;
            }
            var name = /^[\u4e00-\u9fa5]{2,6}$/;
            var realName = $('#realName').val();
            if (!name.test(realName)) {
                prompt('姓名格式不正确，请重新填写！');
                $('#realName').focus();
                return false;
            }

            // 身份证号码
            if ($('#IDnumber').val()=="") {
                prompt('请填写身份证号码！');
                $('#IDnumber').focus();
                return false;
            }
            var ifid = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if (!ifid.test($('#IDnumber').val())){
                prompt('身份证号码格式不正确，请重新填写！');
                $('#IDnumber').focus();
                return false;
            }

            // 银行卡卡号
            if ($('#card_number').val()=="") {
                prompt('请填写银行卡卡号！');
                $('#card_number').focus();
                return false;
            }
            var ifcard = /^(\d{16}|\d{19}|\d{17})$/;
            if (!ifcard.test($('#card_number').val())){
                prompt('卡号格式不正确，请重新填写！');
                $('#card_number').focus();
                return false;
            }

            // 手机号码
            if ($('#phone_number').val()=="") {
                prompt('请填写银行预留手机！');
                $('#phone_number').focus();
                return false;
            }
            var mob = /^1[3,4,5,6,7,8]\d{9}$/;
            if (!mob.test($('#phone_number').val())){
                prompt('银行预留手机格式不正确，请重新填写！');
                $('#phone_number').focus();
                return false;
            }

            // 查找用户输入的姓名和数据库录入的是否一致，否则禁止绑卡
            if (!checkUser(realName, openid)) {
                prompt('您只能绑定自己的银行卡');
                return false;
            }

            // 上面不出错，进行四要素认证
            // 否则就进行四要素认证
            // 四要素认证
            var name = realName;
            var idcardno = $('#IDnumber').val();
            var bankcardno = $('#card_number').val();
            var tel = $('#phone_number').val();
            if (!checkbankcard(name, idcardno, bankcardno, tel, openid)) {
                prompt('四要素核查不一致，绑卡失败！');
                return false;
            }

            // 四要素验证通过之后，开始绑卡
            // ajax提交
            defaultajax('post', "{{ route('wxaddcardstore') }}", {
                "card_number": bankcardno,
                "_token": "{{ csrf_token() }}",
                'openid': openid,
            }, "{{ route('wxrankcard') }}");
            // 返回假，禁止自动跳转
            return false;

        });


	</script>

</body>
</html>