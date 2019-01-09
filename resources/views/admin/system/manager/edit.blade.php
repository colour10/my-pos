@extends('admin.layout.main')

@section('content')
<div class="container container3">
    <form action="{{ route('ManagerUpdate', ['id' => $manager->id]) }}" method="post" name="manager_form">

        @csrf
        {{ method_field('PUT') }}

        <div style="margin-left: 20%;">
            <div class="form-group">
                <label class="form-label">姓名：</label>
                <input class="form-control" type="text" name="name" value="{{ $manager->name }}" />
            </div>
            <div class="form-group">
                <label class="form-label">手机号：</label>
                <input class="form-control" type="text" name="mobile" value="{{ $manager->mobile }}" />
            </div>
            <div class="form-group">
                <label class="form-label">密码：</label>
                <input class="form-control" type="password" name="password" />
            </div>
            <div class="form-group">
                <label class="form-label">邮箱：</label>
                <input class="form-control" type="text" name="email" value="{{ $manager->email }}" />
            </div>
            <div class="form-group">
                <label class="form-label">状态</label>
                <select class="form-control" name="status">
                    <option value="1"@if ($manager->status == 1) selected="selected"@endif>开启</option>
                    <option value="0"@if ($manager->status == 0) selected="selected"@endif>停用</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="manager_edit(); return false;">修改</button>
                <button type="reset" class="btn-groups btn-reset" onclick="window.location.href='{{ route('ManagerIndex') }}';">放弃</button>
            </div>
        </div>

    </form>
</div>

<script type="text/javascript">
    // 管理员逻辑验证
    function manager_edit() {

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
            layer.msg('请填写联系电话！');
            document.manager_form.mobile.focus();
            return false;
        }
        var mob = /^1[3,4,5,6,7,8]\d{9}$/;
        if (!mob.test(document.manager_form.mobile.value)) {
            layer.msg('联系电话格式不正确，请重新填写！');
            document.manager_form.mobile.focus();
            return false;
        }

        // 邮箱
        if (document.manager_form.email.value=="") {
            layer.msg('请填写邮箱！');
            document.manager_form.email.focus();
            return false;
        }
        var email = /^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i;
        if (!email.test(document.manager_form.email.value)) {
            layer.msg('邮箱格式不正确，请重新填写！');
            document.manager_form.email.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('manager_form');
        ajax("{{ route('ManagerUpdate', ['id' => $manager->id]) }}", fd, "{{ route('ManagerIndex') }}");

    }
</script>

@endsection
