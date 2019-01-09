<!DOCTYPE html>
<html>

<head>
    <title>确认申请人信息</title>
    <meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/applycard.css') }}">
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <!-- 判断浏览器版本信息 -->

    <script type="text/javascript">
        var browser;
        $(function() {
            browser = {
                versions: function() {
                    var u = navigator.userAgent;
                    return { //移动终端浏览器版本信息
                        webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                        gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
                        mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                        ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                        android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或uc浏览器
                        qqbrowser: u.indexOf(" QQ") > -1, //qq内置浏览器
                        qq: u.match('QQ/'), //是否QQ
                    };
                }(),
                language: (navigator.browserLanguage || navigator.language).toLowerCase()
            };
        });
    </script>
    <script type="text/javascript">
        $(function() {
            $(".inputbox .cardDiv").css("width", ($(window).width() * 0.93 * 0.9 - 62 - 15) + "px");
            $('#content').width($(window).width() - 20 + "px");
            $('#subDiv').width($(window).width() - 20 + "px");
            $('#okimg').css("margin-top", $(window).width() * 0.15 + "px");
            $('.smallTxt').css("padding-bottom", $(window).width() * 0.15 + "px");
            if (isWeiXin()) {
                if (browser.versions.ios) {
                    $("header").css("background", "#1a1c20");
                } else if (browser.versions.android) {
                    $("header").css("background", "#393a3e");

                    var extension = '';
                    if (extension == '') {
                        var xycId = $("#xycId").val();
                        if (xycId == 44 || xycId == 3 || xycId == 78 || xycId == 69 || xycId == 91 || xycId == 92) {
                            $("#xybBtn").attr("onclick", "");
                            window.setTimeout("addxybBtn()", 1000);
                        }
                    }
                }
            } else {
                $("header").css("background", "#12b7f5");
                if (browser.versions.qqbrowser == false) {
                    $("#pulicBtn").hide();
                }

                if (browser.versions.ios) {} else if (browser.versions.android) {
                    var extension = '';
                    if (extension == '') {
                        var xycId = $("#xycId").val();
                        if (xycId == 44 || xycId == 3 || xycId == 78 || xycId == 69 || xycId == 91 || xycId == 92) {
                            $("#xybBtn").attr("onclick", "");
                            window.setTimeout("addxybBtn()", 1000);
                        }
                    }
                }
            }
        });

        // 提交逻辑
        function subForm(obj) {
            var infoId = $("#infoId").val();
            var xycId = $("#xycId").val();
            var userName = $("#name").html();
            var zjNum = $("#idcard").html();
            var phone = $("#phone").html();
            var province = $("#province").val();
            var city = $("#city").val();
            var caId = $("#caId").val();

            var chk = $("#agree").val();
            if (chk == "false") {
                prompt("请仔细阅读《意远平台服务协议》");
                return;
            }

            // 验证身份证
            yzIdCard();

            if (caId == '') {
                prompt("请先添加申请人信息");
                return;
            }

            if (userName == null || userName == "") {
                prompt("姓名为空！");
                return;
            }

            var reg1 = /^[\u4e00-\u9fa5-· ]{2,20}$/;
            if (!reg1.test(userName)) {
                prompt("请正确输入姓名！");
                return;
            }

            if (zjNum == null || zjNum == "") {
                prompt("请输入身份证！");
                return;
            }
            if (zjNum.length != 16 && zjNum.length != 18) {
                prompt("选择的身份证号码错误！");
                return;
            }

            if (zjNum.trim().indexOf(' ') != -1) {
                prompt("选择的身份证号码错误！");
                return;
            }

            if (realSex == null || realSex == "") {
                prompt("选择的身份证号码错误！");
                return;
            }

            if (phone == null || phone == "") {
                prompt("手机号为空！");
                return;
            }
            reg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
            if (!reg.test(phone)) {
                prompt('请输入有效的手机号码！');
                return;
            }
            // 接下来就走ajax提交
            ajax_submit(userName, zjNum, phone);
        }

        // ajax提交办卡表单
        function ajax_submit(user_name, user_identity, user_phone) {
            // 然后ajax提交
            // 如果验证通过那么就ajax提交
            $.ajax({
                type: 'post',
                url: "{{ route('wxapplycardstore') }}",
                data: {
                    'user_openid': window.localStorage.getItem('openid'),
                    'card_id': {{ $bank->id }},
                    '_token': "{{ csrf_token() }}",
                    'user_name': user_name,
                    'user_identity': user_identity,
                    'user_phone': user_phone,                    
                },
                dataType: 'json',
                timeout: 99999999,
                beforeSend: function() {
                    index = layer.load(1, {
                        shade: [0.5, '#fff'] //0.1透明度的白色背景
                    });
                },
                complete: function() {
                    layer.close(index);
                },
                success: function(data) {
                    // 判断
                    if (data.code == 0) {
                        // 成功返回
                        prompt(data.msg);
                        // 3000毫秒后跳转
                        setTimeout("window.location.href='{!! $bank->creditCardUrl !!}'", 3000);
                    } else {
                        // 失败返回
                        return prompt(data.msg);
                    }
                },
                error: function(data) {
                    // 捕捉错误
                    if (data.status == '422') {
                        var jsonObj = JSON.parse(data.responseText);
                        var errors = jsonObj.errors;
                        for (var item in errors) {
                            for (var i = 0, len = errors[item].length; i < len; i++) {
                                return prompt(errors[item][i]);
                            }
                        }
                    } else {
                        var jsonObj = JSON.parse(data.responseText);
                        return prompt('错误代码：' + jsonObj.code + '，错误类型：' + jsonObj.msg);
                    }
                },
            });
            // 禁止执行下面代码
            return false;
        }

        function addcardAppShow() {
            $("#phone").val(""); //手机号
            $("#zjNum").val(""); //身份证
            $("#userName").val(""); //姓名

            $("#sqDiv").show(); //填写信息的框
            //$("#XzDiv").hide();//已有申请人信息
            $("#addBtn").hide(); //添加申请人按钮
            //$("#subDiv").hide();//申请提交按钮
            $("#addcardTX").show(); //提示文字

            $("#xybBtn").html("保存信息");
            $("#xybBtn").attr("onclick", "addcardApp(this)"); //下一步按钮 更改事件

            //$("#addQRBtn").css("background-color","#ccc");
            selectApp(null, 0);
        }

        function addcardAppHide() {
            $("#sqDiv").hide(); //填写信息的框
            //$("#XzDiv").show();//已有申请人信息
            $("#addBtn").show(); //添加申请人按钮
            //	$("#subDiv").show();//申请提交按钮
            $("#addcardTX").hide(); //提示文字

            $("#xybBtn").html("下一步");
            $("#xybBtn").attr("onclick", "showSqInfo()"); //下一步按钮 更改事件
        }

        function addcardApp(obj) {
            $(obj).attr("onclick", "");
            var chk = $("#agree").val();
            if (chk == "false") {
                alert("请仔细阅读《意远平台服务协议》");
                return;
            }

            var userName = $("#userName").val(); //姓名
            if (userName == null || userName == "") {
                alert("姓名为空！");
                return;
            }

            var reg1 = /^[\u4e00-\u9fa5-· ]{2,20}$/;
            if (!reg1.test(userName)) {
                alert("请正确输入姓名！");
                return;
            }

            var zjNum = $("#zjNum").val();
            if (zjNum == null || zjNum == "") {
                alert("请输入身份证！");
                return;
            }
            if (zjNum.length != 16 && zjNum.length != 18) {
                alert("请输入正确的身份证号码！");
                return;
            }

            var realSex = $("#realSex").val();
            if (realSex == null || realSex == "") {
                alert("请输入正确的身份证号码！");
                return;
            }

            var phone = $("#phone").val(); //手机号
            if (phone == null || phone == "") {
                alert("手机号为空！");
                return;
            }
            reg = /^1[3|4|5|7|8][0-9]{9}$/;
            if (!reg.test(phone)) {
                alert('请输入有效的手机号码！');
                $(obj).attr("onclick", "getYzm(this)");
                return;
            }
            var infoId = $("#infoId").val();
            var xycId = $("#xycId").val();
            if (infoId != null && infoId != '') {
                $("#loadingToast").show();
                $.ajax({
                    async: false,
                    url: "/xinyongka/addCardApp.do",
                    type: "post",
                    data: {
                        "infoId": infoId,
                        "userName": userName,
                        "zjNum": zjNum,
                        "phone": phone,
                    },
                    success: function(data) {
                        if (data != null) {
                            var json = eval("(" + data + ")");
                            if (json.ret == -1) {
                                alert("帐号异常，请稍候再试！");
                            } else if (json.ret == -2) {
                                alert("保存失败，该身份证您已添加！");
                            } else if (json.ret == -3) {
                                alert("您账户只能帮助3位伙伴申请信用卡，如需要申请请直接发连接或二维码让客户自行申请。");
                            } else if (json.ret == 0) {
                                alert("保存成功！");
                                location.href = '/xinyongka/doCard.do?f=' +
                                    Math.random();
                            }
                        }
                        $(obj).attr("onclick", "addcardApp(this)");
                        $("#loadingToast").hide();
                    }
                });
            } else {
                alert("请先登录！");
            }
        }

        function selectApp(obj, caId) {
            //框的颜色
            $(".cardDiv").removeClass("on");
            $(obj).addClass("on");

            $("#caId").val(caId);

            //删除按钮
            $(".delImg").removeClass("yc");
            $("#delImg_" + caId).addClass("yc");

            $(".sucImg").addClass("yc");
            $("#sucImg_" + caId).removeClass("yc");

            //关闭 填写申请信息的框
            if (caId != 0) {
                addcardAppHide();
            }
        }

        function hideSqInfo() {
            $("#sjinfo").hide();
            $("#zy_mask").hide();
        }

        function hideDqInfo() {
            $("#dqinfo").hide();
            $("#dq_mask").hide();
        }

        function showSqInfo() {
            var caId = $("#caId").val();
            if (caId == '') {
                alert("请先添加申请人信息");
                return;
            }

            var userName = $("#name").html(); //姓名
            var zjNum = $("#idcard").html();
            var phone = $("#phone").html(); //手机号

            $("#sqName").html(userName);
            $("#sqIdNo").html(zjNum);
            $("#sqPhone").html(phone);

            $("#sjinfo").show();
            $("#zy_mask").show();
        }

        function yzIdCard(flag) {
            var idCard = $("#idcard").val();
            if (idCard == "") {
                if (flag == 1) {
                    alert("请输入正确的身份证号码！");
                }
                return;
            }

            if (idCard.length < 15) {
                if (flag == 1) {
                    alert("请输入正确的身份证号码！");
                }
                return;
            }

            // 判断身份证是否已经存在于数据库
            if (!checkidnumbervalid(window.localStorage.getItem('openid'), idCard)) {
                // 填充
                prompt('该身份证不允许使用!');
                $("#realSex").val("");
            } else {
                $("#realSex").val(idCard);
            }
        }

        function toApplicants(acId) {
            var url = document.location.href;
            if (typeof(acId) == "undefined") {
                location.href = '/xinyongka/allApplicant.do?xycId=' +
                    $("#xycId").val() + "&type=2&f=" + Math.random();
            } else {
                location.href = '/xinyongka/allApplicant.do?xycId=' +
                    $("#xycId").val() + "&type=2" + "&acId=" + acId + "&f=" +
                    Math.random();
            }
        }

        function hidePopup() {
            $('#backdropOfSQ').hide();
            $('#popupOfSQ').hide();
        }

        function addxybBtn() {
            $("#xybBtn").attr("onclick", "showPopup(1)");
        }
    </script>
