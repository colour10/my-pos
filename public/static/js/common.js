// 重写console.log，禁止输出
console.log = function() {};

// fastclick初始化
window.addEventListener('load', function() {
    FastClick.attach(document.body);
}, false);

// 如果是通过后退到当前页面的，那么就刷新本页面
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
}

// 业务逻辑
var localStorage = window.localStorage;
// openid
var openid = localStorage.getItem('openid');

// 获得授权用户资料
// $.get('/agent/wx/getauthuser', function(response) {
//     console.log('------ 当前用户授权资料 Start ------');
//     console.log(response);
//     // 写入缓存
//     localStorage.setItem('wx_user', response);
//     console.log('------ 当前用户授权资料 End ------');
// });

// 初始化，填充内容
$(function() {

    // 解决ios物理返回键不重新加载js的问题
    var isPageHide = false;
    window.addEventListener('pageshow', function() {
        if (isPageHide) {
            window.location.reload();
        }
    });
    window.addEventListener('pagehide', function() {
        isPageHide = true;
    });

    // 判断是否关注了公众号，已经和下面的实名认证逻辑进行了合并
    // if (!subscribe(openid)) {
    //     console.log('============ 当前微信用户没有关注 ============');
    //     // 关注了公众号才能访问的页面
    //     if ($('meta[name="subscribe"]').attr('content')) {
    //         // 弹窗
    //         popup('意远合伙人', '/static/images/qrcode_344.jpg');
    //     }
    // } else {
    //     console.log('============ 当前微信用户已经关注了 ============');
    // }

    // 邀请人openid
    // 如果地址栏有invite_openid参数，那么就覆盖原来的值
    // 但是如果系统存在这个openid合伙人，那么推荐人就取数据库当中的值，不受传进来的invite_openid的值影响
    // openid存入storage之后，就在js里面调用就可以了~
    // parentopenId和invite_openid保存的值完全一致，一个修改了另外一个马上也跟着修改
    var checkbyopenid_response = checkbyopenid(openid);
    // 判断
    if (!checkbyopenid_response) {
        console.log('============ 当前微信用户不是合伙人 ============');
        // 存入游客
        localStorage.setItem('level', '游客');
    } else {
        localStorage.setItem('level', '普通');
        localStorage.setItem('invite_openid', checkbyopenid_response.data.parentopenid);
        localStorage.setItem('parentopenid', checkbyopenid_response.data.parentopenid);
        // 可以正常分享
        localStorage.setItem('not_agent_errmsg', '');
        // 然后取出里面的合伙人编号，姓名，手机号，身份证等信息，写入缓存
        localStorage.setItem('agent_id', checkbyopenid_response.data.id);
        localStorage.setItem('agent_name', checkbyopenid_response.data.name);
        localStorage.setItem('agent_mobile', checkbyopenid_response.data.mobile);
        localStorage.setItem('agent_id_number', checkbyopenid_response.data.id_number);
        // dom赋值，因为之前姓名、身份证都不是强制的，所以一旦值为空，那么我们就允许编辑
        if ($('#user_name').length > 0) {
            if (!localStorage.getItem('agent_name') || $.trim(localStorage.getItem('agent_name').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#user_name').val('');
                // 然后移除readonly
                $('#user_name').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#user_name').val(localStorage.getItem('agent_name'));
                // 然后锁住input，禁止编辑
                $('#user_name').attr('readonly', 'readonly');
            }
        }
        if ($('#user_identity').length > 0) {
            if (!localStorage.getItem('agent_id_number') || $.trim(localStorage.getItem('agent_id_number').toLowerCase()) == 'null') {
                // 把null值赋值为空，允许编辑
                $('#user_identity').val('');
                // 然后移除readonly
                $('#user_identity').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#user_identity').val(localStorage.getItem('agent_id_number'));
                // 然后锁住input，禁止编辑
                $('#user_identity').attr('readonly', 'readonly');
            }
        }
        if ($('#user_phone').length > 0) {
            if (!localStorage.getItem('agent_mobile') || $.trim(localStorage.getItem('agent_mobile').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#user_phone').val('');
                // 然后移除readonly
                $('#user_phone').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#user_phone').val(localStorage.getItem('agent_mobile'));
                // 然后锁住input，禁止编辑
                $('#user_phone').attr('readonly', 'readonly');
            }
            // 隐藏验证码
            if ($('.wxcode')) {
                $('.wxcode').hide();
            }
        }
        // 手机号码可以修改
        if ($('#telPhone').length > 0) {
            if (!localStorage.getItem('agent_mobile') || $.trim(localStorage.getItem('agent_mobile').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#telPhone').val('');
                // 然后移除readonly
                // $('#telPhone').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#telPhone').val(localStorage.getItem('agent_mobile'));
                // 然后锁住input，禁止编辑
                // $('#telPhone').attr('readonly', 'readonly');
            }
        }
        // 身份证号码可以修改
        if ($('#aiIdCard').length > 0) {
            if (!localStorage.getItem('agent_id_number') || $.trim(localStorage.getItem('agent_id_number').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#aiIdCard').val('');
                // 然后移除readonly
                $('#aiIdCard').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#aiIdCard').val(localStorage.getItem('agent_id_number'));
            }
        }
        // 姓名可以修改
        if ($('#realName').length > 0) {
            if (!localStorage.getItem('agent_name') || $.trim(localStorage.getItem('agent_name').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#realName').val('');
                // 然后移除readonly
                $('#realName').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#realName').val(localStorage.getItem('agent_name'));
            }
        }

        // 填写确认姓名
        if ($('#name').length > 0) {
            if (!localStorage.getItem('agent_name') || $.trim(localStorage.getItem('agent_name').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#name').html('');
                // 然后移除readonly
                $('#name').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#name').html(localStorage.getItem('agent_name'));
            }
        }

        // 填写确认身份证
        if ($('#idcard').length > 0) {
            if (!localStorage.getItem('agent_id_number') || $.trim(localStorage.getItem('agent_id_number').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#idcard').html('');
                // 然后移除readonly
                $('#idcard').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#idcard').html(localStorage.getItem('agent_id_number'));
            }
        }

        // 填写确认手机
        if ($('#phone').length > 0) {
            if (!localStorage.getItem('agent_mobile') || $.trim(localStorage.getItem('agent_mobile').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#phone').html('');
                // 然后移除readonly
                $('#phone').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#phone').html(localStorage.getItem('agent_mobile'));
            }
        }

        // 修改手机
        if ($("#oldPhone").length > 0) {
            if (!localStorage.getItem('agent_mobile') || $.trim(localStorage.getItem('agent_mobile').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#oldPhone').html('');
            } else {
                // 编辑框赋值
                $('#oldPhone').html(localStorage.getItem('agent_mobile'));
            }
        }

        // 绑卡身份证禁止修改
        if ($('#IDnumber').length > 0) {
            if (!localStorage.getItem('agent_id_number') || $.trim(localStorage.getItem('agent_id_number').toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('#IDnumber').html('');
                // 然后移除readonly
                $('#IDnumber').removeAttr('readonly');
            } else {
                // 编辑框赋值
                $('#IDnumber').val(localStorage.getItem('agent_id_number'));
                // 禁止修改
            }
        }
    }

    // 判断是否实名认证，每个页面都要执行
    // 但是在验证实名认证之前，要先判断有没有关注公众号
    // 如果没有实名认证
    // 但是首页比较特殊，不弹窗，所以要先区分是首页还是非首页
    // 是首页
    // if ($('meta[name="is_index"]').attr('content')) {
    //     // 弹窗
    //     console.log('==== 当前首页不弹公众号认证和实名认证 ====');
    // } else {
    //     // 非首页
    //     if (!checkisreal(openid)) {
    //         // 如果没有关注
    //         if (!subscribe(openid)) {
    //             if ($('meta[name="subscribe"]').attr('content')) {
    //                 // 弹窗
    //                 popup('意远合伙人', '/static/images/qrcode_344.jpg');
    //             }
    //         } else {
    //             // 如果已经关注，但是没有实名认证的
    //             // 实名认证弹窗
    //             if ($('#androidDialog1').length > 0) {
    //                 $('#androidDialog1').fadeIn(200);
    //             }

    //             // 实名认证才能访问的页面
    //             if ($('meta[name="identity_for_real"]').attr('content')) {
    //                 // 清空dom，让页面显示一片空白
    //                 $('body').html('');
    //                 // 返回首页
    //                 prompt('抱歉，当前页面必须实名认证后才能访问');
    //                 // 3秒钟后跳转
    //                 setTimeout('location.href="/agent/wx/identityforreal"', 3000);
    //             }

    //         }
    //     } else {
    //         // 如果已经实名认证，但是没有关注的，也要给出提示
    //         if (!subscribe(openid)) {
    //             if ($('meta[name="subscribe"]').attr('content')) {
    //                 // 弹窗
    //                 popup('意远合伙人', '/static/images/qrcode_344.jpg');
    //             }
    //         }
    //     }
    // }


    // 弹窗
    if (!checkisreal(openid)) {
        // 如果没有关注
        if (!subscribe(openid)) {
            if ($('meta[name="subscribe"]').attr('content')) {
                // 弹窗
                popup('意远合伙人', '/static/images/qrcode_344.jpg');
            }
        } else {
            // 如果已经关注，但是没有实名认证的
            // 实名认证弹窗
            if ($('#androidDialog1').length > 0) {
                $('#androidDialog1').fadeIn(200);
            }

            // 实名认证才能访问的页面
            if ($('meta[name="identity_for_real"]').attr('content')) {
                // 清空dom，让页面显示一片空白
                $('body').html('');
                // 返回首页
                prompt('抱歉，当前页面必须实名认证后才能访问');
                // 3秒钟后跳转
                setTimeout('location.href="/agent/wx/identityforreal"', 3000);
            }

        }
    } else {
        // 如果已经实名认证，但是没有关注的，也要给出提示
        if (!subscribe(openid)) {
            if ($('meta[name="subscribe"]').attr('content')) {
                // 弹窗
                popup('意远合伙人', '/static/images/qrcode_344.jpg');
            }
        }
    }


    // 填充账户信息 [首页]
    // 取出合伙人账户信息
    var agentAccountResult = getAgentAccount(openid);
    // 如果存在
    if (agentAccountResult) {
        if ($('.num1').length > 0) {
            $('.num1').text(agentAccountResult.account.available_money.toFixed(2));
        }
        // 个人信息页面
        // 姓名
        if ($('.msg_name').length > 0) {
            // 如果为null，就赋值为空
            if (!agentAccountResult.agent.name || $.trim(agentAccountResult.agent.name.toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('.msg_name').text('');
            } else {
                $('.msg_name').text(agentAccountResult.agent.name);
            }
        }
        // 手机号
        if ($('.msg_mobile').length > 0) {
            // 如果为null，就赋值为空
            if (!agentAccountResult.agent.mobile || $.trim(agentAccountResult.agent.mobile.toLowerCase()) == 'null') {
                // 把null值赋值为空
                $('.msg_mobile').text('');
            } else {
                $('.msg_mobile').text(agentAccountResult.agent.mobile);
            }
        }
        // 总余额
        if ($('.jljTotalNum').length > 0) {
            $('.jljTotalNum').text(agentAccountResult.account.sum_money.toFixed(2));
        }
        // 可提现余额
        if ($('.txANum').length > 0) {
            $('.txANum').text(agentAccountResult.account.available_money.toFixed(2));
        }
        if ($('.reflect_money_num').length > 0) {
            $('.reflect_money_num').text(agentAccountResult.account.available_money.toFixed(2));
        }
        // 手续费
        if ($('.service_charge').length > 0) {
            $(".service_charge").find("span").html(agentAccountResult.method.per_charge);
        }
        // 提现中余额
        if ($('.txDNum').length > 0) {
            $('.txDNum').text(agentAccountResult.account.cash_money.toFixed(2));
        }
    } else {
        if ($('.num1').length > 0) {
            $('.num1').text('0.00');
        }
        // 个人信息页面
        if ($('.msg_name').length > 0) {
            $('.msg_name').text('');
        }
        if ($('.msg_mobile').length > 0) {
            $('.msg_mobile').text('');
        }
        // 总余额
        if ($('.jljTotalNum').length > 0) {
            $('.jljTotalNum').text('0.00');
        }
        // 可提现余额
        if ($('.txANum').length > 0) {
            $('.txANum').text('0.00');
        }
        // 提现中余额
        if ($('.txDNum').length > 0) {
            $('.txDNum').text('0.00');
        }
    }
    // 写入等级 [首页]
    if ($('.info_grade').length > 0) {
        $('.info_grade').text(localStorage.getItem('level'));
    }

    // 取出团队人数 [首页]
    var myTeamNum = getMyTeam(openid);
    if (!myTeamNum) {
        if ($('.num2').length > 0) {
            $('.num2').text('0');
        }
    } else {
        if ($('.num2').length > 0) {
            $('.num2').text(myTeamNum);
        }
    }

    // 推荐卡片 [首页]
    var cardboxesResult = getCardboxes();
    if (cardboxesResult) {
        var data = cardboxesResult.data;
        var len = data.length;
        var html = '';
        for (var i = 0; i < len; i++) {
            html += '<div class="card_item" data-href="/agent/wx/cardinfo/' + data[i].id + '">';
            html += '   <img src="' + data[i].merCardImg + '" class="bankImg" />';
            html += '   <span class="bankname">' + data[i].merCardName + '</span>';
            html += '   <div class="card_label">';
            html += '       <span>' + data[i].littleFlag + '</span>';
            html += '   </div>';
            html += '</div>';
        }
        // 写入dom
        if ($('.card_lists').length > 0) {
            $('.card_lists').html(html);
        }

        // 邀请页面的佣金表
        if ($('.bankLi').length > 0) {
            lis = '';
            html = '';
            lis += '<dt><ul><li>银行</li><li>结算条件</li><li>标准佣金</li></ul></dt>';
            $('.bankLi').append(lis);

            // 循环填充
            for (var i = 0; i < len; i++) {
                html += '<dd>';
                html += '   <ul>';
                html += '       <li><img src="' + data[i].merCardOrderImg + '" />' + data[i].merCardName + '<i></i></li>';
                html += '       <li>核卡成功<br><span>通过率' + data[i].rate + '%</span><i></i></li>';
                html += '       <li>';
                html += '           <p>' + data[i].cardAmount + '元</p><span>' + data[i].method + '</span></li>';
                html += '   </ul>';
                html += '</dd>';
            }
            $('.bankLi').append(html);
        }

        // 办卡点击跳转逻辑
        if ($('.card_item').length > 0) {
            $('.card_item').each(function() {
                $(this).click(function() {
                    // 如果没有实名认证，就弹窗
                    if (!checkisreal(openid)) {
                        // 判断有没有关注了微信公众号
                        // 如果没有关注，则弹出关注
                        if (!subscribe(openid)) {
                            if ($('meta[name="subscribe"]').attr('content')) {
                                // 弹窗
                                popup('意远合伙人', '/static/images/qrcode_344.jpg');
                            }
                        } else {
                            // 如果关注了，就弹出实名认证框
                            if ($('#androidDialog1').length > 0) {
                                $('#androidDialog1').fadeIn(200);
                            }
                        }
                    } else {
                        // 如果没有关注，则弹出关注
                        if (!subscribe(openid)) {
                            if ($('meta[name="subscribe"]').attr('content')) {
                                // 弹窗
                                popup('意远合伙人', '/static/images/qrcode_344.jpg');
                            }
                        } else {
                            // 跳转到实际申请页面
                            var jumpUrl = $(this).attr('data-href');
                            console.log('当前跳转链接为：' + jumpUrl);
                            window.location.href = jumpUrl;
                        }
                    }
                });
            });
        }
    }

    // 填充原来的手机号
    if ($('#beforePhone').length > 0) {
        console.log('==== 存在beforePhone节点 ====');
        $('#beforePhone').val(localStorage.getItem('agent_mobile'));
    } else {
        console.log('==== 不存在beforePhone节点 ====');
    }

    // 填充原来的姓名
    if ($('#beforeName').length > 0) {
        console.log('==== 存在beforeName节点 ====');
        $('#beforeName').val(localStorage.getItem('agent_name'));
    } else {
        console.log('==== 不存在beforeName节点 ====');
    }

    // 零碎赋值
    // 昵称
    if ($('.nickname')) {
        if (localStorage.getItem('nickname')) {
            $('.nickname').text(localStorage.getItem('nickname'));
        }
    }

    // 头像
    if ($('.touxiang')) {
        if (localStorage.getItem('headimgurl')) {
            $('.touxiang').attr('src', localStorage.getItem('headimgurl'));
        }
    }

    // 头像
    if ($('.headimgurl')) {
        if (localStorage.getItem('headimgurl')) {
            $('.headimgurl').attr('src', localStorage.getItem('headimgurl'));
        }
    }

    // 会员级别
    if ($('.hyjb')) {
        if (localStorage.getItem('level')) {
            $('.hyjb').text(localStorage.getItem('level'));
        }
    }
    if ($('.info_grade')) {
        if (localStorage.getItem('level')) {
            $('.info_grade').text(localStorage.getItem('level'));
        }
    }

    // 等级 [我的信息]
    if ($('.msg_level').length > 0) {
        $('.msg_level').text(localStorage.getItem('level'));
    }

    // 这里结束jquery
});

// 实名认证点击
if ($('#androidDialog1').length > 0) {
    $('#tzBtn').click(function() {
        window.location.href = '/agent/wx/identityforreal';
    });
}

// localStorage检测
function GetFromStorage(key) {
    if (!window.localStorage) {
        alert("您的浏览器不支持localstorage");
    } else {
        var storage = window.localStorage;
        return storage.getItem(key);
    }
}

// 设置缓存有效期
function set(key, value) {
    var curTime = new Date().getTime();
    localStorage.setItem(key, JSON.stringify({ data: value, time: curTime }));
}

// 判断是否过期
function get(key, exp) {
    var data = localStorage.getItem(key);
    var dataObj = JSON.parse(data);
    // 打印结果
    // console.log(dataObj);
    // 判断逻辑
    var date = new Date().getTime();
    if (date - dataObj.time > exp) {
        console.log('信息已过期');
    } else {
        console.log('距离过期还有：' + ((exp - date + dataObj.time) / 1000).toFixed(0) + '秒');
        var dataObjDatatoJson = JSON.parse(dataObj.data)
        return dataObjDatatoJson;
    }
}

// 应该已经记录了openid
console.log("openid=" + localStorage.getItem('openid'));
console.log("parentopenid=" + localStorage.getItem('parentopenid'));

// 邀请办卡人id
// 应该已经记录了invite_openid
console.log("invite_openid=" + localStorage.getItem('invite_openid'));
// 测试合伙人编号，手机号，身份证号，姓名
console.log("agent_id=" + localStorage.getItem('agent_id'));
console.log("agent_name=" + localStorage.getItem('agent_name'));
console.log("agent_mobile=" + localStorage.getItem('agent_mobile'));
console.log("agent_id_number=" + localStorage.getItem('agent_id_number'));
// 打印参数
console.log("GetQueryString_parentopenId=" + GetQueryString('parentopenId'));
console.log("GetQueryString_invite_openid=" + GetQueryString('invite_openid'));

// 采用正则表达式获取地址栏参数，并且解决了当参数中有中文的时候， 就出现乱码的问题
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return decodeURI(r[2]);
    return null;
}

// 手机号码隐藏中间4位
function hidephone(cellValue) {
    // 判断是否为11位的纯数字
    if (Number(cellValue) && String(cellValue).length === 11) {
        var mobile = String(cellValue);
        var reg = /^(\d{3})\d{4}(\d{4})$/;
        return mobile.replace(reg, '$1****$2');
    } else {
        return cellValue;
    }
}

//保留两位小数  
//功能：将浮点数四舍五入，取小数点后2位 
function toDecimal(x) {
    var f = parseFloat(x);
    if (isNaN(f)) {
        return;
    }
    f = Math.round(x * 100) / 100;
    return f;
}

// 关闭未登录弹窗
function txHide() {
    if ($('#androidDialog1').length > 0) {
        $('#androidDialog1').fadeOut(200);
    }
}

// 取出storage当中的对象
function getObjItem(key) {
    // 取出对象
    var data = localStorage.getItem(key);
    var dataObj = JSON.parse(data);
    // 返回对象
    return dataObj;
}


/**
 * 提示信息【美化处理】
 * @param {string} msg 提示信息
 * @return {null}
 */
function prompt(msg) {
    var prompt_box = document.getElementsByClassName("prompt_box")[0];
    if (!prompt_box) {
        var html = "<div class='prompt_box'><span>" + msg + "</span></div>";
        $("body").append(html);
    } else {
        prompt_box.innerHTML = "<span>" + msg + "</span>";
    }

    // 3秒钟显示隐藏
    $(".prompt_box").fadeIn(3000);
    $(".prompt_box").fadeOut(3000);

}


/**
 * 发送验证码短信逻辑1.1
 * @param {tel} tel 待发送的手机号码
 * @param {obj} obj 倒计时控制的节点,是一个jQuery对象
 * @return {null}
 */
function wxcreatecode(tel, obj) {
    $.get('/agent/wx/createcode', function(response) {
        // 打印验证码
        console.log('==== 打印验证码 Start ====');
        console.log(response);
        console.log('==== 打印验证码 End ====');
        // 发送验证码
        sendmsg(tel, '2002', response);
        // 前端状态显示倒计时
        settime(obj);
    });
}

// 发送验证码短信逻辑1.2
// 然后进行倒计时120秒
var countdown = 120;


/**
 * 发送验证码短信逻辑1.3
 * 发送验证码倒计时
 * @param {object} obj 短信倒计时操作节点，object
 * @return {null}
 */
function settime(obj) {
    if (countdown == 0) {
        obj.attr('disabled', false);
        //obj.removeattr("disabled");
        obj.val("获取验证码");
        countdown = 60;
        return;
    } else {
        obj.attr('disabled', true);
        obj.val("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function() {
        settime(obj)
    }, 1000)
}


/**
 * 发送短信
 * @param {number} tel 待发送的手机号码
 * @param {string} sendid 短信模板
 * @param {string} sendmsg 短信内容
 * @return {string}
 */
function sendmsg(tel, sendid, sendmsg) {
    // 发送短信
    $.post("/agent/wx/sendmsg", {
        'tel': tel,
        'sendid': sendid,
        'sendmsg': sendmsg,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 数据返回测试
        console.log('==== 打印发送短信结果 Start ====');
        console.log(response);
        console.log('==== 打印发送短信结果 End ====');
        // 逻辑
        if (response.errcode == '0') {
            return prompt('短信发送成功');
        } else {
            // 逻辑判断
            if (response.errcode == '1') {
                return prompt('手机号码格式错误');
            } else if (response.errcode == '2') {
                return prompt('IP被拒绝');
            } else if (response.errcode == '3') {
                return prompt('短信模版ID不存在或审核未通过');
            } else if (response.errcode == '4') {
                return prompt('appkey不存在');
            } else if (response.errcode == '5') {
                return prompt('param内容数据格式错误（与短信模版的变量数量不相符）');
            } else if (response.errcode == '6') {
                return prompt('必填参数不正确');
            } else if (response.errcode == '7') {
                return prompt('用户余额不足');
            } else if (response.errcode == '8') {
                return prompt('param内容不合规或含违禁词');
            } else if (response.errcode == '9') {
                return prompt('param内容长度超限');
            } else if (response.errcode == '10') {
                return prompt('发送超频，请稍后再试');
            } else if (response.errcode == '-1') {
                return prompt('其他原因发送失败，请联系我们');
            } else {
                return prompt('未知错误，短信发送失败！');
            }
        }
    }).error(function(response) {
        return prompt('未知错误，短信发送失败！');
    });
}


/**
 * 判断验证码是否正确
 * @param {number} capcha 数字验证码
 * @return {bool}
 */
function checkwxcode(capcha) {
    // 初始化
    var result = false;
    // 同步
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/checkregcode', {
        'capcha': capcha,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 如果不为0，说明不正确
        if (response.code != '0') {
            result = false;
        } else {
            result = true;
        }
    });
    // 返回
    return result;
}

/**
 * 判断四要素核查是否一致
 * @param {string} name 持卡人姓名
 * @param {number} idcardno 身份证号
 * @param {number} bankcardno 银行卡号
 * @param {number} tel 手机号码
 * @param {string} openid 微信openid
 * @return {bool}
 */
function checkbankcard(name, idcardno, bankcardno, tel, openid) {
    // 初始化
    var result = false;
    // 同步
    $.ajaxSetup({ async: false });
    // 逻辑
    $.ajax({
        type: 'post',
        url: "/agent/wx/checkbankcard",
        data: {
            'name': name,
            'idcardno': idcardno,
            'bankcardno': bankcardno,
            'tel': tel,
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'openid': openid,
        },
        timeout: 999999,
        beforeSend: function() {
            index = layer.load(1, {
                shade: [0.5, '#fff'] //0.1透明度的白色背景
            });
        },
        complete: function() {
            layer.close(index);
        },
        success: function(response) {
            // 打印返回结果
            console.log('==== 打印结果 Start ====');
            console.log(response);
            console.log('==== 打印结果 End ====');
            // 验证
            // 查询成功
            if (response.isok == '1') {
                if (response.code == '1') {
                    // 四要素验证通过
                    // 返回真
                    result = true;
                } else {
                    // 四要素验证不通过，返回假
                    result = false;
                }
            } else {
                // 查询失败
                result = false;
            }
        },
        error: function(response) {
            // 程序错误也返回假
            result = false;
        },
    });
    // 最终返回
    return result;
}


/**
 * 判断合伙人是否已经绑卡
 * @param {string} openid 微信openid
 * @return {bool}
 */
function checktiecard(openid) {
    // 初始化
    var result = false;
    // 同步
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/cards', {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'openid': openid,
    }, function(response) {
        // 取出绑卡数据
        console.log('==== 取出绑卡数据 Start ====');
        console.log(response);
        console.log('==== 取出绑卡数据 End ====');
        // 判断逻辑
        // 还未绑卡
        if (response.length == '0') {
            result = false;
        } else {
            // 已经绑卡
            result = true;
        }
    });
    // 返回
    return result;
}



/**
 * 查找用户输入的姓名和数据库录入的是否一致，否则禁止绑卡
 * @param {string} name 用户输入的名字
 * @param {string} openid 用户微信openid
 * @return {bool}
 */
function checkUser(name, openid) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/checkuser', {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'openid': openid,
        'realName': name,
    }, function(response) {
        // 打印录入结果
        console.log('-------判断姓名能否被使用 start-------');
        console.log(response);
        console.log('-------判断姓名能否被使用 end-------');
        // 判断逻辑
        // 如果可以使用
        if (response.code == '0') {
            result = true;
        } else {
            result = false;
        }
    });
    // 返回
    return result;
}

/**
 * 取出当前用户唯一绑定卡
 * @param {string} openid 用户微信openid
 * @return {bool}
 */
function getFirstCard(openid) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/firstcard', {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'openid': openid,
    }, function(response) {
        // 打印录入结果
        console.log('-------取出默认绑定卡 start-------');
        console.log(response);
        console.log('-------取出默认绑定卡 end-------');
        // 判断逻辑
        // 如果不存在，说明没有绑定
        if (response.length == '0') {
            result = false;
        } else {
            result = response;
        }
    });
    // 返回
    return result;
}

