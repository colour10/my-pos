/*
 Exemples :
 <a href="posts/2" data-method="delete" data-token="{{csrf_token()}}">
 - Or, request confirmation in the process -
 <a href="posts/2" data-method="delete" data-token="{{csrf_token()}}" data-confirm="Are you sure?">
 */


(function() {

    var laravel = {
        initialize: function() {
            this.methodLinks = $('a[data-method]');
            this.token = $('a[data-token]');
            this.registerEvents();
        },

        registerEvents: function() {
            this.methodLinks.on('click', this.handleMethod);
        },

        handleMethod: function(e) {
            var link = $(this);
            var httpMethod = link.data('method').toUpperCase();
            var form;

            // If the data-method attribute is not PUT or DELETE,
            // then we don't know what to do. Just ignore.
            if ($.inArray(httpMethod, ['PUT', 'DELETE']) === -1) {
                return;
            }

            // Allow user to optionally provide data-confirm="Are you sure?"
            if (link.data('confirm')) {
                if (!laravel.verifyConfirm(link)) {
                    return false;
                }
            }

            form = laravel.createForm(link);
            form.submit();

            // e.preventDefault();
            return false;

        },

        verifyConfirm: function(link) {
            return confirm(link.data('confirm'));
        },

        createForm: function(link) {
            var form =
                $('<form>', {
                    'method': 'POST',
                    'action': link.attr('href')
                });

            var token =
                $('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': link.data('token')
                });

            var hiddenInput =
                $('<input>', {
                    'name': '_method',
                    'type': 'hidden',
                    'value': link.data('method')
                });

            return form.append(token, hiddenInput)
                .appendTo('body');
        }
    };

    laravel.initialize();

})();


// 表单重置
function manager_reset() {
    document.manager_form.reset();
}

// 判断是否为数字
function isNumber(obj) {
    return obj === +obj
}

// 获得form表单的formdata值
// formname：form的name值
function getFormData(formname) {
    var form = document.getElementsByName(formname)[0];
    return new FormData(form);
}

// 封装的ajax
// 几乎每个页面都要用，所以进行了封装
// url：数据接收的地址
// fd：当前表单的FormData对象
// successUrl：页面执行成功后跳转的url地址，如果不填写则默认是当前页面的地址
// second：设置几秒后页面跳转，默认是3秒
function ajax(url, fd, successUrl = window.location.href, second = 3) {
    // 处理逻辑
    $.ajax({
        type: 'post',
        url: url,
        data: fd,
        dataType: 'json',
        timeout: 99999,
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
            // 打印数据
            console.log(data);
            // 逻辑
            if (data.code == 0) {
                layer.msg(data.msg, { icon: 1 });
                // 3000毫秒后跳转
                setTimeout("window.location.href = '" + successUrl + "'", second * 3000);
            } else {
                layer.msg(data.msg, { icon: 2 });
                // 3000毫秒后跳转
                // setTimeout("document.location.reload()", second * 1000);
            }
        },
        error: function(data) {
            // 打印数据
            console.log(data);
            // 逻辑
            if (data.status == 422) {
                var jsonObj = JSON.parse(data.responseText);
                var errors = jsonObj.errors;
                for (var item in errors) {
                    for (var i = 0, len = errors[item].length; i < len; i++) {
                        layer.msg(errors[item][i], { icon: 2 });
                        return;
                    }
                }
            } else {
                layer.msg('服务器连接失败', { icon: 2 });
                return;
            }
        },
    });
}



// 封装的ajax-default方法
// 几乎每个页面都要用，所以进行了封装
// type: 数据请求的方式(使用post)
// url：数据接收的地址
// fd：当前表单的FormData对象
// successUrl：页面执行成功后跳转的url地址，如果不填写则默认是当前页面的地址
// second：设置几秒后页面跳转，默认是3秒
function defaultajax(url, fd, successUrl = window.location.href, second = 3) {
    // 处理逻辑
    $.ajax({
        type: 'post',
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
            // 打印返回值
            console.log(data);
            // 判断逻辑
            if (data.code == '0') {
                layer.msg(data.msg, { icon: 1 });
                // 3000毫秒后跳转
                setTimeout("window.location.href = '" + successUrl + "'", second * 3000);
            } else {
                // 返回错误
                layer.msg(data.msg, { icon: 2 });
                // 3000毫秒后跳转
                // setTimeout("document.location.reload()", second*1000);
            }
        },
        error: function(data) {
            // 打印返回值
            console.log(data);
            // 判断逻辑
            if (data.status == 422) {
                var jsonObj = JSON.parse(data.responseText);
                var errors = jsonObj.errors;
                for (var item in errors) {
                    for (var i = 0, len = errors[item].length; i < len; i++) {
                        layer.msg(errors[item][i], { icon: 2 });
                        return;
                    }
                }
            } else {
                // 提示
                layer.msg('程序内部错误，请联系管理员！', { icon: 2 });
                return;
            }
        },
    });
    // 返回假，禁止自动跳转
    return false;
}