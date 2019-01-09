<!DOCTYPE HTML>
<html>

<head>
    <title>信用卡介绍</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/weui.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/example.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mui.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/circle.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/cardinfo.css') }}">
    <style type="text/css">
        @font-face {
            font-family: DIN Engschrift Bali;
            src: url('/fonts/DIN Engschrift Bali.otf');
        }
        
        .btn {
            width: 50%;
            height: 28px;
            line-height: 28px;
            background: #00A0E6;
            color: white;
            border: 0;
            text-align: center;
            border-radius: 3px;
        }
        
        .item ul li {
            list-style: none;
            float: left;
            width: 33.33%;
            text-align: center;
            border-left: 1px solid #ccc;
            border-top: 1px solid #ccc;
            margin: -1px;
        }
        
        img {
            border: 0;
        }
        
        ul,
        p {
            padding: 0;
            margin: 0;
        }
        
        .clear_1 {
            width: 100%;
            height: 20px;
            clear: both;
        }
        
        .p1 {
            margin-top: 5px;
            width: 96%;
            height: 38px;
            line-height: 19px;
        }
        
        .clear_10 {
            width: 100%;
            height: 10px;
            clear: both;
        }
        
        ul li {
            list-style: none;
            float: left;
            text-align: center;
        }
        
        .module_div {
            float: left;
            width: 100%;
        }
        
        .module_div .img_div {
            width: 31%;
            float: left;
        }
        
        .module_div .img_select {
            width: 70%;
            margin-left: 15%;
        }
        
        .module_div .module_title {
            color: #333;
            margin: 0 0 0 3%;
            font-size: 0;
        }
        
        .service-provider {
            /* padding: 10px 0 0 0; */
            float: left;
            width: 13%;
        }
        
        .service-provider img {
            height: 32px;
            width: 32px;
            /* border-radius: 50%; */
            margin-left: 10px;
            border: 4px solid #e6e6e6;
        }
        
        .service_info {
            float: left;
            /* width: 35%; */
            position: relative;
            left: 30px;
            top: 0px;
        }
        
        .service_info .service_id img {
            width: 15px;
            position: relative;
            top: 0px;
        }
        
        .service_info .service_nmae {
            text-align: left;
            margin-top: 7px;
            /* margin-right: 20px; */
            font-size: 14px;
            padding-top: 8px;
        }
        
        .service_info .service_id {
            text-align: left;
            color: #999;
            font-size: 12px;
            /* margin-right: 20px; */
        }
        
        .scDetail {
            width: 100%;
        }
        
        .scDetail div {
            width: 31%;
            height: 30%;
            float: left;
            /* margin-left: 1%; */
        }
        
        .clear_100 {
            width: 100%;
            height: 80px;
            clear: both;
        }
        
        .clear {
            width: 100%;
            clear: both;
        }
        
        .loadmore {
            color: #00A0E6;
        }
        
        .fontColor {
            color: #FF4400;
        }
        
        .background_color {
            background: #1b1a1f;
        }
        
        .zy-repayment {
            width: 45%;
            height: 35px;
            line-height: 35px;
            color: white;
            background-color: #FFcc00;
            border: none;
            font-size: 16px;
            border-radius: 3px;
        }
        
        .zy-repayment2 {
            width: 50%;
            height: 35px;
            line-height: 35px;
            color: white;
            background-color: #ccc;
            border: none;
            font-size: 16px;
            border-radius: 3px;
            margin-right: 7%;
        }
        
        .zy_mask {
            background: rgba(0, 0, 0, 0.6);
            position: fixed;
            z-index: 1;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
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
        
        #dqinfo select {
            color: #999;
            margin: 7px 0 7px 14px;
            width: 80%;
            outline: 0px;
            float: left;
            height: 36px;
            border: none;
            background-color: #e6e6e6;
            border-radius: 3px;
            -webkit-appearance: none;
            font-size: 12px;
            text-align: center;
        }
        
        .newbtn01 {
            width: 50%;
            height: 100%;
            border: 0px;
            margin: 0px;
            float: left;
            color: #fff;
            font-size: 15px;
        }
        
        .newbtn11 {
            width: 33.33%;
        }
        
        .no01 {
            background: #ffffff;
            color: #333;
        }
        
        .no02 {
            background: #fed500;
        }
        
        .no03 {
            background: #ff4400;
        }
        
        .no11 {
            background: #ffffff;
            color: #333;
        }
        
        .no12 {
            background: #fed500;
        }
        
        .no13 {
            background: #ff4400;
        }
        
        .posterBottom {
            width: 100%;
            height: 210px;
            background: #fff;
            position: fixed;
            bottom: 0px;
            z-index: 95557;
            display: none;
        }
        
        .posterBottom .item {
            padding: 10px 20px 10px 20px;
        }
        
        .posterBottom .item .top {
            font-size: 13px;
            color: #333;
            text-align: justify;
        }
        
        .posterBottom .item .middle {
            margin-top: 20px;
        }
        
        .posterBottom .item .bottom {
            margin-top: 20px;
        }
        
        .posterBottom .item .middle .input {
            border: 1px solid #999;
            border-radius: 3px;
            line-height: 40px;
            height: 40px;
            width: 70%;
            font-size: 13px;
            color: #333;
            float: left;
        }
        
        .posterBottom .item .btn {
            float: right;
            width: 28%;
            text-align: cneter;
            background: #fed500;
            color: #fff;
            height: 40px;
            line-height: 40px;
            border-radius: 3px;
        }
        
        .posterBottom .item .bottom .btn {
            width: 100%;
            float: none;
        }
        
        .posterBottom .item .middle .ntype {
            float: left;
            width: 80px;
            height: 25px;
            line-height: 25px;
            text-align: center;
            font-size: 12px;
            background: #fff;
            border: 1px solid #999;
            border-radius: 3px;
            color: #999;
            margin-right: 10px;
        }
        
        .posterBottom .item .middle .y {
            height: 27px;
            width: 82px;
            border: 0;
            color: #fff;
            background: #fed500;
        }
        
        #item2 .middle {
            margin-top: 10px;
        }
        
        #item2 .bottom {
            margin-top: 10px;
        }
        
        #item1 .middle {
            margin-top: 5px;
        }
        
        #item1 .bottom {
            margin-top: 5px;
        }
        
        #item2 .middle .contact {
            margin-top: 10px;
        }
        
        .xian {
            width: 100%;
            height: 1px;
            display: block;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        
        .lBtn {
            margin-left: 20px;
            height: 50px;
            float: left;
            line-height: 50px;
            background: #fff;
            font-size: 16px;
            color: #fed500
        }
        
        .rBtn {
            width: 50%;
            height: 50px;
            float: right;
            line-height: 50px;
            text-align: center;
            background: #fff;
            font-size: 16px;
            color: #fff
        }
        
        .applyBtn {
            width: 80%;
            height: 35px;
            line-height: 35px;
            margin: 0 auto;
            margin-top: 7.5px;
            text-align: center;
            border-radius: 30px;
            background: #ff4400;
            color: #fff;
            font-size: 14px;
            margin-bottom:7.5px;
        }
        
        #jiangli {
            width: 150px;
            /* height: 150px; */
            background: url("/static/images/7a8968a2730c4a0091a5055582509259.gif");
            position: fixed;
            top: 30%;
            left: 0px;
            right: 0px;
            bottom: 0px;
            margin: auto;
            background-size: 150px;
            background-repeat: no-repeat;
        }
    </style>
    <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/backend/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/mui.js"></script>
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

        function isWeiXin() {
            var ua = window.navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == 'micromessenger') {
                return true;
            } else {
                return false;
            }
        }

        function isIOS() {
            if (browser.versions.ios) {
                return true;
            }
            return false;
        }

        function isAndroid() {
            if (browser.versions.android) {
                return true;
            }
            return false;
        }
    </script>
    <!-- 需要使用才引入 -->
    <script type="text/javascript">
        function getCookie(name) {
            var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

            if (arr = document.cookie.match(reg)) {
                return unescape(arr[2]);
            } else {
                return "";
            }
        }

        function setCookie(name, value, time) {
            var strsec = getsec(time);
            var exp = new Date();
            exp.setTime(exp.getTime() + strsec * 1);
            document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
        }

        function getsec(str) {
            var str1 = str.substring(1, str.length) * 1;
            var str2 = str.substring(0, 1);
            if (str2 == "s") {
                return str1 * 1000;
            } else if (str2 == "h") {
                return str1 * 60 * 60 * 1000;
            } else if (str2 == "d") {
                return str1 * 24 * 60 * 60 * 1000;
            }
        }
    </script>
    <script type="text/javascript">
        // 	var browser;
        $(function() {

            // 如果没有实名认证，就弹窗
            if (window.localStorage.getItem('is_real') == '1') {
                alert("亲，需要实名认证才能申请哦~");
                window.location.href = '{{ route("wxidentityforreal") }}';
            }

            var extension = "";
            // var bankId = '8';
            var bankId = '{{ $bankid }}';
            if (extension != '') {
                if (bankId == 1) {
                    ptApply(1);
                } else if (bankId == 2) {
                    ptApply(2);
                } else if (bankId == 3) {
                    ptApply(3);
                } else if (bankId == 4) {
                    ptApply(4);
                } else if (bankId == 5) {
                    ptApply(5);
                } else if (bankId == 6) {
                    ptApply(6);
                } else if (bankId == 7) {
                    ptApply(7);
                } else if (bankId == 8) {
                    ptApply(8);
                } else if (bankId == 9) {
                    ptApply(9);
                } else if (bankId == 10) {
                    ptApply(10);
                } else if (bankId == 11) {
                    ptApply(11);
                }
            }


            var extension = "null";
            if (extension == null || extension != "1") {
                document.addEventListener('touchstart', function() {
                    function audioAutoPlay() {
                        var bgaudio = document.getElementById('voice');
                        bgaudio.play();
                        bgaudio.pause();
                    }
                    audioAutoPlay();
                });
            }


            if (isWeiXin()) {
                if (browser.versions.ios) {
                    $("header").css("background", "#1a1c20");
                } else if (browser.versions.android) {
                    $("header").css("background", "#393a3e");
                }

                if (browser.versions.ios) {
                    $("#pic").css("background", "#1b1a1f");
                } else if (browser.versions.android) {
                    $("#pic").css("background", "#393a3e");
                }
            } else {
                if (browser.versions.ios) {
                    $("#savePic").show();
                } else if (browser.versions.android) {
                    $("#savePic").show();
                    /* $("#savePic").hide(); */

                }
                $("header").css("background", "#12b7f5");
                if (browser.versions.qqbrowser == false) {
                    $("#pulicBtn").hide();
                }
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

        //根据用户类别
        function searchByType(type) {
            if (type == 0) {
                $("#pxypzl").css("display", "block");
                $("#btcontent").css("display", "none");
                $("#moredata").css("display", "none");
                $("#imageText").css("display", "none");
            } else if (type == 1) {
                $("#pxypzl").css("display", "none");
                $("#btcontent").css("display", "block");
                $("#moredata").css("display", "block");
                $("#appenddata").css("display", "block");
                $("#imageText").css("display", "none");
                $("#btcontent").html("<div id='appenddata'></div>"); //清空原有内容
                morepage(1);
            } else if (type == 2) {
                $("#imageText").css("display", "block");
                $("#pxypzl").css("display", "none");
                $("#btcontent").css("display", "none");
                $("#moredata").css("display", "none");
                $("#btcontent").html("<div id='appenddata'></div>"); //清空原有内容
                morepage(1);
            }

            $("#zlType").val(type);
            changeTab();

        }
        //选项颜色切换
        function changeTab() {
            var zlType = $("#zlType").val();
            if (zlType == 1) {
                $(tab1).css("color", "white");
                $(tab1).css("background-color", "#00A0E6");
                $(tab2).css("background-color", "#E6E6E6");
                $(tab2).css("color", "#666");
                $(tab3).css("background-color", "#E6E6E6");
                $(tab3).css("color", "#666");
            } else if (zlType == 0) {
                $(tab1).css("background-color", "#E6E6E6");
                $(tab1).css("color", "#666");
                $(tab2).css("background-color", "#00A0E6");
                $(tab2).css("color", "white");
                $(tab3).css("background-color", "#E6E6E6");
                $(tab3).css("color", "#666");
            } else if (zlType == 2) {
                $(tab3).css("background-color", "#00A0E6");
                $(tab3).css("color", "white");
                $(tab2).css("background-color", "#E6E6E6");
                $(tab2).css("color", "#666");
                $(tab1).css("background-color", "#E6E6E6");
                $(tab1).css("color", "#666");
            }
        }
        //下一页
        function nextPage() {
            var currPage = $("#currPage").val();
            morepage(parseInt(currPage) + 1);
        }

        function showIMG(obj) {
            var imgurl = $(obj).attr("src");
            $('html').css({
                "height": "100%",
                "overflow": "hidden"
            });
            $('body').css({
                "height": "100%",
                "overflow": "hidden"
            });
            $("#showimg").attr("src", imgurl);
            $("#hgDiv1").show();
            $("#QDStatement").show();
        }

        function closeDiv() {
            $('html').css({
                "height": "100%",
                "overflow": "scroll"
            });
            $('body').css({
                "height": "100%",
                "overflow": "scroll"
            });
            //$('html').removeAttr('style');
            //$('body').removeAttr('style');
            $("#hgDiv1").hide();
            $("#QDStatement").hide();
        }

        function redirectTo(url) {
            location.href = url;
        }

        // function returnUserCenter() {
        //     location.href = "/zy/userCenter.do?f=" +
        //         Math.random();
        // }

        // 返回首页
        function returnIndex() {
            location.href = "/agent/wx?f=" + Math.random();
        }
        var ting3 = null;

        // function iNeedToSpread(xykType) {
        //     var nameType = 1;
        //     var contactType = 3;
        //     $("#xykType").val(xykType);

        //     $("#loadingToast").show();
        //     $.ajax({
        //         url: "/zy/qrimg_xyk_new.do",
        //         type: "post",
        //         data: {
        //             "xykType": xykType,
        //             "nameType": nameType,
        //             "contactType": contactType,
        //             "isgx": false
        //         },
        //         success: function(data) {
        //             var json = eval("(" + data + ")");
        //             if (json.hbcId > 0) {
        //                 ting3 = setInterval("getHBImg(" + json.hbcId + ",3)", 2000);
        //                 $("#posterUrl").val(json.tuiguangURL);
        //             } else {
        //                 if (json.xykImg != null) {
        //                     $("#poster").attr("src", json.xykImg);
        //                     $("#bigimg").attr("src", json.xykImg);
        //                     $("#posterUrl").val(json.tuiguangURL);
        //                     $("#gzgzh1").show();
        //                     $("#posterDiv").show();
        //                     $("#posterBottom").show();
        //                     $("#loadingToast").hide();
        //                 } else if (json.ret == -2) {
        //                     alert("很报歉，您暂时没有推广权限，请进入用户中心联系您的专属客服开通！");
        //                     location.href = "https://zb.ew1.cn/zy/index.do";
        //                 } else if (json.ret == -1) {
        //                     alert("账户异常！");
        //                 } else if (json.ret == 2) {
        //                     alert("两小时生成一次！");
        //                 } else {
        //                     alert("系统异常！");
        //                 }
        //             }
        //         }
        //     });
        // }

        // function getHBImg(hbcId, type) {

        //     $.ajax({
        //         url: "/zy/getHBImg.do",
        //         type: "post",
        //         data: "hbcId=" + hbcId,
        //         success: function(data) {
        //             var json = eval("(" + data + ")");
        //             if (json.ret == 0) {
        //                 if (type == 3) {
        //                     $("#poster").attr("src", json.imgUrl);
        //                     $("#bigimg").attr("src", json.imgUrl);
        //                     $("#gzgzh1").show();
        //                     $("#posterDiv").show();
        //                     $("#posterBottom").show();
        //                     $("#loadingToast").hide();
        //                 }
        //                 clearInterval(ting3);
        //             } else if (json.ret == 3) {
        //                 $("#loadingToast").hide();
        //                 clearInterval(ting3);
        //             }
        //         }
        //     });
        // }

        // function tzApplyPA(xycId) {

        //     $.ajax({
        //         async: false,
        //         type: "post",
        //         url: "/xinyongka/isTXINFO.do",
        //         data: {
        //             "infoId": $("#infoId").val()
        //         },
        //         success: function(ret) {
        //             if (ret == 1) {
        //                 location.href = "https://zb.ew1.cn/xinyongka/creditApply.do?xycId=" + xycId;
        //             } else {
        //                 $('#androidDialog1').fadeIn(200);
        //                 $("#tzBtn").attr("onclick", "location.href='/xinyongka/creditApply.do?xycId=" + xycId + "'");
        //             }
        //         }
        //     });
        // }


        function txHide() {
            $('#androidDialog1').fadeOut(200);
        }
        var browser = {
            versions: function() {
                var u = navigator.userAgent,
                    app = navigator.appVersion;
                return {
                    trident: u.indexOf('Trident') > -1, //IE内核
                    presto: u.indexOf('Presto') > -1, //opera内核
                    webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                    gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
                    mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                    ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                    android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android终端
                    iPhone: u.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器
                    iPad: u.indexOf('iPad') > -1, //是否iPad
                    webApp: u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
                    weixin: u.indexOf('MicroMessenger') > -1, //是否微信 （2015-01-22新增）
                    qq: u.match(/\sQQ/i) == " qq" //是否QQ
                };
            }(),
            language: (navigator.browserLanguage || navigator.language).toLowerCase()
        };

        function isIOS() {
            if (browser.versions.ios) {
                return true;
            }
            return false;
        }

        function isAndroid() {
            if (browser.versions.android) {
                return true;
            }
            return false;
        }

        function ptApply(xycId) {
            if (xycId == '') {
                alert("异常！");
                return;
            }
            $("#xycId").val(xycId);
            location.href = '/agent/wx/applycard/'+xycId;
        }
    </script>
</head>

<body style="margin:0 auto;font-size:13px;color:black;">

    <div id="mainDiv">
        <input type="hidden" id="infoGiveFlow" value="0">
        <input type="hidden" id="infoId" value="14150603">
        <input type="hidden" id="infoIdentity" value="-1">
        <input type="hidden" id="xycId">
        <input type="hidden" id="isMultiple" value="">
        <input type="hidden" id="province" value="">
        <input type="hidden" id="city" value="">
        <input type="hidden" id="bankId" value="{{ $bank->id }}">
        <input type="hidden" id="tp_flag">
        <input type="hidden" id="tp_title" value="">
        <input type="hidden" id="tp_describe" value="">
        <input type="hidden" id="tp_logo" value="">
        <input type="hidden" id="tp_rlink" value="">
        <input type="hidden" id="et_id" value="">
        <input type="hidden" id="tptype" value="">
        <input type="hidden" id="tp_type" value="">
        <input type="hidden" id="tpid" value="">
        <input type="hidden" id="tp_id" value="">
        <input type="hidden" id="open_id" value="{{ $user['id'] }}">
        <input type="hidden" id="tp_integrate" value="">
        <input type="hidden" id="read" value="">
        <input type="hidden" id="tp_rsecond" value="">

        <header>

            <!-- 广告图片 Start -->
            @if (!empty($bank->advertiseImg))
            <img style="width: 100%;display: block;" src="{{ $bank->advertiseImg }}" />
            @endif
            <!-- 广告图片 End -->

            <div class="clear"></div>
        </header>

        <div>
            <!-- 广发 -->
                <!-- <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/aa6f51fd0f774a20a3a04359c5adfb70.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/761be525cd4448ebaeb67e634d2b9fa0.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/472e94f2d553429e913bcdd2dab4856f.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/ec78161a4a5b42c5a87301aad3d30e26.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/22a2f03e4a31497792591893d1420d97.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/1be03b464b744664b4ee2aa746befbf8.gif">
                </div> -->


            <!-- 招商 -->
                <!-- <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/456c04ab9f56b4f45af122bce3de5eb3.jpg">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/042fdec67a9dbfbb4f6485adf759f10c.jpg">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/b150dde0c445a57d3c14562d8f495b5c.jpg">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/06b7d8ffbf48ddc954a663f27e6c6801.jpg">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/1f9fd413a6e9bb173f71771e81bfc625.jpg">
                </div> -->




            <!-- 卡片简介 Start -->
            {!! $bank->cardContent !!}
            <!-- 卡片简介 End -->

            


            <!-- 工商 -->
                <!-- <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/43d1cf65204a4177a2f8b5fed23724cf.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/0afc8bf79d2143528cb41491f74dd0f3.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/4c3b76f7569348bba21ad2a652ace7d9.gif">
                </div> -->

            <!-- 中行 -->
                <!-- <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/f41fda587d8a4e10b17549ef664d3160.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/318f4c51c8a642998aa7dfb7c918bd81.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/c870547276fa4c148035ad1e01050323.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/13c90bf5e9234e5493859387d4a98c0c.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/01d8a8c12a6d4ba2844616bc6514a81d.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/1a1060057de84b28af109ac6e8e96861.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/ce71e20e905641d08d31804cc29dff8f.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/47f0a4d7dd1a406ba6d6e2a17672f858.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/41e2aa0320b4482b8fa7dae9444e2bdb.gif">
                </div> -->


            <!-- 广州 -->
                <!-- <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/80648fcbe7f6444aa9ca9454c5f9270e.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/16ec929c9eb642ae92ba6f310cbe3e95.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/48cf8935d1a04f68a23c2c149b42f5f4.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 70%;display: block;margin-left: 15%;" src="/static/images/db4fb08e4eb440d1941d9ccaedbb1537.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/5535c05e287749feb035bff7729d2a0b.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/25ff4786a70b45a883fdb1f034e1dd26.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/e44468aed5cf422fb850e828682b89de.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/578c422e79ae4e4d9800dcd917294efb.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/91d9a4cb284d4830b84e4e20f8109f03.gif">
                </div> -->

            <!-- 温州 -->

            <!-- 招商校园卡 -->
                <!-- <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/77777a1a1c6c45ad8fcf7ee4c4a152d7.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/c7248761eac14748b9ebabedb1a1a11a.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/2463424fc08b42fabc1c727ccec3982e.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/37dbae26e31749b386276cbba8999006.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/3da8f2d421614826ba0f3856f744ba6b.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/3a2ada0d66bb40a68fa1dc355180934e.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/c26129395fe64d4582b05bbb36c076aa.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/e7dcc791b8974c9f934f937f7e527399.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/597b302f2de34340b0545efb58a25bd1.gif">
                </div> -->

            <!-- 建行 -->
                <!-- <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/1c44afbfbc514889910851c5b5ae9d6d.gif">
                </div>
                <div>
                    <div class="goods_list clearfix">
                        <i class="all-pic ani set_dis" data-href="http://www.ccb.com/cn/html1/office/xyk/subject/17/0630fxsj/index.html?title=%E5%B9%BF%E5%91%8A%E8%AF%A6%E6%83%85">
                                <img src="/static/images/5d8181e2d6074d62ad17e337eaffbbed.gif">
                                <i class="bear set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/index/activit_wond_info/w_id/7"></i>
                        </i>
                        <ul class="threelist list">
                            <li>
                                <div class="md-img set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/Index/activit_wond_info/w_id/5"><img src="/static/images/df673f4fe30d4684a2c3432881257b52.gif" alt=""></div>
                            </li>
                            <li>
                                <div class="md-img set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/index/activit_wond_info/w_id/1"><img src="/static/images/c2859fa02ac04b76bf2091acfc6f25ab.gif" alt=""></div>
                            </li>
                            <li>
                                <div class="md-img set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/index/activit_wond_info/w_id/6"><img src="/static/images/b6b693151aa543358b755461060d2724.gif" alt=""></div>
                            </li>
                        </ul>
                    </div>
                    <div class="goods_list clearfix">
                        <i class="all-pic ani set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/Index/activit_wiki_info/w_id/11">
                            <img src="/static/images/a520f948a0954efc8e4bcc21276206f3.gif">
                            <i class="monk set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/Index/activit_wiki_info/w_id/3"></i>
                        </i>
                        <ul class="threelist list">
                            <li>
                                <div class="md-img set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/Index/activit_wiki_info/w_id/9"><img src="/static/images/493ac9ed2c4c4f33928500395f4b3c9a.gif" alt=""></div>
                            </li>
                            <li>
                                <div class="md-img set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/Index/activit_wiki_info/w_id/4"><img src="/static/images/d2ca41621de34024a2a414bfe342c3b0.gif" alt=""></div>
                            </li>
                            <li>
                                <div class="md-img set_dis" data-href="http://ccbxyk.jikehd.com/weixin.php/Index/activit_wiki_info/w_id/10"><img src="/static/images/c268a99fd7ff4019a4ffaab5bd8f8e26.gif" alt=""></div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/13febc3f526444809f9a37c6fff6383f.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/e9936c6affd44ae3b17c674d8021c9d0.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/d8c5f068925547a3ae1b005093ce2297.gif">
                </div>
                <style>
                    .monk {
                        width: 11.6rem;
                        height: 8.9rem;
                        bottom: 0.3rem;
                        right: 2rem;
                        background: url(/static/images/29a4b33a826d458a9a7060c3fc764a78.gif) no-repeat;
                        background-size: 11.6rem 8.9rem;
                    }
                    
                    .ani {
                        position: relative;
                    }
                    
                    .all-pic,
                    .all-pic i {
                        display: block;
                    }
                    
                    i {
                        font-style: normal;
                    }
                    
                    .all-pic img {
                        width: 100%;
                        vertical-align: top !important;
                    }
                    
                    .ani i {
                        display: block;
                        position: absolute;
                    }
                    
                    .bear {
                        width: 12.4rem;
                        height: 8.6rem;
                        bottom: 0.5rem;
                        right: 2.5rem;
                        background: url(/static/images/004f0cad74114d0988cd466085a03ca3.gif) no-repeat;
                        background-size: 12.4rem 8.6rem;
                    }
                    
                    .threelist li {
                        width: 33.333%;
                        height: 11.5rem;
                    }
                    
                    .list li {
                        box-sizing: border-box;
                        float: left;
                        position: relative;
                    }
                    
                    .threelist li .md-img {
                        width: 10.6rem;
                        height: 11.5rem;
                        background: #fff;
                    }
                    
                    .md-img {
                        display: table-cell;
                        text-align: center;
                        vertical-align: middle;
                    }
                    
                    .goods img {
                        vertical-align: bottom;
                    }
                    
                    .md-img img {
                        max-width: 100%;
                        max-height: 100%;
                        display: block;
                        margin: 0 auto;
                    }
                </style> -->

            <!-- 花旗 -->
                <!-- <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/09490ae67a6c4b01a4b6e8cf06a5ff13.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/0daf99f505804d8ba4f298b7e85c96c5.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/062fdf0e01a442ebb55dead545718ed3.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/fde8bc80e1384fa0b72c2d32feeb81f8.gif">
                </div>
                <div>
                    <img id="ck_header2" style="width: 100%;display: block;" src="/static/images/b3e8727856774c70aa80b78f4d8b5afe.gif">
                </div> -->


        </div>



		@include('agent.layout.floatbtn')



        <!DOCTYPE html>
        <html>

        <head>
            <title>版权信息</title>
            <meta http-equiv="content-type" content="text/html;charset=utf-8" />
            <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
            <style type="text/css">
                #InsPartner {
                    padding: 10px 10px;
                }
                
                .insp_foot {
                    padding-top: 10px;
                }
                
                .insp_foot * {
                    text-align: center;
                }
                
                .logos {
                    padding: 5px 10px;
                }
                
                .logos img {
                    width: 50%;
                    display: block;
                    margin: 0 auto;
                }
            </style>
        </head>

        <body>
            <footer>
                <div id="InsPartner">
                    <div class="insp_foot">
                        <div class="gzCode">
                            <!-- <div><img style="height:32px;margin: 0 auto;padding-top:10px; display: block" src="/static/images/6075b231cd2f4ce7b14200e3acc1f296.gif"></div> -->
                            <div style="font-size: 11px;color: #999;">客服热线：<a href="tel:4000421110" style="color:#999;">400-042-1110</a><!--&nbsp;&nbsp;&nbsp;&nbsp;商务合作：bd@zonbank.cn--></div>
                            <div style="font-size: 11px;color: #999;">&copy;2018 意远 All rights reserved.</div>
                            <div id="gzCodeImg" style="display: none;">
                                <div><img style="width: 100px;margin: 10px auto 0;display: block" src="/static/images/qrcode_for_gh_7ee7e8b0fa71_860.jpg"></div>
                                <div style="font-size: 11px;color: #999;line-height: 30px;">长按识别二维码关注微信公众号：意远合伙人</div>
                                <!-- <div><img style="width: 100px;margin: 10px auto 0;display: block" src="/static/images/ecebd757743c4712a8cafc3acc4d1291.gif"></div>
						<div style="font-size: 11px;color: #999;line-height: 30px;">长按识别二维码关注微信公众号：众银家</div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div style="clear:both;"></div>
            </footer>
        </body>
        <script type="text/javascript">
            $(function() {
                if (isWeiXin()) {
                    $("#gzCodeImg").show();
                }
            });
        </script>

        </html>

    </div>

    <footer>
        <div id="bottomBtn" style="position:fixed;bottom:0;z-index:9996;clear: both;background-color: #fff;">
            <img style="width: 100%;float: left;display: block;background-color: #fff;" src="/static/images/ee15ab9b39c543d787d436580d01e627.gif">

            <!-- 立刻申请 Start -->
            @if (!empty($bank->id))
                <div id="applyBtn" class="applyBtn" onclick="ptApply({!! $bank->id !!})">立即申请</div>
            @endif
            <!-- 立刻申请 End -->


        </div>
    </footer>

    <div style="height: 55px;"></div>

    <!-- 环形进度条 -->
    <div class="circle" style="display: none;">
        <div class="pie_left">
            <div class="left"></div>
        </div>
        <div class="pie_right">
            <div class="right"></div>
        </div>
        <div class="mask">
            <span id="time">0</span>%
        </div>
    </div>
    <!-- <audio id="voice" src="/images/jinbi.mp3" preload="auto"></audio> -->
    <div id="jiangli" style="display: none;">
        <div style="text-align: center;margin-top: 20px;color: #fff;font-size:18px;font-weight: 900; ">阅读奖励</div>

        <div style="text-align: center;color: #fff;margin-top:20px;">+<span style="font-size:30px;font-family: DIN Engschrift Bali;"></span></div>


    </div>
    <!-- 底部导航菜单 end-->

    <link rel="stylesheet" href="/static/css/weui.min.css" />
    <style>
        .weui_dialog {
            top: 40%;
            z-index: 100000;
        }
    </style>
    <div id="weui_dialog_alert" class="weui_dialog_alert" style="display:none;">
        <div class="weui_mask" style="z-index:99999;"></div>
        <div class="weui_dialog">
            <div class="weui_dialog_hd"><strong id="weui_dialog_title" class="weui_dialog_title">提示</strong></div>
            <div class="weui_dialog_bd" id="weui_dialog_text">弹窗内容，告知当前页面信息等</div>
            <div class="weui_dialog_ft">
                <a href="https://zb.ew1.cnjavascript:$('#weui_dialog_alert').hide();" class="weui_btn_dialog primary">确定</a>
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

        /* function custom_alert(content){
        	$("#weui_dialog_text").html(content);
        	$("#weui_dialog_alert").show();
        } */
    </script>
    <script type="text/javascript">
        $(function() {
            $("#gzgzh1").css("height", $(window).height() + "px");

            $("#posterDiv").css("height", $(window).width() + "px");
            $("#posterDiv").css("width", $(window).width() * 0.562 + "px");
            $("#posterDiv").css("left", ($(window).width() - $("#posterDiv").width()) / 2 + "px");

            $("#poster").css("width", $("#posterDiv").width() + "px");

            var extension = "null";
            if (extension == null || extension != "1") {
                if ($("#read").val() == "ok") {
                    $(".circle").show();
                }
            }
        });

        function nameChoose(obj, type) {
            $("#nameType").val(type);
            $(obj).parent().find("div").removeClass("y");
            $(obj).addClass("y");
        }

        function contactChoose(obj, type) {
            var contactType = $("#contactType").val();
            if ($(obj).hasClass("y")) {
                $("#contactType").val(parseInt(contactType) - parseInt(type));
                $(obj).removeClass("y");
            } else {
                $("#contactType").val(parseInt(contactType) + parseInt(type));
                $(obj).addClass("y");
            }

            if ($("#contactType").val() > 0) {
                $("#creatPoster").css("background", "#fed500");
                $("#creatPoster").attr("onclick", "doDiy(this)");
            } else {
                $("#creatPoster").css("background", "#999");
                $("#creatPoster").attr("onclick", "");
            }

            //$(obj).toggleClass("y");
        }

        function copyUrl() {
            var theUrl = $("#posterUrl").val();
            copyToClipboard($("#posterUrl").get(0));
            alert("复制成功");
        }

        function diyPoster() {
            $("#item1").hide();
            $("#item2").show();
        }

        // function doDiy(obj) {
        //     $(obj).html("正在为您生成海报......");
        //     $(obj).attr("onclick", "");
        //     var nameType = $("#nameType").val();
        //     var contactType = $("#contactType").val();
        //     var xykType = $("#xykType").val();

        //     $(".weui_mask_transparent").css("z-index", 100001);
        //     $(".weui_toast").css("z-index", 100003);
        //     $(".weui_dialog").css("z-index", 100005);
        //     $("#loadingToast").show();
        //     $.ajax({
        //         url: "/zy/qrimg_xyk_new.do",
        //         type: "post",
        //         data: {
        //             "xykType": xykType,
        //             "nameType": nameType,
        //             "contactType": contactType,
        //             "isgx": true
        //         },
        //         success: function(data) {
        //             var json = eval("(" + data + ")");
        //             if (json.xykImg != null) {
        //                 $("#poster").attr("src", json.xykImg);
        //                 $("#bigimg").attr("src", json.xykImg);
        //                 $("#posterUrl").val(json.tuiguangURL);
        //                 $("#item1").show();
        //                 $("#item2").hide();
        //                 $("#gzgzh1").show();
        //                 $("#posterDiv").show();
        //                 $("#loadingToast").hide();
        //                 $(obj).attr("onclick", "doDiy(this)");
        //                 $(obj).html("生成海报");
        //             } else if (json.ret == -2) {
        //                 alert("很报歉，您暂时没有推广权限，请进入用户中心联系您的专属客服开通！");
        //                 location.href = "https://zb.ew1.cn/zy/index.do";
        //             } else if (json.ret == -1) {
        //                 alert("账户异常！");
        //                 $(obj).attr("onclick", "doDiy(this)");
        //             } else if (json.ret == 2) {
        //                 alert("两小时生成一次！");
        //                 $(obj).attr("onclick", "doDiy(this)");
        //             } else {
        //                 alert("系统异常！");
        //                 $(obj).attr("onclick", "doDiy(this)");
        //             }
        //         }
        //     });
        // }

        mui.init();

        function copyToClipboard(elem) {
            var targetId = "_hiddenCopyText_";
            var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
            var origSelectionStart, origSelectionEnd;
            if (isInput) {
                target = elem;
                origSelectionStart = elem.selectionStart;
                origSelectionEnd = elem.selectionEnd;
            } else {
                target = document.getElementById(targetId);
                if (!target) {
                    var target = document.createElement("textarea");
                    target.style.position = "absolute";
                    target.style.left = "-9999px";
                    target.style.top = "0";
                    target.id = targetId;
                    document.body.appendChild(target);
                }
                target.textContent = elem.textContent;
            }
            var currentFocus = document.activeElement;
            target.focus();
            target.setSelectionRange(0, target.value.length);
            var succeed;
            try {
                succeed = document.execCommand("copy");
            } catch (e) {
                succeed = false;
            }
            if (currentFocus && typeof currentFocus.focus === "function") {
                currentFocus.focus();
            }

            if (isInput) {
                elem.setSelectionRange(origSelectionStart, origSelectionEnd);
            } else {
                target.textContent = "";
            }
            return succeed;
        }
    </script>


</body>

</html>