/**
 * 判断当前微信openid是否为合伙人
 * @param {string} openid 用户微信openid
 * @return {bool}
 */
function checkbyopenid(openid) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/checkbyopenid', {
        'openid': openid,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        console.log('==== 判断当前微信openid是否为合伙人 Start ====');
        console.log(response);
        console.log('==== 判断当前微信openid是否为合伙人 End ====');
        // 判断逻辑
        // 如果存在
        if (response.code == '0') {
            result = response;
        } else {
            result = false;
        }
    });
    // 返回
    return result;
}

/**
 * 判断当前微信合伙人是否已经进行了实名认证
 * @param {string} openid 用户微信openid
 * @return {bool}
 */
function checkisreal(openid) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/wxisreal', {
        'openid': openid,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        console.log('==== 是否实名认证 Start ====');
        console.log(response);
        console.log('==== 是否实名认证 End ====');
        // 判断逻辑
        // 如果没有实名认证
        if (response.code == '1') {
            result = false;
        } else {
            result = true;
        }
    });
    // 返回
    return result;
}

/**
 * 取出团队人数
 * @param {string} openid 微信用户openid
 * @return {bool}
 */
function getMyTeam(openid) {
    // 初始化
    var result = 0;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/myteamapi', {
        'openid': openid,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 打印结果
        console.log('-------取出团队人数 start-------');
        console.log(response);
        console.log('-------取出团队人数 end-------');
        // 判断逻辑
        // 如果有下级
        if (response) {
            result = response.length;
        } else {
            result = 0;
        }
    });
    // 返回
    return result;
}