</head>

<body>
    <input type="hidden" id="province" value="">
    <input type="hidden" id="city" value="">
    <input type="hidden" id="realSex">
    <input type="hidden" id="infoId" value="14150603">
    <input type="hidden" id="xycId" value="82">
    <input type="hidden" id="thecaId" value="5401717">

    <div class="okimg" id="okimg">
        <img src="/static/images/a260599d-88e9-4184-b17a-9087a66396db.png">
        <div class="txt">请确认申请人信息</div>
        <!-- <div class="smallTxt">温馨提示：信用卡申请成功后进入平台查询进度，可获得最高188元的现金奖励。</div> -->
    </div>

    <div id="content">
        <div class="defaultApp">
            <div class="appInfo">
                <input type="hidden" id="caId" value="5401717">
                <div class="top">
                    <span class="name"><img class="littleimg" src="/static/images/43e7a6bf-409c-4a0e-9be7-653dcb3a5c55.png"><span id="name"></span></span>
                    <span class="phone"><img class="littleimg" src="/static/images/6a6d78f6-4beb-4121-9788-8c9e582e4664.png"><span id="phone"></span></span>
                </div>
                <div class="bottom">
                    <span class="idcard"><img class="littleimg" src="/static/images/946ef9a0-9467-4fea-a41b-b311b337ece1.png"><span id="idcard"></span></span>
                </div>
                <img class="xian2" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png"></img>
            </div>
        </div>
    </div>


    <div id="subDiv" style=" background: #eee;">
        <div style="width: 100%; height: 10px;"></div>
        <div style="width:100%;height: 56px;background: #fff">
            <input type="hidden" id="agree" value="true">
            <div style="width: 50px;float: left;">
                <img style="width:40px;float: right;margin-top: 8px;" onclick="agree(this)" src="/static/images/048df3ec-521c-415c-af7e-5410e8741417.png">
            </div>
            <div id="js03" style="float: left;font-size:12px;color: #999;line-height: 18px;padding: 10px 10px 10px 10px;">我已认真阅读并完全同意：<a style="color: #ffcc00;padding: 0px;margin: 0px" href="{{ route('wxagreement') }}">《意远平台服务协议》</a>的所有条款</div>
            <div style="clear: both;"></div>
        </div>
        <div class="thetitle">注意事项</div>
        <div id="addcardTX" style="background: #fff;text-align: justify;">
            <div style="height: 10px;"></div>
            <p>1、必须准确填写申请人真实个人信息
                <!-- ，否则申请成功后无法领取1-188元的现金奖励。 -->
            </p>
            <p>2、收到办卡审核通知短信，2个工作日后查到进度并提示“等待工作人员审核”，将有机会获得现金奖励。</p>
            <p>3、在意远申请信用卡不收取任何费用，凡是索取均为欺诈，请不要相信！</p>
            <p>平台监督举报电话：400-042-1110，举报属实者均有现金奖励。</p>
            <div style="height: 20px;"></div>
        </div>
    </div>



    <div style="height: 55px;"></div>

    <div style="width:100%;max-width:640px;margin:0 auto;position:fixed;bottom:0;z-index:9996;clear: both;">
        <img style="width: 100%;float: left;display: block;" src="/static/images/a4500ebf-9543-41dc-aa6c-c1cde2ee975e.png">
        <div style="height: 45px;">
            <!-- <button class="newbtn01" id="xybBtn" onclick="subForm(this)">下一步</button> -->
            <button class="newbtn01" id="xybBtn" onclick="showPopup(1)">下一步</button>
        </div>
    </div>

    <div id="backdropOfSQ" class="backdrop" onclick="hidePopup()"></div>
    <div id="popupOfSQ" class="popup">
        <div class="content">
            <div class="top">
                <strong>结算周期：</strong>T+1（不需要查询贷款进度，客户贷款成功且符合平台结算标准，一般两个工作日内结算） 注：如客户以前有注册过或贷款过的，代理商则无法拿到该奖励金。一般贷款成功后第2个工作日会显示贷款结果，贷款结果会显示放款利率，放款金额，放款期限，请仔细查看。
            </div>
            <div class="bottom">
                <strong>结算规则：</strong>客户必须为首次注册申请该产品，才算有效申请。贷款奖金按照平台分发的标准进行结算，贷款奖金结算时间如遇到重大节假日可能会延迟到工作日。 客户是第一次注册并且申请成功方可结算佣金，若客户之前有注册或申请或该产品则为无效申请，也无法计入在代理商名下的业绩，也无法拿到贷款奖金。
            </div>
            <div class="btn" onclick="subForm(this)">确认申请</div>
        </div>
    </div>

    <div class="zy_mask" id="zy_mask" style="z-index:95554;display: none;" onclick="hideSqInfo()"></div>
    <div id="sjinfo" style="width: 100%;  z-index: 95555; overflow: scroll; position: fixed; bottom: 0px; overflow: hidden;display: none;">
        <div style="width: 100%; height: 100%; margin: 0 auto; background: white;">
            <div style="width:100%;">
                <div style="width: 93%;margin-left:3.5%;padding: 7px 0;font-size: 14px;color: #666;">
                    确认申请人信息
                </div>
                <img style="width: 93%;margin-left:3.5%;padding: 0 0 5px 0; display: block;" src="/static/images/542af673-79c1-4cd3-bbf6-4858a3015e0c.png"></img>
                <div style="height: 10px;"></div>
                <div class="sjtinfo" style="width: 93%;margin-left: 3.5%;color: #333;">
                    <div class="sj_txt">姓名：<span id="sqName"></span></div>
                    <div class="sj_txt">身份证号：<span id="sqIdNo"></span></div>
                    <div class="sj_txt">手机号 ：<span id="sqPhone"></span></div>
                </div>
                <div style="height: 10px;"></div>
                <img style="width: 93%;margin-left:3.5%;padding: 0 0 5px 0; display: block;" src="/static/images/542af673-79c1-4cd3-bbf6-4858a3015e0c.png"></img>
                <div class="zhfu" style="width: 96%;margin-left: 2%;">
                    <div onclick="hideSqInfo()" style="float:left;border-radius:5px; width:48%;height:40px;background: #ffcc00; font-size: 16px;line-height: 40px;margin: 10px auto 5px;text-align: center;color: #fff;">取消</div>
                    <div onclick="subForm(this)" style="float:left;border-radius:5px; width:48%;height:40px;background: #ffcc00; font-size: 16px;line-height: 40px;margin: 10px 0 5px 4%;text-align: center;color: #fff;">确认申请</div>
                </div>
                <div style="clear: both;height: 10px;"></div>
            </div>
        </div>
    </div>

    <div class="zy_mask" id="dq_mask" style="z-index:10000;display: none;" onclick="hideDqInfo()"></div>
    <div id="dqinfo" style="width: 100%;  z-index: 10001; overflow: scroll; position: fixed; bottom: 0px; overflow: hidden;display: none;">
        <div style="width: 100%; height: 100%; margin: 0 auto; background: white;">
            <div style="width: 93%;margin-left:3.5%;">
                <div style="padding: 7px 0;font-size: 14px;color: #666;">
                    地区
                </div>
                <img style="padding: 0 0 5px 0; display: block;width: 100%;" src="/static/images/542af673-79c1-4cd3-bbf6-4858a3015e0c.png"></img>
                <div style="height: 10px;"></div>
                <div class="fontStyle-field" style="width: 49%;">
                    <select style="padding-left: 5px;" name="s_province" id="s_province" onchange="getCity(this)"><option>请选择</option></select>
                </div>
                <div class="fontStyle-field" style="width: 49%;">
                    <select style="padding-left: 5px;" name="s_city" id="s_city"><option>请选择</option></select>
                </div>
                <div style="height: 10px;"></div>
                <img style="padding: 0 0 5px 0; display: block;width: 100%;" src="/static/images/542af673-79c1-4cd3-bbf6-4858a3015e0c.png"></img>
                <div class="zhfu" style="width: 98%;margin-left: 1%;">
                    <div onclick="hideDqInfo()" style="float:left;border-radius:5px; width:48%;height:40px;background: #ffcc00; font-size: 16px;line-height: 40px;margin: 10px auto 5px;text-align: center;color: #fff;">取消</div>
                    <div onclick="subForm()" style="float:left;border-radius:5px; width:48%;height:40px;background: #ffcc00; font-size: 16px;line-height: 40px;margin: 10px 0 5px 4%;text-align: center;color: #fff;">确认申请</div>
                </div>
                <div style="clear: both;height: 10px;"></div>
            </div>
        </div>
    </div>

    <div id="backdropOfmsg" class="backdrop2"></div>
    <div id="popupOfmsg" class="popup2" style="z-index: 100002;height: 220px">
        <div class="top" style="padding: 10px 30px 10px 30px">
            <div class="title" id="codeTitle">系统将给下列手机号发送验证码</div>
            <input id="caId" type="hidden">
            <div id="oldPhone" style="width:100%;font-size: 18px;text-align: center;line-height: 50px;color: #333;font-family: DINPro-Bold;"></div>
            <div id="changePhone" style="display: none;">
                <span class="infoLeft">手机号</span>
                <input class="infoRight" id="newPhone" style="font-family: DINPro-Regular;width:120px;" type="text" value="" placeholder="请输入手机号">
            </div>
            <img class="xian3" style="width: 100%;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">

            <input id="validate" class="input_pic" style="width: 50%; float: left;margin-left: 0%;text-align: left;height: 50px;text-align: center;font-size: 14px;color:#333;font-family: DINPro-Regular;" type="tel" placeholder="请输入验证码" maxlength="4" />
            <div id="getcode" class="input_pic" onclick="sendDXMsg(this, 1)" style="width: 40%; margin-top:9px; float: right; background: #fed500; color: #fff; border: 0px solid #fed500; border-radius:3px;line-height: 33px;font-size: 12px;">获取验证码</div>
            <img class="xian3" style="width: 100%;" src="/static/images/78e8ec60-8785-48e7-9475-d77049593b71.png">
            <div style="clear: both;height: 20px;"></div>
            <div class="bottom">
                <div id="quit" class="quit" onclick="hidePopup(1)">取消</div>
                <div id="next2" class="next" onclick="checkCode()">下一步</div>
            </div>
        </div>
    </div>

    <div id="backdropOfsure" class="backdrop2"></div>
    <div id="popupOfsure" class="popup2" style="z-index: 100001;height: 242px;">
        <div class="top">


            <div class="title">请仔细核对您的身份信息</div>
            <div class="ftitle">意远平台申请信用卡完全免费，凡索取费用的均为欺诈行为，请不要相信！</div>

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

                <div id="quit" class="quit" onclick="updateApp()">修改</div>
                <div id="next" class="next" onclick="subForm(this)">确认无误</div>


            </div>
        </div>

        <div style="clear: both"></div>
    </div>


    @include('agent.layout.floatbtn')


    <link rel="stylesheet" href="/static/css/weui.min.css" />
    <style>
        .weui_dialog {
            top: 40%;
            z-index: 100000;
        }
        
        .weui_mask {
            z-index: 999998;
        }
        
        .weui_toast {
            z-index: 999999;
        }
    </style>
    <div id="weui_dialog_alert" class="weui_dialog_alert" style="display:none;">
        <div class="weui_mask"></div>
        <div class="weui_dialog">
            <div class="weui_dialog_hd"><strong id="weui_dialog_title" class="weui_dialog_title">提示</strong></div>
            <div class="weui_dialog_bd" id="weui_dialog_text">弹窗内容，告知当前页面信息等</div>
            <div class="weui_dialog_ft">
                <a href="javascript:$('#weui_dialog_alert').hide();" class="weui_btn_dialog primary">确定</a>
            </div>
        </div>
    </div>

    <div id="loadingToast" class="weui_loading_toast" style="display:none;">
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
        //页面布局计算
        $(function() {
            $('#js03').width($("#subDiv").width() - 70 + "px");
        });
        //图片加载完全后
        window.onload = function() {}

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
    </script>
    <input type="hidden" id="infoNickname" value="{{ $user['nickname'] }}">
    <input type="hidden" id="bankName" value="{{ $bank->merCardName }}">
    <input type="hidden" id="bankId" value="{{ $bank->id }}">
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
    <script type="text/javascript" src="/backend/layer/layer.js"></script>
    <script type="text/javascript">
        function updateApp() {
            hidePopup(2);
            var caId = $("#thecaId").val();
            var phone = $("#phone").html().trim();
            $("#caId").val(caId);
            $("#oldPhone").html(phone);
            $("#oldPhone").show();
            $("#changePhone").hide();
            showPopup(2);
        }

        // 修改为新手机号
        function updApp(obj) {
            var onclick = $(obj).attr("onclick");
            $(obj).attr("onclick", "");
            var caId = $("#caId").val();
            var infoId = $("#infoId").val();
            var code = $("#validate").val().trim();
            var phone = $("#newPhone").val().trim();
            var oldphone = $("#oldPhone").html().trim();

            if (phone == oldPhone) {
                alert("修改前后手机号相同，无法修改！");
                return;
            }

            // ajax提交
            // 首先判断验证码是否正确
            // 判断验证码是否正确
            if (!checkwxcode(code)) {
                // 提示错误
                prompt('验证码错误');
                // 放开点击
                $(obj).attr("onclick", onclick);
                // 返回
                return false;
            } else {
                // 然后输入新手机号逻辑
                if (!modifyMobile(window.localStorage.getItem('openid'), phone)) {
                    // 提示错误
                    prompt('手机号修改失败');
                    // 放开点击
                    $(obj).attr("onclick", onclick);
                    // 清空计时器
                    recovery(2);
                    // 清除验证码缓存
                    removewxyzm();
                    return false;
                } else {
                    // 提示错误
                    prompt('手机号修改成功');
                    // 清空计时器
                    recovery(2);
                    // 清除验证码缓存
                    removewxyzm();
                    // 3000毫秒后重新刷新页面
                    setTimeout("location.reload()", 3000);
                }
            }
            // 禁止往下执行
            return false;
        }


        //身份证号确认
        var idcardSure = false;

        function infoSure(yesorno) {
            if (yesorno == 0) {
                //保存
                idcardSure = true;
                hidePopup(2);
                addcardApp($("#saveApp").get(0));
            } else {
                //修改
                idcardSure = false;
                hidePopup(2);
            }
        }

        // 发送短信验证
        var ting1, ting2;
        // 发送短信逻辑        
        function sendDXMsg(obj, kind) {
            var phone = "";
            switch (kind) {
                case 1:
                    phone = $("#oldPhone").html();
                    break; //修改时原始电话号码
                case 2:
                    phone = $("#newPhone").val();
                    if (phone == $("#oldPhone").html()) {
                        alert("手机号与修改前相同！请重新输入");
                        $("#newPhone").val("");
                        return;
                    }
                    break; //修改时新电话号码
            }

            phone = $("#oldPhone").html();
            if (!valid(phone)) {
                alert("手机号错误，请仔细核对输入的手机号！");
                return;
            }

            // 禁止重复点击
            $(obj).attr("onclick", "");

            // 验证手机号并发送验证码
            toSendCode(phone, obj, kind);
        }

        //判断电话号码真实性
        function valid(phone) {
            var reg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
            if (reg.test(phone)) {
                return true;
            }
            return false;
        }

        //120秒倒计时
        function countingdown() {
            var ss = $('#countdown').html();
            $('#countdown').html(ss - 1);
        }

        //恢复验证码发送按钮
        function recovery(kind) {
            $('#getcode').attr("onclick", "sendDXMsg(this," + kind + ")");
            $('#getcode').html("获取验证码");
            clearInterval(ting1);
            clearInterval(ting2);
            // 背景还原
            $('#getcode').css("background-color", "#fed500");
        }

        function hidePopup(flag) {
            switch (flag) {
                case 1:
                    $('#backdropOfmsg').hide();
                    $('#popupOfmsg').hide();
                    location.reload();
                    break;
                case 2:
                    $('#backdropOfsure').hide();
                    $('#popupOfsure').hide();
                    break;
            }
        }

        function showPopup(flag) {
            if (flag == 1) {
                var idcard = $("#idcard").html().trim();
                var name = $("#name").html().trim();
                var phone = $("#phone").html().trim();
                idcard = idcard.substring(0, 6) + " " + idcard.substring(6, 14) + " " + idcard.substring(14, idcard.length);
                $("#bigIdcard").html(idcard);
                $("#bigName").html(name);
                $("#bigPhone").html(phone);
                $('#backdropOfsure').show();
                $('#popupOfsure').show();
                $('.noBtn').width(($("#popupOfsure").find(".bottom").width() - 4) * 0.5 + "px");
                $('.yesBtn').width(($("#popupOfsure").find(".bottom").width() - 4) * 0.5 + "px");
            } else if (flag == 2) {
                $('#backdropOfmsg').show();
                $('#popupOfmsg').show();
            }
            leftAndRight(2);
        }

        // 修改短信验证码逻辑
        // 获取验证码
        // 发送验证码短信
        function toSendCode(mobile, obj, kind) {
            $.get("{{ route('wxcreatecode') }}", function(response) {
                // 打印验证码
                console.log('-------取出验证码 start-------');
                console.log(response);
                console.log('-------取出验证码 end-------');
                // 发送验证码，测试期间可以先注释
                sendmsg(mobile, '2002', response);
                // 添加节点dom
                // 前端状态显示倒计时
                $(obj).html("<span id='countdown'>180</span>");
                $(obj).css("background-color", "#7b7b7b");
                ting1 = setInterval("countingdown()", 1000);
                ting2 = setInterval("recovery(" + kind + ")", 180000);
            });
        }

        // 检查输入的手机验证码是否正确
        function checkCode() {

            // 锁定提交按钮，禁止重复点击
            $('#next2').attr('onclick', '');

            // 重要参数
            var code = $("#validate").val().trim();
            var phone = $("#oldPhone").html().trim();

            if (code == '' || code == null || code == 'null') {
                prompt('请填写验证码');
                $('#next2').attr('onclick', 'checkCode()');
                return;
            }

            // 判断验证码是否正确
            if (!checkwxcode(code)) {
                prompt('验证码错误');
                // 放开点击
                $('#next2').attr('onclick', 'checkCode()');
                // 返回
                return false;
            } else {
                // 然后输入新手机号逻辑
                $("#getcode").css("background", "#fed500");
                // 启用新手机号验证dom
                $("#getcode").attr("onclick", "sendDXMsg(this, 2)");
                $("#next2").attr("onclick", "updApp(this)");
                $("#next2").html("确认修改");
                $("#codeTitle").html("请输入你要修改的新手机号");
                $("#oldPhone").hide();
                $("#changePhone").show();
                $("#validate").val("");
                recovery(2);
            }
            // 禁止往下执行
            return false;
        }

        function leftAndRight(type) {
            switch (type) {
                case 1:
                    $("#sqDiv").find(".infoRight").each(function() {
                        $(this).width(($("#sqDiv").find(".content").width() - 80) + "px");
                    });
                    break;
                case 2:
                    $("#changePhone").find(".infoRight").width(($("#popupOfmsg").find(".top").width() - 80) + "px");
                    break;
                default:
                    break;
            }
        }
    </script>

</html>