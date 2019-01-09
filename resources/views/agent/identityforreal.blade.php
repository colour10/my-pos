<!DOCTYPE html>

<html>

<head>
    <title>注册会员</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')
    
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/static/css/custom-font.css" />
    <link rel="stylesheet" href="/static/css/swiper-3.3.1.min.css" />
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/backend/layer/layer.js"></script>
    <style type="text/css">
        @font-face {
            font-family: DINPro-Bold;
            src: url('/static/fonts/DINPro-Bold.otf');
        }
        
        @font-face {
            font-family: DINPro-Regular;
            src: url('/static/fonts/DINPro-Regular.otf');
        }
        
        .zy-repayment {
            width: 80%;
            height: 40px;
            line-height: 40px;
            color: white;
            background-color: #Fed500;
            border: none;
            font-size: 16px;
            border-radius: 3px;
            margin-right: 10%;
        }
        
        .thetitle {
            height: 25px;
            line-height: 25px;
            text-align: left;
            font-size: 14px;
            color: #333;
            background: #eee;
            margin-top: 10px;
            padding-left: 10px;
            padding-bottom: 10px;
            padding-top:10px;
        }
        
        .xian {
            height: 1px;
            display: block;
        }
        
        body {
            margin: 0 auto;
            background: #eee;
        }
        
        footer {
            margin: 0 auto;
            background-color: #E6E6E6;
        }
        
        .fontStyle-field {
            font-size: 14px;
            float: left;
            color: #333;
            line-height: 50px;
            display: block;
            width: 60px;
            text-align: left;
        }
        
        .fontStyle-value {
            color: #999;
            text-align: right;
            line-height: 50px;
            border: none;
            outline: none;
            float: right;
            font-size: 13px;
        }
        
        .upload {
            opacity: 0;
            filter: alpha(opacity=0);
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 9;
            border: 1px solid red;
        }
        
        .input_pic {
            width: 70%;
            height: 33px;
            outline: none;
            color: #00A0E6;
            border: none;
            /* border-bottom: 1px solid #eee; */
            font-size: 14px;
            line-height: 35px;
            text-align: center;
            ime-mode: disabled;
            margin-left: 15%;
        }
        
        .input_pic::-webkit-input-placeholder {
            color: #999;
            font-size: 13px;
        }
        
        #infoEnter img {
            display: block;
        }
        
        #addcardTX p {
            font-size: 12px;
            color: #999;
            width: 90%;
            margin-left: 5%;
        }
        
        .backdrop {
            width: 100%;
            height: 100%;
            position: fixed;
            z-index: 99999;
            top: 0px;
            left: 0px;
            background: black;
            opacity: 0.7;
            display: none;
        }
        
        .popup {
            width: 80%;
            height: 130px;
            position: fixed;
            z-index: 100000;
            top: 15%;
            margin-left: 10%;
            border-radius: 3px;
            background: #fff;
            display: none;
        }
        
        #popupOfsure {
            width: 90%;
            margin-left: 5%;
        }
        
        #popupOfsure .top {
            padding: 10px 20px;
        }
        
        #popupOfsure .top .title {
            height: 30px;
            line-height: 30px;
            text-align: center;
            font-size: 14px;
            color: #333;
        }
        
        #popupOfsure .top .ftitle {
            height: 32px;
            line-height: 16px;
            text-align: center;
            font-size: 12px;
            color: #ff4400;
            width: 80%;
            margin-left: 10%;
        }
        
        #popupOfsure .top .num {
            width: 70%;
            float: right;
            height: 30px;
            line-height: 30px;
            text-align: left;
            /* font-size: 16px; */
            font-size:0.8em;
            font-family: DINPro-Bold;
            color: #ff4400;
        }
        
        #popupOfsure .bottom {}
        
        #popupOfsure .bottom .yesBtn {
            width: 49%;
            line-height: 40px;
            float: right;
            text-align: center;
            border-radius: 5px;
            border-left: 1px solid #333;
            border-top: 1px solid #333;
        }
        
        #popupOfsure .bottom .noBtn {
            width: 49%;
            float: left;
            line-height: 40px;
            text-align: center;
            border-radius: 5px;
            border-right: 1px solid #333;
            border-top: 1px solid #333;
        }
        
        .bottom .next {
            width: 45%;
            height: 35px;
            line-height: 35px;
            text-align: center;
            font-size: 13px;
            float: right;
            border-radius: 3px;
            border: 1px solid #fed500;
            background: #fed500;
            color: #fff;
        }
        
        .bottom .quit {
            width: 45%;
            height: 35px;
            line-height: 35px;
            text-align: center;
            font-size: 13px;
            float: left;
            border-radius: 3px;
            border: 1px solid #fed500;
            color: #fed500;
        }
        
        .sureTitle {
            line-height: 30px;
            font-size: 14px;
            font-weight: normal;
            color: #999;
            width: 60px;
            float: left;
            text-align: right;
        }
        /*prompt_box*/
        .prompt_box {
            display: none;
            position: fixed;
            top: 0;
            width: 100%;
            height: 100%;
            padding-top: 10rem;
            text-align: center;
            z-index: 9999999999;
        }

        .prompt_box span {
            display: inline-block;
            border: 1px solid #fc0;
            height: 1.7rem;
            line-height: 1.7rem;
            padding: 0 1rem;
            color: #000;
            font-size: .64rem;
            margin: 0 auto;
            border-radius: .85rem;
            background: #fc0;
        }
    </style>
    <script type="text/javascript">
        $(function() {
            var browser = {
                versions: function() {
                    var u = navigator.userAgent,
                        app = navigator.appVersion;
                    return { //移动终端浏览器版本信息
                        webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                        gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
                        mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                        ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                        android: u.indexOf('Android') > -1 ||
                            u.indexOf('Linux') > -1, //android终端或uc浏览器
                        qqbrowser: u.indexOf(" QQ") > -1, //qq内置浏览器
                    };
                }(),
                language: (navigator.browserLanguage || navigator.language)
                    .toLowerCase()
            };
            $('#agreeDiv').width($(window).width() - 20 + "px");
            $('#js03').width($("#agreeDiv").width() - 70 + "px");
        });

        function isWeiXin() {
            var ua = window.navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == 'micromessenger') {
                return true;
            } else {
                return false;
            }
        }

        function agree(obj) {
            var agree = $("#agree").val();
            if (agree == "true") {
                $("#agree").val(false);
                $(obj).attr("src", "/static/images/b775b12f-2df9-470b-86eb-4161cd555222.png");
            } else {
                $("#agree").val(true);
                $(obj).attr("src", "/static/images/048df3ec-521c-415c-af7e-5410e8741417.png");
            }
        }

        //照片提交
        function img_tijiao(flag) {
            $("#flag").val(flag);
            $("#loadingToast").show();
            $("#frms").submit();
        }

        function IsMobile(text) {
            var _emp = /^\s*|\s*$/g;
            text = text.replace(_emp, "");
            var reg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;

            if (reg.test(text)) {
                return true;
            }
            return false;
        }

        var ting1, ting2;


        // 手机号和身份证修改验证逻辑
        function sendDXMsg() {

            // 获取姓名
            var beforeName = $('#beforeName').val();
            var realName = $('#realName').val();

            // 姓名不能为空
            if (realName != null && realName.length > 0) {
                realName = realName.trim();
            }
            // 姓名正则验证
            if (realName == '' || realName == 'null' || realName == null) {
                prompt('姓名不能为空');
                return;
            } else {
                var reg1 = /^[\u4e00-\u9fa5-· ]{2,20}$/;
                if (!reg1.test(realName)) {
                    prompt("请正确输入姓名！");
                    return;
                }
            }

            // 获取原来和新输入的身份证
            var yl_aiIdCard = window.localStorage.getItem('agent_id_number');
            var aiIdCard = $("#aiIdCard").val();

            // 身份证不能为空
            if (aiIdCard != null && aiIdCard.length > 0) {
                aiIdCard = aiIdCard.trim();
            }
            // 身份证正则验证
            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if (reg.test(aiIdCard) === false) {
                prompt("请输入正确的身份证号！");
                return;
            } else {
                if (aiIdCard.indexOf(' ') != -1) {
                    prompt("请输入正确的身份证号！");
                    return;
                }

                var year = parseInt(aiIdCard.substring(6, 10));
                if (year > 2000) {
                    prompt("您的年龄尚未满足平台要求！");
                    return;
                }
            }

            // 身份证数据库验证
            if (!identityCheck(aiIdCard)) {
                prompt("抱歉，该身份证已被其他人使用！");
                return;
            }

            // 获取电话号码
            var beforePhone = window.localStorage.getItem('agent_mobile');
            var phone = $('#telPhone').val();
            
            // 手机号验证
            // 手机号正则验证
            if (!IsMobile(phone)) {
                prompt("亲，请输入正确的手机号!");
                return;
            }

            // 手机号数据库验证
            if (!phoneNumBelongerCheck(phone)) {
                prompt('抱歉，该手机号已被其他人使用！');
                return;
            }
            
            // 姓名验证逻辑
            if (realName != beforeName) {
                //获取验证码
                toSendCode(phone);
                // 必须加return，完成发送逻辑
                return;
            }

            // 身份证验证逻辑
            if (aiIdCard != yl_aiIdCard) {
                //获取验证码
                toSendCode(phone);
                // 必须加return，完成发送逻辑
                return;
            }

            // 手机号修改逻辑
            if (beforePhone != phone) {
                //获取验证码
                toSendCode(phone);
                // 必须加return，完成发送逻辑
                return;
            }

            // 如果以上都没有触发，说明没有做任何修改，那么无需做任何处理，保持现状即可
        }


        // 获取验证码
        // 发送验证码短信
        function toSendCode(mobile) {
            $.get("{{ route('wxcreatecode') }}", function(response) {
                // 打印验证码
                console.log('-------取出验证码 start-------');
                console.log(response);
                console.log('-------取出验证码 end-------');
                // 发送验证码，测试期间可以先注释
                sendmsg(mobile, '2002', response);
                // 前端状态显示倒计时
                countingdown();
            });
        }

        // 然后进行倒计时120秒
        var countdown = 120;
        function countingdown() {
            var obj = $("#getcode");
            settime(obj);
        }
        // 发送验证码倒计时
        function settime(obj) {
            if (countdown == 0) {
                // 恢复点击
                obj.attr("onclick", "sendDXMsg()");
                obj.html("获取验证码");
                countdown = 120;
                return;
            } else {
                obj.html("<span id='countdown'>"+countdown+"</span>");
                obj.css("background-color", "#7b7b7b");
                // 禁止点击
                obj.attr("onclick", "");
                countdown--;
            } 
            setTimeout(function() {
                settime(obj) 
            }, 1000)
        }

        // 提交逻辑
        function submitRZ(obj) {
            var infoId = $("#infoId").val();
            var realName = $("#realName").val();
            // 用户输入的手机号
            var telPhone = $("#telPhone").val();
            var phoneChange = $("#phoneChange").val();
            var beforePhone = $("#beforePhone").val();
            if (telPhone != null && telPhone.length > 0) {
                telPhone = telPhone.trim();
            }

            var code = $('#code').val();
            //修改电话号码时获取验证码
            if (phoneChange == 0) {
                if (telPhone != beforePhone && telPhone.length == 11) {
                    code = $('#code').val();
                    if (code != null && code.length > 0) {
                        code = code.trim();
                    }
                }
            }
            //修改身份证时获取验证码
            var yl_aiIdCard = "";
            // 用户输入的身份证号
            var aiIdCard = $("#aiIdCard").val();
            if (yl_aiIdCard == "" && aiIdCard != "") {
                code = $('#code').val();
                if (code == null || code == "") {
                    prompt("请填写验证码！");
                    return;
                }
                if (code.length != 4) {
                    prompt("验证码错误！");
                    return;
                }
                if (code != null && code.length > 0) {
                    code = code.trim();
                }
            }

            var wxNo = $("#wxNo").val();
            var wxImg = $('#img1_input').val();
            var aiIdCard = $("#aiIdCard").val();

            if (aiIdCard != null && aiIdCard.length > 0) {
                aiIdCard = aiIdCard.trim();
            }
            if (wxImg != null && wxImg.length > 0) {
                wxImg = wxImg.trim();
            }
            if (realName != null && realName.length > 0) {
                realName = realName.trim();
            }

            if (wxNo != null && wxNo.length > 0) {
                wxNo = wxNo.trim();
            }

            if (realName == null || realName == "") {
                prompt("请填写姓名！");
                return;
            } else {
                var reg1 = /^[\u4e00-\u9fa5-· ]{2,20}$/;
                if (!reg1.test(realName)) {
                    prompt("请正确输入姓名！");
                    return;
                }
            }

            if (wxNo != null && wxNo.length > 0) {
                wxNo = wxNo.trim();
            }

            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if (reg.test(aiIdCard) === false) {
                prompt("请输入正确的身份证号！");
                return;
            } else {
                if (aiIdCard.indexOf(' ') != -1) {
                    prompt("请输入正确的身份证号！");
                    return;
                }

                var year = parseInt(aiIdCard.substring(6, 10));
                if (year > 2000) {
                    prompt("您的年龄尚未满足平台要求！");
                    return;
                }
            }

            if (telPhone == null || telPhone == "") {
                prompt("请填写手机号！");
                return;
            }
            if (telPhone.length != 11) {
                prompt("请填写正确手机号！");
                return;
            }

            var chk = $("#agree").val();
            if (chk == "false") {
                prompt("请仔细阅读《意远平台服务协议》");
                return;
            }

            $(obj).attr("onclick", ""); //防止二次点击

            // 验证码逻辑
            // 判断登录验证码是否正确
            // 如果验证码开关打开了，那么就验证
            if ($('#code_input').is(':visible')) {
                if (!checkwxcode(code)) {
                    prompt(response.msg);
                    // 放开点击
                    $(obj).attr("onclick", "submitRZ(this)");
                    // 返回
                    return false;
                } else {
                    // 提交
                    ajaxSubmit(realName, aiIdCard, telPhone);                    
                }
                // 禁止往下执行
                return false;
            }
            // ajax提交
            ajaxSubmit(realName, aiIdCard, telPhone);
        }

        // ajax提交单独函数
        function ajaxSubmit(name, id_number, mobile) {
            // 如果都通过了，那么就ajax提交
            $.ajax({
                type: 'post',
                url: "{{ route('wxidentityforrealstore') }}",
                data: {
                    'name': name,
                    'id_number': id_number,
                    'mobile': mobile,
                    '_token': "{{ csrf_token() }}",
                    'openid': window.localStorage.getItem('openid'),
                    // parentopenid在首页就写入了
                    'parentopenid': window.localStorage.getItem('invite_openid'),
                },
                dataType: 'json',
                timeout: 99999,
                // processData: false,
                // contentType: false,
                beforeSend: function() {
                    index = layer.load(1, {
                        shade: [0.5,'#fff'] //0.1透明度的白色背景
                    });
                },
                complete: function () {
                    layer.close(index);
                },                
                success: function(data) {

                    // 测试数据
                    console.log('-------打印实名认证返回结果 start-------');
                    console.log(data);
                    console.log('-------打印实名认证返回结果 end-------');

                    if (data.code == 0) {

                        // 成功返回
                        prompt(data.msg);

                        // 使用localStorage存储用户信息
                        // 如果有登录错误的记录，那么就清除
                        var storage = window.localStorage;
                        if (storage.getItem('error_login_ip') !== null) {
                            storage.removeItem('error_login_ip');
                        }
                        if (storage.getItem('error_login_count') !== null) {
                            storage.removeItem('error_login_count');
                        }
                        // 等级重新写入
                        storage.setItem('level', '普通');

                        // 记录为已认证，0代表错误代码为0，表示已认证；1代表错误代码为1，表示未认证或认证失败，Changing
                        storage.setItem('is_real', '0');                      

                        // 3000毫秒后跳转
                        setTimeout("window.location.href='{{ route("wxindex") }}'", 3000);

                    } else {
                        // 失败返回
                        prompt(data.msg);
                        // 放开点击
                        $("#next").attr("onclick", "submitRZ(this)");
                    }
                },
                error: function(data) {
                    // 捕捉错误
                    if (data.status == 422) {
                        var jsonObj = JSON.parse(data.responseText);
                        var errors = jsonObj.errors;
                        for (var item in errors) {
                            for (var i=0, len=errors[item].length; i<len; i++) {
                                prompt(errors[item][i]);
                                // 放开点击
                                $("#next").attr("onclick", "submitRZ(this)");
                            }
                        }
                    } else {
                        // 其他错误就重新登录
                        prompt('网络错误，请重新认证！');
                        // 3000毫秒后跳转
                        setTimeout("window.location.href='{{ route("wxidentityforreal") }}'", 3000);
                    }
                },
            });

            // 禁止自动跳转
            return false;            
        }

        function checkAccount(str) {
            var x = /^[0-9a-zA-Z]{3,15}$/;
            return x.test(str);
        }

        function checkPwd(str) {
            var x = /^[A-Za-z0-9_]{6,15}$/;
            return x.test(str);
        }

        function phoneChange() {
            prompt("手机号每月只能修改一次！");
        }

        //input keydown事件
        $(document).ready(function() {
            // 姓名修改时显示获取验证码
            $("#realName").keydown(function() {
                $("#code_input").show();
            });
            $("#realName").keyup(function() {
                $("#code_input").show();
            });
            // 手机号修改时显示获取验证码
            $("#telPhone").keydown(function() {
                $("#code_input").show();
            });
            $("#telPhone").keyup(function() {
                $("#code_input").show();
            });
            //身份证修改时显示获取验证码按钮
            $("#aiIdCard").keydown(function() {
                $("#code_input").show();
            });
            $("#aiIdCard").keyup(function() {
                $("#code_input").show();
            });
        });

        function showPopup() {
            var realName = $("#realName").val();
            var telPhone = $("#telPhone").val();
            var code = $('#code').val();
            var wxNo = $("#wxNo").val();
            var wxImg = $('#img1_input').val();
            var aiIdCard = $("#aiIdCard").val();

            // 判断验证码是否正确
            if (code != null && code.length > 0) {
                code = code.trim();
            }
            if (aiIdCard != null && aiIdCard.length > 0) {
                aiIdCard = aiIdCard.trim();
            }
            if (wxImg != null && wxImg.length > 0) {
                wxImg = wxImg.trim();
            }
            if (realName != null && realName.length > 0) {
                realName = realName.trim();
            }
            if (telPhone != null && telPhone.length > 0) {
                telPhone = telPhone.trim();
            }
            if (wxNo != null && wxNo.length > 0) {
                wxNo = wxNo.trim();
            }

            if (realName == null || realName == "") {
                prompt("请填写姓名！");
                return;
            } else {
                var reg1 = /^[\u4e00-\u9fa5-· ]{2,20}$/;
                if (!reg1.test(realName)) {
                    prompt("请正确输入姓名！");
                    return;
                }
            }

            if (wxNo != null && wxNo.length > 0) {
                wxNo = wxNo.trim();
            }

            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if (reg.test(aiIdCard) === false) {
                prompt("请输入正确的身份证号！");
                return;
            } else {
                if (aiIdCard.indexOf(' ') != -1) {
                    prompt("请输入正确的身份证号！");
                    return;
                }

                var year = parseInt(aiIdCard.substring(6, 10));
                if (year > 2000) {
                    prompt("您的年龄尚未满足平台要求！");
                    return;
                }
            }

            if (telPhone == null || telPhone == "") {
                prompt("请填写手机号！");
                return;
            }
            if (telPhone.length != 11) {
                prompt("请填写正确手机号！");
                return;
            }

            if ($('#code_input').is(':visible')) {
                if (code == null || code == "") {
                    prompt("请填写验证码！");
                    return;
                }
            } else {
                prompt("您没有进行任何修改");
                return;
            }

            if (code.length != 4) {
                prompt("验证码错误！");
                return;
            }

            var idcard = $("#aiIdCard").val().trim();
            var name = $("#realName").val().trim();
            idcard = idcard.substring(0, 6) + " " + idcard.substring(6, 14) + " " + idcard.substring(14, idcard.length);
            $("#bigIdcard").html(idcard);
            $("#bigName").html(name);
            $("#bigPhone").html(telPhone);
            $('#backdropOfsure').show();
            $('#popupOfsure').show();
            $('.noBtn').width(($("#popupOfsure").find(".bottom").width() - 4) * 0.5 + "px");
            $('.yesBtn').width(($("#popupOfsure").find(".bottom").width() - 4) * 0.5 + "px");
        }

        function hidePopup() {
            $('#backdropOfsure').hide();
            $('#popupOfsure').hide();
        }
    </script>