/**
 * 取出合伙人各项参数
 * @param {string} openid 微信用户openid
 * @return {bool}
 */
function getAgentAccount(openid) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/my', {
        'openid': openid,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 打印结果
        console.log('-------取出合伙人账户参数 start-------');
        console.log(response);
        console.log('-------取出合伙人账户参数 end-------');
        // 判断逻辑
        // 如果存在
        if (response.code == '0') {
            result = response;
        } else {
            result = false;
        }
    });
    // 返回
    return result;
}

/**
 * 推荐银行列表
 * @return {bool}
 */
function getCardboxes() {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/cardboxes', {
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 打印结果
        console.log('-------取出推荐银行列表 start-------');
        console.log(response);
        console.log('-------取出推荐银行列表 end-------');
        // 判断逻辑
        // 如果存在
        if (response.code == '0') {
            result = response;
        } else {
            result = false;
        }
    });
    // 返回
    return result;
}

// 链接判断是否实名认证，然后进行跳转
/**
 * 
 * @param {object} obj 传一个jQuery对象
 * @return {null}
 */
function identityForReal(obj) {
    // 转换成jQuery对象
    obj = $(obj);
    // 如果没有实名认证，就弹窗
    if (!checkisreal(localStorage.getItem('openid'))) {
        if ($('#androidDialog1').length > 0) {
            $('#androidDialog1').fadeIn(200);
        }
    } else {
        // 跳转到实际申请页面
        var jumpUrl = obj.attr('data-href');
        window.location.href = jumpUrl;
    }
}

