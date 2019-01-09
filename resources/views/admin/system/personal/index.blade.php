@extends('admin.layout.main')

@section('content')
<div class="container container3">
    <form action="{{ route('PersonalUpdate', ['id' => $personal->id]) }}" method="post" name="personal_form">

        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div style="margin-left: 20%;">
            <div class="form-group">
                <label class="form-label">姓名：</label>
                <input class="form-control" type="text" name="name" value="{{ $personal->name }}" />
            </div>
            <div class="form-group">
                <label class="form-label">手机号：</label>
                <input class="form-control" type="text" name="mobile" value="{{ $personal->mobile }}" readonly="readonly" />　<span class="form-title red show">(*禁止修改)</span>
            </div>
            <div class="form-group">
                <label class="form-label">密码：</label>
                <input class="form-control" type="password" name="password" />
            </div>
            <div class="form-group">
                <label class="form-label">邮箱：</label>
                <input class="form-control" type="text" name="email" value="{{ $personal->email }}" />
            </div>
            <div class="form-group">
                <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="personal_edit(); return false;">修改</button>
                <button type="reset" class="btn-groups btn-reset" onclick="manager_reset();">重置</button>
            </div>
        </div>

    </form>
</div>

<script type="text/javascript">
    // 开户行添加验证
    function personal_edit() {

        // 验证姓名
        if (document.personal_form.name.value==""){
            layer.msg('请填写姓名！');
            document.personal_form.name.focus();
            return false;
        }
        var name = /^[\u4e00-\u9fa5]{2,6}$/;
        if (!name.test(document.personal_form.name.value)){
            layer.msg('姓名格式不正确，请重新填写！');
            document.personal_form.name.focus();
            return false;
        }

        // 手机号码
        if (document.personal_form.mobile.value=="") {
            layer.msg('请填写联系电话！');
            document.personal_form.mobile.focus();
            return false;
        }
        var mob = /^1[3,4,5,6,7,8]\d{9}$/;
        if (!mob.test(document.personal_form.mobile.value)) {
            layer.msg('联系电话格式不正确，请重新填写！');
            document.personal_form.mobile.focus();
            return false;
        }

        // 邮箱
        if (document.personal_form.email.value=="") {
            layer.msg('请填写邮箱！');
            document.personal_form.email.focus();
            return false;
        }
        var email = /^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i;
        if (!email.test(document.personal_form.email.value)) {
            layer.msg('邮箱格式不正确，请重新填写！');
            document.personal_form.email.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('personal_form');
        ajax("{{ route('PersonalUpdate', ['id' => $personal->id]) }}", fd);

    }
</script>

@endsection
