// 跳转页面
/*function topage(pageId){
	let topage = document.getElementById(pageId).dataset.topage
	let url = topage+".html"
	location.href = url
}*/
function toPage(page) {
    var url = page + ".html"
    location.href = url
}


// //提示信息
// function prompt(msg) {
//     var prompt_box = document.getElementsByClassName("prompt_box")[0];
//     if (!prompt_box) {
//         var html = "<div class='prompt_box'><span>" + msg + "</span></div>";
//         $("body").append(html);
//     } else {
//         prompt_box.innerHTML = "<span>" + msg + "</span>";
//     }

//     // 3秒钟显示隐藏
//     $(".prompt_box").fadeIn(3000);
//     $(".prompt_box").fadeOut(3000);

// }


// //倒计时，手机验证码倒计时
// var countdown = 60;
// var timer;

// function settime(val) {
//     if (countdown <= 0) {
//         val.removeAttribute("disabled");
//         val.value = "获取验证码";
//         countdown = 60;
//         return;
//     } else {
//         val.setAttribute("disabled", true);
//         val.value = "重新发送(" + countdown + ")";
//         countdown--;
//     }
//     timer = setTimeout(function() {
//         settime(val)
//     }, 1000)
// }

//delete,清空当前input值
/*$(".delete").on("touchend",function(){
	$(this).parents().find("input").val("")
	
});*/
function deletefunc(id) {
    $("#" + id).val("").focus();

}

var i = 0;

//获取图片验证码
function imgVerificationCode() {
    $.ajax({
        type: 'POST',
        contentType: 'application/json',
        url: baseurl + "/v1/verify/img",
        data: {},
        dataType: 'json',
        success: function(data) {
            console.log(data.data);
            imgKey = data.data.data.imgKey; // 获取图片验证时返回的唯一标志
            $(".yzm_img").attr("src", "data:image/png;base64," + data.data.data.imgBase64);
        },
        error: function(data) {
            //alert("error");
        }
    });
}


//刷新图片
$(".refreshImg").on("touchend", function() {
    $(".refreshImg").css({ "transform": "rotateZ(" + i * 360 + "deg)", "transition": "all 1s" });
    i++;
    imgVerificationCode();
});

//获取验证码倒计时

//时间戳转换
function timestampToTime(timestamp) {
    var date = new Date(timestamp);
    Y = date.getFullYear() + '-';
    M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    D = (date.getDate() < 10 ? '0' + date.getDate() : date.getDate()) + ' ';
    h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    m = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
    s = date.getSeconds();

    return Y + M + D + h + m;
}

//时间戳转换
function timestampToTimeToDay(timestamp) {
    var date = new Date(timestamp);
    Y = date.getFullYear() + '-';
    M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    D = (date.getDate() < 10 ? '0' + date.getDate() : date.getDate()) + ' ';
    h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    m = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
    s = date.getSeconds();

    return Y + M + D;
}


//功能暂未开放提示
function notopen() {
    var html = '<div class="notopenbox">';
    html += '<div class="notopenbox">';
    html += '<div class="notopen">';
    html += '<div class="notopen_text">该功能暂未开放，敬请期待~</div>';
    html += '</div></div></div>';
    $("body").append(html);
    $(".notopenbox").fadeIn(1500);
    $(".notopenbox").fadeOut(1500);
}

//图片不可点击
function imgfalse() {
    return false;
}


//身份证校验
function identityCheck(id, value) {
    var reg = /^[0-9A-Za-z]*$/;
    var regC = /^[\u4e00-\u9fa5]+$/;
    var identity = /[0-9]|[xX]/;
    var identityReg = /^[1-9]{1}[0-9]{14}$|^[1-9]{1}[0-9]{16}([0-9]|[xX])$/;

    if (identity.test(value)) {
        if (value.length > 18) {
            value = value.slice(0, 18);
            $("#" + id).val(value);
        } else if (value.length < 18) {
            for (i = 0; i < value.length; i++) {
                var char = value.charAt(i);
                if (/[0-9]/.test(char)) {

                } else {
                    value = value.slice(0, value.length - 1);
                    $("#" + id).val(value);
                }
            }
        } else if (value.length == 18) {
            for (i = 0; i < value.length; i++) {
                var char = value.charAt(i);
                if (identity.test(char)) {

                } else {
                    value = value.slice(0, value.length - 1);
                    $("#" + id).val(value);
                }
            }
        }
    } else {
        value = value.slice(0, value.length - 1)
        value = value.split(value.match(/[\u4E00-\u9FA5]/i))[0];
        $("#" + id).val(value);
    }
}


//设置校验
function passwordCheck(id, value) {
    var reg = /^[0-9]*$/;
    if (reg.test(value)) {
        if (value.length <= 6) {
            $("#" + id).val(value);
        } else {
            value = value.slice(0, 6);
            $("#" + id).val(value);
        }
    } else {

        value = value.slice(0, value.length - 1)
        $("#" + id).val(value);
    }
}