/**
 * 取出微信公众号所有设置参数
 * @return {bool}
 */
function getWxConfig() {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/config', {
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 打印结果
        console.log('-------取出微信公众号配置参数 start-------');
        console.log(response);
        console.log('-------取出微信公众号配置参数 end-------');
        // 判断逻辑
        // 如果存在
        result = response;
    });
    // 返回
    return result;
}

/**
 * 引入微信jssdk
 * @return {bool}
 */
function getSignPackage(app_id, secret) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/getsignpackage', {
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 打印结果
        console.log('-------取出微信jssdk参数 start-------');
        console.log(response);
        console.log('-------取出微信jssdk参数 end-------');
        // 判断逻辑
        // 如果存在
        result = response;
    });
    // 返回
    return result;
}

/**
 * 微信分享，暂时不可用，原因正在查找...
 */
function wxshare(app_id, secret, openid, timestamp, nonceStr, signature) {
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
    var share_appuuid = app_id;
    var share_wxurlsecret = secret;
    var share_openid = openid;

    wx.config({
        // 测试时打开，正式上线后关闭
        // debug: true,
        debug: false,
        appId: app_id,
        timestamp: timestamp,
        nonceStr: nonceStr,
        signature: signature,
        jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            'onMenuShareAppMessage',
            'onMenuShareTimeline',
            'hideAllNonBaseMenuItem',
            'showMenuItems'
        ]
    });

    // config配置成功后进入
    wx.ready(function() {
        wx.hideAllNonBaseMenuItem();
        wx.showMenuItems({
            menuList: [
                'menuItem:share:appMessage',
                'menuItem:share:timeline'
            ]
        });
        // 分享给好友
        wx.onMenuShareAppMessage({
            title: "让我们有福一起享，有财一起发！", // 分享标题
            desc: "我在意远办卡方便快捷，还有钱拿，快来和我一起发财吧~", // 分享描述
            link: "http://" + document.domain + "/agent/wx?wxshare=wxshare" + "&appuuid=" + share_appuuid + "&parentopenId=" + share_openid,
            imgUrl: "http://" + document.domain + "/static/images/icon-logo.png", // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            success: function() {
                // 成功之后的回调
                // alert("分享成功");
                // 如果不为空，说明找不到合伙人，就提示注册
                if (localStorage.getItem('not_agent_errmsg')) {
                    // Layer信息框
                    layer.open({
                        content: localStorage.getItem('not_agent_errmsg'),
                        btn: '我知道了'
                    });
                } else {
                    prompt("分享成功");
                }
            }
        });

        // 分享到朋友圈
        wx.onMenuShareTimeline({
            title: "让我们有福一起享，有财一起发！", // 分享标题
            desc: "我在意远办卡方便快捷，还有钱拿，快来和我一起发财吧~", // 分享描述
            link: "http://" + document.domain + "/agent/wx?wxshare=wxshare" + "&appuuid=" + share_appuuid + "&parentopenId=" + share_openid,
            imgUrl: "http://" + document.domain + "/static/images/icon-logo.png", // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            success: function() {
                // 成功之后的回调
                // alert("分享成功");
                // 如果不为空，说明找不到合伙人，就提示注册
                if (localStorage.getItem('not_agent_errmsg')) {
                    // Layer信息框
                    layer.open({
                        content: localStorage.getItem('not_agent_errmsg'),
                        btn: '我知道了'
                    });
                } else {
                    prompt("分享成功");
                }
            }
        });
    });
    wx.error(function(res) {
        //打印错误消息。及把 debug:false,设置为debug:true就可以直接在网页上看到弹出的错误提示
    });
}