</head>

<body>
    <input type="hidden" id="top_grade" value="">
    <input type="hidden" value="14150721" id="infoId" />
    <input type="hidden" value="{{ $user['nickname'] }}" id="infoNickname" />
    <input type="hidden" value="0" id="infoPlatform" />
    <input type="hidden" value="{{ $user['id'] }}" id="infoSpopenid" />
    <input type="hidden" value="0" id="infoSubscribe" />
    <input type="hidden" value="-1" id="infoIdentity" />
    <input type="hidden" value="0" id="result" />
    <input type="hidden" value="6" id="returnUrl">
    <!-- 0代表目前修改的次数为0，可以随时修改手机 -->
    <input type="hidden" value="0" id="phoneChange">
    <input type="hidden" value="" id="type">
    <input type="hidden" value="" id="beforePhone">
    <input type="hidden" value="0" id="wxState">
    <input type="hidden" value="" id="beforeName">


    <!-- <header>
		<img style="display: block;width: 100%;" src="/static/images/09b4c40037f9407ea628066dc0a41d33.gif">
	</header> -->
    <!-- 	http://liuliang-10002703.image.myqcloud.com/08894ae6-5eb3-4098-a93e-62a6a976f8c6 -->
    <header>
        <div id="backdrop" align="center">
            <div style="height: 30px;"></div>
            <img id="QRCode" class="zy-avatar" style="height:70px;width:70px; border-radius:50px;-moz-box-shadow:0px 0px 20px 0px #E6E6E6;-webkit-box-shadow:10px 10px 20px 0px #E6E6E6;box-shadow:0 0 0px 6px rgba(230, 230, 230, 0.1);" src="{{ $user['avatar'] }}">
            <div>
                <span style="font-size: 16px; line-height: 60px;color: #999;">{{ $user['nickname'] }}</span>
            </div>
            <div style="height: 10px;"></div>
        </div>
    </header>

    <!-- 账号关联 -->
    <!-- <div id="guanlian" style="margin-left: 10px;margin-right:10px;display: none;">
        <div onclick="guanlian(14150721);" class="input_pic" style="width: 31%;margin-left: 3%;margin-top:7px; float: right; background: #fed500;
			color: #fff; border: 0px solid #fed500; border-radius:3px;">去关联账号</div>
        <div style="clear: both;"></div>
    </div> -->

    <div id="content" style="background: #fff;margin-left: 10px">
        <div class="thetitle">填写个人申请资料</div>
        <div style="width: 80%;margin-left: 10%;" align="center">
            <div style="height: 10px"></div>
            <div id="infoEnter">
                <input id="myinfoId" type="hidden" value="14150721">

                <img class="xian" style="width: 100%;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">
                <span class="fontStyle-field">姓名</span>
                <input id="realName" onfocus="updateFontSize(2)" onblur="reduceFontSize(2)" class="fontStyle-value" placeholder="请输入您的姓名" value="" type="text">
                <img class="xian" style="width: 100%;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">
                <span class="fontStyle-field">身份证号</span>
                <input id="aiIdCard" name="aiIdCard" onfocus="updateFontSize(2)" onblur="yzIdCard()" class="fontStyle-value" placeholder="请输入您的身份证" value="" type="text">
                <img style="width: 100%;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">

                <span class="fontStyle-field">手机号</span>
                <input id="telPhone" maxlength="11" onfocus="updateFontSize(4)" onblur="reduceFontSize(4)" class="fontStyle-value" placeholder="请输入常用手机号码" value="" oninput="" type="text">
                <img class="xian" style="width: 100%;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">
                <div id="code_input" style="display: none">
                    <input id="code" class="input_pic" style="width: 50%; float: left;margin-left: 0%; text-align: left;height: 50px" type="tel" placeholder="请输入验证码" maxlength="4" />
                    <div id="getcode" class="input_pic" style="width: 31%; margin-left: 3%;margin-top:7px; float: right; background: #fed500; color: #fff; border: 0px solid #fed500; border-radius:3px;" onclick="sendDXMsg();">获取验证码</div>
                    <img class="xian" style="width: 100%;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">
                </div>
                <div class="clear_10"></div>
                <div style="height: 20px;clear: both;"></div>
            </div>
        </div>
    </div>


    <div id="agreeDiv" style="margin-left: 10px;margin-top: 10px;">
        <div style="width:100%;background: #fff">
            <input type="hidden" id="agree" value="true">
            <div style="width: 50px;float: left;">
                <img style="width:40px;float: right;margin-top: 8px;" onclick="agree(this)" src="/static/images/048df3ec-521c-415c-af7e-5410e8741417.png">
            </div>
            <div id="js03" style="float: left;font-size:12px;color: #999;line-height: 18px;padding: 10px 10px 10px 10px;">我已认真阅读并完全同意：<a style="color: #ffcc00;padding: 0px;margin: 0px" href="{{ route('wxagreement') }}">《意远平台服务协议》</a>的所有条款</div>
            <div style="clear: both;"></div>
            <div class="thetitle">注意事项</div>
            <div id="addcardTX" style="background: #fff;text-align: justify;">
                <div style="height: 10px;"></div>
                <p>1、必须填写真实个人信息，否则无法在平台办理任何业务。</p>
                <!-- <p>2、收到办卡审核通知短信，第2个工作日查到进度并提示“等待工作人员审核”，将有机会拆红包。</p> -->
                <p>2、在意远申请信用卡不收取任何费用，凡是索取均为欺诈，请不要相信！</p>
                <p>3、会员资料与银行无关，意远对此资料提供隐私保护。</p>
                <p>平台监督举报电话：400-042-1110，举报属实者均有现金奖励。</p>
                <div style="height: 20px;"></div>
            </div>

        </div>
    </div>




    <div id="content2" style="background: #fff;margin-left: 10px;display:none;">
        <div class="thetitle">上传个人微信二维码</div>
        <div style="height: 30px;"></div>
        <form id="frms" action="/wxyt/upload.do" method="post" enctype="multipart/form-data" target="frameFile">
            <div style="width: 100%; margin: 0 auto;">
                <div style="width: 40%; margin: 0 auto;">
                    <div style="width: 100%; position: relative;">
                        <input type="file" class="upload" name="fileName1" onchange="img_tijiao(1)" />
                        <img id="img1_img" width="100%" src="/static/images/132d9def-3fdc-4a2a-af19-043c819220e5.png">
                    </div>
                    <input type="hidden" id="img1_input" value="" />
                </div>
                <input type="hidden" name="flag" id="flag" />
                <div style="height: 20px;"></div>
            </div>
        </form>
        <iframe id="frameFile" name="frameFile" style="display: none;"></iframe>
    </div>

    <div style="height: 10px;clear: both;"></div>

    <footer>
        <div style="height: 60px;background-color: #eee;"></div>
        <div style="width: 100%; max-width: 640px; margin: 0 auto; position: fixed; bottom: 0; background: white; z-index: 9996; clear: both;">
            <img style="width: 100%; float: left;" src="/static/images/c9d8efbd-a6ce-4028-94b4-408152cb659b.png">
            <div style="width: 100%; height: 50px; margin: 10px auto 0; float: right; text-align: right;">
                <button class="zy-repayment" onclick="showPopup()">提交资料</button>
            </div>
        </div>
    </footer>

    <!-- 是否有绑定提现ID -->
    <input type="hidden" value="0" id="needBindTX">



    @include('agent.layout.floatbtn')


    <!-- 加载中 -->
    <div id="loadingToast" class="weui_loading_toast" style="display: none;">
        <div class="weui_mask_transparent"></div>
        <div class="weui_toast">
            <div class="weui_loading">
                <!-- :) -->
                <div class="weui_loading_leaf weui_loading_leaf_0"></div>
                <div class="weui_loading_leaf weui_loading_leaf_1"></div>
                <div class="weui_loading_leaf weui_loading_leaf_2"></div>
                <div class="weui_loading_leaf weui_loading_leaf_3"></div>
                <div class="weui_loading_leaf weui_loading_leaf_4"></div>
                <div class="weui_loading_leaf weui_loading_leaf_5"></div>
                <div class="weui_loading_leaf weui_loading_leaf_6"></div>
                <div class="weui_loading_leaf weui_loading_leaf_7"></div>
                <div class="weui_loading_leaf weui_loading_leaf_8"></div>
                <div class="weui_loading_leaf weui_loading_leaf_9"></div>
                <div class="weui_loading_leaf weui_loading_leaf_10"></div>
                <div class="weui_loading_leaf weui_loading_leaf_11"></div>
            </div>
            <p class="weui_toast_content">数据加载中</p>
        </div>
    </div>

    <div id="backdropOfsure" class="backdrop"></div>
    <div id="popupOfsure" class="popup" style="z-index: 100001;height: 242px;">
        <div class="top">
            <div class="title">请仔细核对您的身份信息</div>
            <div class="ftitle">意远办理业务完全免费，凡索取费用的均为欺诈行为，请不要相信！</div>
            <img class="xian" style="width: 100%;padding: 0px 0 10px 0;margin: 0px;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">
            <div style="padding: 0 20px;">
                <span class='sureTitle'>姓名：</span>
                <div class="num" id="bigName" style="font-weight: bold;"></div>
                <span class='sureTitle'>手机号：</span>
                <div class="num" id="bigPhone"></div>
                <span class='sureTitle'>身份证：</span>
                <div class="num" id="bigIdcard"></div>
            </div>
            <img class="xian" style="width: 100%;padding: 10px 0 10px 0;margin: 0px;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">
            <div class="bottom">
                <div id="quit" class="quit" onclick="hidePopup()">取消</div>
                <div id="next" class="next" onclick="submitRZ(this)">确认</div>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
    <!-- 弹窗内容注释 -->
    <script type="text/javascript">
        //自定义alert
        function custom_alert(title, content) {

            if (content == null || content == "" || content == "null") {
                content = "";
            }
            $("#weui_dialog_title").html(title);
            $("#weui_dialog_text").html(content);
            $("#weui_dialog_alert").show();
        }
    </script>
    <script type="text/javascript">
        function updateFontSize(n) {
            $("#input" + n).attr("style", "font-size:14px;");
        }

        function reduceFontSize(n) {
            $("#input" + n).attr("style", "font-size:12px;");
        }

        function uploadAvatar() {
            $("#upload").click();
        }

    </script>
    <script type="text/javascript">
        $(function() {
            $('#content').width($(window).width() - 20 + "px");
            $('#content2').width($(window).width() - 20 + "px");
            $('.thetitle').width($(window).width() - 30 + "px");
            var browser = {
                versions: function() {
                    var u = navigator.userAgent,
                        app = navigator.appVersion;
                    return { //移动终端浏览器版本信息
                        webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                        gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
                        mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                        ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                        android: u.indexOf('Android') > -1 ||
                            u.indexOf('Linux') > -1, //android终端或uc浏览器
                        qqbrowser: u.indexOf(" QQ") > -1, //qq内置浏览器
                    };
                }(),
                language: (navigator.browserLanguage || navigator.language)
                    .toLowerCase()
            };
            if (isWeiXin()) {
                if (browser.versions.ios) {
                    $("#backdrop").css("background-color", "#1a1c20");
                } else if (browser.versions.android) {
                    $("#backdrop").css("background-color", "#393a3e");
                }
            } else {
                if (browser.versions.qqbrowser == false) {
                    $("#pulicBtn").hide();
                }
                $("#backdrop").css("background", "#fed500");
            }
        });

        function isWeiXin() {
            var ua = window.navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == 'micromessenger') {
                return true;
            } else {
                return false;
            }
        }

        // 检测该手机号是否能被该用户使用
        function phoneNumBelongerCheck(phoneNum) {
            // 初始化变量
            var result = false;
            // 请求设置为同步
            $.ajaxSetup({async:false});
            $.post('{{ route("wxcheckmobilevalid") }}', {
                '_token': "{{ csrf_token() }}",
                'openid': window.localStorage.getItem('openid'),
                'mobile': phoneNum,
            }, function(response) {
                console.log('-------测试当前手机能否被使用 start-------');
                console.log(response);
                console.log('-------测试当前手机能否被使用 end-------');
                // 判断逻辑
                if (response.code == '0') {
                    result = true;
                } else {
                    result = false;
                }
            });
            // 返回
            return result;
        }

        // 检测该身份证是否能被该用户使用
        function identityCheck(id_number) {
            // ajax请求数据
            var result = false;
            // 请求设置为同步，获取到变量的值
            $.ajaxSetup({async:false});
            $.post('{{ route("wxcheckidnumbervalid") }}', {
                '_token': "{{ csrf_token() }}",
                'openid': window.localStorage.getItem('openid'),
                'id_number': id_number,
            }, function(response) {
                console.log('-------测试当前身份证能否被使用 start-------');
                console.log(response);
                console.log('-------测试当前身份证能否被使用 end-------');
                // 判断逻辑
                if (response.code == '0') {
                    result = true;
                } else {
                    result = false;
                }
            });
            // 返回
            return result;
        }

        //身份验证
        function yzIdCard() {
            var idCard = $("#aiIdCard").val().trim();
            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if (reg.test(idCard) === false) {
                prompt("请输入正确的身份证号");
            }
        }

        //上传图片
        function upload() {
            $("#doc").click();
            var flag = $("#flag").val();
            $("#doc").attr("name", "fileName" + flag);
            $("#frms").attr("target", "frameFile");
            $("#frms").attr("action", "/wxyt/upload.do");
            $("#frms").attr("enctype", "multipart/form-data");
            $("#frms").submit();
        }

        //显示只读模式：已经添加过且审核通过
        $(function() {
            var siexService = $("#siexService").val();
            var siexState = "";
            //var siexRemark = $(".siexRemark").attr("src");
            if ((siexService != null && siexService != "" && siexState != 3) /*   || (siexRemark!=null && siexRemark!="") */ ) {
                $("#siexService").attr("readOnly", "true");
                //$("#fangfa").attr("onclick","");
            }
        });

        //提示信息
        function prompt(msg) {
            var prompt_box = document.getElementsByClassName("prompt_box")[0];
            if (!prompt_box) {
                var html = "<div class='prompt_box'><span>" + msg + "</span></div>";
                $("body").append(html);
            } else {
                prompt_box.innerHTML = "<span>" + msg + "</span>";
            }

            // 3秒钟显示隐藏
            $(".prompt_box").fadeIn(2000);
            $(".prompt_box").fadeOut(5000);

        }

    </script>

    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>

</body>

</html>