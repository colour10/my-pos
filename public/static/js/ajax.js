// ajax提交函数

/**
 * 获得form表单的formdata值
 * @param {string} formname form的name值
 * @return {object} 获得form表单的formdata值
 */
function getFormData(formname) {
    var form = document.getElementsByName(formname)[0];
    return new FormData(form);
}

/**
 * 封装的ajax-post方法
 * 几乎每个页面都要用，所以进行了封装
 * @param {string} type 数据请求的方式(get/post)
 * @param {string} url 数据接收的地址
 * @param {object} fd 当前表单的FormData对象，或者是表单对象集合
 * @param {string} successUrl 页面执行成功后跳转的url地址，如果不填写则默认是当前页面的地址
 * @param {number} second 设置几秒后页面跳转，默认是3秒
 * @return {bool}
 */
function ajax(type, url, fd, successUrl = window.location.href, second = 3) {
    // 处理逻辑
    $.ajax({
        type: type,
        url: url,
        data: fd,
        dataType: 'json',
        timeout: 99999,
        // 下面两个和delete方法冲突，但是post还是需要的
        // processData只对post有效
        processData: false,
        contentType: false,
        beforeSend: function() {
            index = layer.load(1, {
                shade: [0.5, '#fff'] //0.1透明度的白色背景
            });
        },
        complete: function() {
            layer.close(index);
        },
        success: function(data) {
            // 打印返回值
            console.log('==== 打印结果 Start ====');
            console.log(data);
            console.log('==== 打印结果 End ====');
            // 判断逻辑
            if (data.code == '0') {
                prompt(data.msg);
                // 3000毫秒后跳转
                setTimeout("window.location.href = '" + successUrl + "'", second * 1000);
            } else {
                // 返回错误
                prompt(data.msg);
            }
        },
        error: function(data) {
            // 判断逻辑
            if (data.status == 422) {
                var jsonObj = JSON.parse(data.responseText);
                var errors = jsonObj.errors;
                for (var item in errors) {
                    for (var i = 0, len = errors[item].length; i < len; i++) {
                        prompt(errors[item][i]);
                        return false;
                    }
                }
            } else {
                // 提示
                prompt('程序内部错误，请联系管理员！');
                return false;
            }
        },
    });
    // 返回假，禁止自动跳转
    return false;
}


/**
 * 封装的ajax-default方法
 * 几乎每个页面都要用，所以进行了封装
 * @param {string} type 数据请求的方式(get/post)
 * @param {string} url 数据接收的地址
 * @param {object} fd 当前表单的FormData对象，或者是表单对象集合
 * @param {string} successUrl 页面执行成功后跳转的url地址，如果不填写则默认是当前页面的地址
 * @param {number} second 设置几秒后页面跳转，默认是3秒
 * @return {bool}
 */
function defaultajax(type, url, fd, successUrl = window.location.href, second = 3) {
    // 处理逻辑
    $.ajax({
        type: type,
        url: url,
        data: fd,
        dataType: 'json',
        timeout: 99999,
        beforeSend: function() {
            index = layer.load(1, {
                shade: [0.5, '#fff'] //0.1透明度的白色背景
            });
        },
        complete: function() {
            layer.close(index);
        },
        success: function(data) {
            console.log('==== 打印结果 Start ====');
            console.log(data);
            console.log('==== 打印结果 End ====');
            // 判断逻辑
            if (data.code == '0') {
                prompt(data.msg);
                // 3000毫秒后跳转
                setTimeout("window.location.href = '" + successUrl + "'", second * 1000);
            } else {
                // 返回错误
                prompt(data.msg);
            }
        },
        error: function(data) {
            // 判断逻辑
            if (data.status == 422) {
                var jsonObj = JSON.parse(data.responseText);
                var errors = jsonObj.errors;
                for (var item in errors) {
                    for (var i = 0, len = errors[item].length; i < len; i++) {
                        prompt(errors[item][i]);
                        return false;
                    }
                }
            } else {
                // 提示
                prompt('程序内部错误，请联系管理员！');
                return false;
            }
        },
    });
    // 返回假，禁止自动跳转
    return false;
}