/**
 * 取出当前微信用户的提现记录
 * @param {string} openid 微信用户openid
 * @return {bool}
 */
function getWithdraws(openid) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/withdraws', {
        'openid': openid,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 打印结果
        console.log('-------取出提现记录 start-------');
        console.log(response);
        console.log('-------取出提现记录 end-------');
        // 判断逻辑
        // 如果存在
        if (response.length == '0') {
            result = false;
        } else {
            result = response;
        }
    });
    // 返回
    return result;
}

/**
 * 修改合伙人手机号
 * @param {string} openid 微信用户openid
 * @param {number} mobile 新手机号
 * @return {bool}
 */
function modifyMobile(openid, mobile) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑
    $.post('/agent/wx/modifymobile', {
        'openid': openid,
        'mobile': mobile,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 打印结果
        console.log('-------取出修改合伙人手机号执行结果 start-------');
        console.log(response);
        console.log('-------取出修改合伙人手机号执行结果 end-------');
        // 判断逻辑
        // 如果成功
        if (response.code == '0') {
            result = true;
        } else {
            result = false;
        }
    }).error(function() {
        result = false;
    });
    // 返回
    return result;
}


/**
 * 清除验证码缓存
 * @return {bool}
 */
function removewxyzm() {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑    
    $.post('/agent/wx/removewxyzm', {
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        if (response.code == '0') {
            result = true;
        } else {
            result = false;
        }
    });
    // 返回
    return result;
}


