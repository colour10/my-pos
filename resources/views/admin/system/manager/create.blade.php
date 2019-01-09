@extends('admin.layout.main')

@section('content')
<div class="container container3">
    <form action="{{ route('ManagerStore') }}" method="post" name="manager_form">

        @csrf

        <div style="margin-left: 20%;">
            <div class="form-group">
                <label class="form-label">姓名：</label>
                <input class="form-control" type="text" name="name" />
            </div>
            <div class="form-group">
                <label class="form-label">手机号：</label>
                <input class="form-control" type="text" name="mobile" />
            </div>
            <div class="form-group">
                <label class="form-label">密码：</label>
                <input class="form-control" type="password" name="password" />
            </div>
            <div class="form-group">
                <label class="form-label">邮箱：</label>
                <input class="form-control" type="text" name="email" />
            </div>
            <div class="form-group">
                <label class="form-label">状态</label>
                <select class="form-control" name="status">
                    <option value="1">开启</option>
                    <option value="0">停用</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="manager_create(); return false;">立即提交</button>
                <button type="reset" class="btn-groups btn-reset" onclick="window.location.href='{{ route('ManagerIndex') }}';">放弃</button>
            </div>
        </div>

    </form>
</div>

<script type="text/javascript">
    // 管理员添加验证
    function manager_create() {

        // 验证姓名
        if (document.manager_form.name.value==""){
            layer.msg('请填写姓名！');
            document.manager_form.name.focus();
            return false;
        }
        var name = /^[\u4e00-\u9fa5]{2,6}$/;
        if (!name.test(document.manager_form.name.value)){
            layer.msg('姓名格式不正确，请重新填写！');
            document.manager_form.name.focus();
            return false;
        }

        // 手机号码
        if (document.manager_form.mobile.value=="") {
            layer.msg('请填写手机号！');
            document.manager_form.mobile.focus();
            return false;
        }
        var mob = /^1[3,4,5,6,7,8]\d{9}$/;
        if (!mob.test(document.manager_form.mobile.value)){
            layer.msg('手机号格式不正确，请重新填写！');
            document.manager_form.mobile.focus();
            return false;
        }
        
        // 登录密码
        if (document.manager_form.password.value=="") {
            layer.msg('请填写密码！');
            document.manager_form.password.focus();
            return false;
        }

        // 邮箱
        var ifemail = /^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i;
        if (!ifemail.test(document.manager_form.email.value)){
            layer.msg('邮箱格式不正确，请重新填写！');
            document.manager_form.email.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('manager_form');
        ajax("{{ route('ManagerStore') }}", fd, "{{ route('ManagerIndex') }}");

    }
</script>

@endsection

