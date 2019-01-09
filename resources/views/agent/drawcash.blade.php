<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    @include('agent.layout.identity-meta')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>提现页面</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/incentive.css') }}">
</head>

<body>
    <div class="reflectInfo">
        <img src="/static/images/tixianjilu_bg.png" class="reflectBg nopoint" />
        <div class="reflectDetail">
            <text class="reflectDes">可提现金额(元)</text>

            <div class="reflectMoney">
                <span class="reflect_money_num">--.--</span>
            </div>

        </div>
    </div>
    <div class="drawcash_box">
        <div class="drawcash_item">
            <span class="drawcash_item_text">提现金额</span>
            <input id="draw_money" type="number" step="0.01" placeholder="请输入提取金额" oninput="drawcash_number('draw_money',value)" onkeydown="" name="sum" />
            <span class="drawcash_all" onclick="drawcashAll();">全部提现</span>
        </div>

        <div class="drawcash_item">
            <text class="drawcash_item_text">交易密码</text>
            <input id="deal_id" type="tel" placeholder="请输入交易密码" oninput="passwordCheck('deal_id',value)" style="-webkit-text-security:disc" name="cash_password">
            <img onclick="deletefunc('deal_id')" src="/static/images/delete.png" class="delete" />
        </div>

        <div class="drawcash_confirm">
            <span>温馨提示：</span>
            <span>奖励金满<span class="platthreshold" style="display: inline;">2</span>元后，才可提现。</span>
            <span>22:30-07:00不可提现。</span>
        </div>

        <div class="service_charge">
            手续费：<span></span>元
        </div>
        <div class="drawcash_btnbox">
            <div class="drawcash_btn" onclick="drawcash();">确认提现</div>
        </div>

        </form>

    </div>

    @include('agent.layout.floatbtn')

    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src='/static/js/bootstrap.js'></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
    <script type="text/javascript">
        // 赋值
        // 微信openid
        var openid = window.localStorage.getItem('openid');

        // 初始化
        $(function() {
            // 从服务器获取当前用户的银行卡号
            var html = '';
            var firstcardResult = getFirstCard(openid);
            if (firstcardResult) {
                // 卡号写入dom
                $('.service_charge').append('<input type="hidden" id="card_id" name="card_id" value="' + firstcardResult.data.id + '">');
            }
        });

        //提现金额限制
        $("#draw_money").bind("input propertychange", function() {
            var val = $("#draw_money").val();
        })

        //全部提现
        function drawcashAll() {
            var total_money = Number($(".reflect_money_num").html());
            $("#draw_money").val(total_money);
        }

        //提现
        var tx_status = false;

        // 提现逻辑
        function drawcash() {
            tx_status = true;
            forbiddenClick();
            if (tx_status) {
                // 如果密码，金额校验通过，则结果为1
                var status = accounting();
                if (status != 1) {
                    tx_status = false;
                    forbiddenClick();
                    return;
                } else {
                    // ajax提交
                    // 然后继续
                    // 如果都通过了，那么就ajax提交
                    $.ajax({
                        type: 'post',
                        url: "{{ route('AgentauthCash') }}",
                        data: {
                            "_token": $('meta[name="csrf-token"]').attr('content'),
                            "card_id": $('#card_id').val(),
                            "sum": $('#draw_money').val(),
                            'openid': openid,
                            "cash_password": $('#deal_id').val(),
                        },
                        dataType: 'json',
                        timeout: 99999,
                        success: function(data) {
                            // 打印提现返回结果
                            console.log('-------取出提现返回结果 start-------');
                            console.log(data);
                            console.log('-------取出提现返回结果 end-------');
                            // 判断
                            if (data.code == '0') {
                                // 提示
                                prompt(data.msg);
                                // 修改可提现余额的值
                                $('.reflect_money_num').text(data.data['available_money']);
                                tx_status = false;
                                forbiddenClick();
                                setTimeout(function() {
                                    location.reload();
                                }, 3000);
                            } else {
                                // 如果提现密码不正确，则清空input
                                if (data.msg == '提现密码不正确！') {
                                    $('#deal_id').val("");
                                    $('#deal_id').focus();
                                }
                                // 提示
                                prompt(data.msg);
                                tx_status = false;
                                forbiddenClick();
                                setTimeout(function() {
                                    location.reload();
                                }, 3000);
                            }
                        },
                        error: function(data) {
                            // 捕捉错误
                            if (data.status == '422') {
                                var jsonObj = JSON.parse(data.responseText);
                                var errors = jsonObj.errors;
                                for (var item in errors) {
                                    for (var i = 0, len = errors[item].length; i < len; i++) {
                                        prompt(errors[item][i]);
                                        setTimeout(function() {
                                            location.reload();
                                        }, 3000);
                                    }
                                }
                            } else {
                                // var jsonObj = JSON.parse(data.responseText);
                                console.log(data);
                                prompt('网络错误，请重试...');
                                setTimeout(function() {
                                    location.reload();
                                }, 3000);
                            }
                        },
                    });

                    // 禁止跳转
                    return false;
                }
            } else {
                tx_status = false;
                forbiddenClick();
                return;
            }

        }

        //防止二次点击
        function forbiddenClick() {
            var html = "<div class='zzc' style='position: fixed;top: 0;left: 0;width: 100%;height: 100%;z-index: 33333333;'></div>";
            if (tx_status == true) {
                $("body").append(html);
            } else {
                $(".zzc").remove();
            }
        }

        //金额,密码校验
        function accounting() {
            //输入金额
            var draw_money = Number($("#draw_money").val());
            //可提现
            var reflect_money_num = Number($(".reflect_money_num").html());

            var password = $("#deal_id").val();

            if (draw_money == 0) {
                prompt("请输入提现金额！")
                return 0;
            }
            if (draw_money > reflect_money_num) {
                prompt("提现金额应小于等于可提现金额！")
                return 0;
            } else if (password.length != 6) {
                prompt("请输入6位数字密码！")
                return 0;
            } else {
                return 1;
            }
        }

        function drawcash_number(id, value) {
            value = clearNoNum(value);
            $("#" + id).val(value);
        }

        function clearNoNum(value) {
            console.log(value);
            //先把非数字的都替换掉，除了数字和.
            value = value.replace(/[^\d.]/g, "");
            //保证只有出现一个.而没有多个.
            value = value.replace(/\.{2,}/g, ".");
            //必须保证第一个为数字而不是.
            value = value.replace(/^\./g, "");
            //保证.只出现一次，而不能出现两次以上
            value = value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
            //只能输入两个小数
            value = value.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3');

            return value;
        }

        function drawcash_down(id, event) {
            var value = event.path[0].value;
            drawcash_number('draw_money', value);
        }
    </script>

</body>

</html>