/**
 * 设置交易密码
 * @param {number} id  合伙人id
 * @param {number} password 提现密码
 * @param {number} password_confirmation 确认提现密码
 */
function setPwd(id, password, password_confirmation) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑    
    $.post('/agent/wx/' + id + '/setpwd', {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        "_method": "PUT",
        'cash_password': password,
        'cash_password_confirmation': password_confirmation,
    }, function(response) {
        if (response.code == '0') {
            result = true;
        } else {
            result = false;
        }
    }).error(function() {
        result = false;
    });
    // 返回
    return result;
}

// 判断是否通过微信访问
function isWeiXin() {
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
}

// 判断是否为IOS设备
function isIOS() {
    if (browser.versions.ios) {
        return true;
    }
    return false;
}

// 判断是否为安卓设备
function isAndroid() {
    if (browser.versions.android) {
        return true;
    }
    return false;
}


/**
 * 检测当前身份证能否被使用
 * @param {string} openid 微信用户openid
 * @param {*} id_number 身份证号
 */
function checkidnumbervalid(openid, id_number) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑    
    $.post('/agent/wx/checkidnumbervalid', {
        'openid': openid,
        '_token': $('meta[name="csrf-token"]').attr('content'),
        "id_number": id_number,
    }, function(response) {
        if (response.code == '0') {
            result = true;
        } else {
            result = false;
        }
    }).error(function() {
        result = false;
    });
    // 返回
    return result;
}

/**
 * JS从一个数组中随机取出一个元素或者几个元素
 */
function getRandomArrayElements(arr, count) {
    var shuffled = arr.slice(0),
        i = arr.length,
        min = i - count,
        temp, index;
    while (i-- > min) {
        index = Math.floor((i + 1) * Math.random());
        temp = shuffled[index];
        shuffled[index] = shuffled[i];
        shuffled[i] = temp;
    }
    return shuffled.slice(min);
}

/**
 * 判断当前用户是否关注了微信公众号
 */
function subscribe(openid) {
    // 初始化
    var result = false;
    // 同步处理
    $.ajaxSetup({ async: false });
    // 逻辑    
    $.post('/wechat/getuser', {
        'openid': openid,
        '_token': $('meta[name="csrf-token"]').attr('content'),
    }, function(response) {
        // 打印结果
        console.log('-------取出当前用户是否关注了微信公众号执行结果 start-------');
        console.log(response);
        console.log('-------取出当前用户是否关注了微信公众号执行结果 end-------');
        if (response.subscribe == '1') {
            result = true;
        } else {
            result = false;
        }
    }).error(function() {
        result = false;
    });
    // 返回
    return result;
}

/**
 * 未关注公众号提示关注 [可以关闭]
 * @param {string} name 二维码名称
 * @param {string} img_url 二维码图片
 */
function popup(name, img_url) {
    var html = '';
    html += '<div class="popup">';
    html += '<img class="popup_close" onclick="popup_close();" src="/static/images/close2.png" />';

    //公众号名称
    html += '<div class="popup_text">长按二维码识别关注：<span class="popup_name">' + name + '</span></div>';
    //公众号二维码
    html += '<img class="popup_img" src="' + img_url + '"  />';
    html += '</div>';
    $("body").append(html);
}

/**
 * 未关注公众号提示关注 [不可以关闭]
 * @param {string} name 二维码名称
 * @param {string} img_url 二维码图片
 */
function popupForbidClose(name, img_url) {
    var html = '';
    html += '<div class="popup">';
    //公众号名称
    html += '<div class="popup_text">请先长按二维码关注：<span class="popup_name">' + name + '</span></div>';
    //公众号二维码
    html += '<img class="popup_img" src="' + img_url + '"  />';
    html += '</div>';
    $("body").append(html);
}

/**
 * 关闭二维码
 */
function popup_close() {
    $(".popup").remove();
}