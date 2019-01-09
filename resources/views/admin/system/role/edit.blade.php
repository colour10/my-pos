@extends('admin.layout.main')

@section('content')
<div class="container marginTop">

    <!-- form start -->
    <form role="form" action="{{ route('RoleUpdate', ['id' => $role->id]) }}" method="POST" name="role_form">

        @csrf
        {{ method_field('PUT') }}

        <div class="form-group">
            <label class="form-title">角色名称：</label>
            <input class="form-control" type="text" name="name" value="{{ $role->name }}" />
        </div>

        <div class="form-group">
            <label class="form-title">角色描述：</label>
            <input class="form-control" type="text" name="description" value="{{ $role->description }}" />
        </div>

        <div class="form-group form-submit">

            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="role_edit(); return false;">提交</button>

            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('RoleIndex') }}';">放弃</button>

        </div>

    </form>

</div>

<script type="text/javascript">
    // 角色添加验证
    function role_edit() {

        // 验证名称
        if (document.role_form.name.value==""){
            layer.msg('请填写角色名称！');
            document.role_form.name.focus();
            return false;
        }

        // 描述
        if (document.role_form.description.value=="") {
            layer.msg('请填写角色描述！');
            document.role_form.description.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('role_form');
        ajax("{{ route('RoleUpdate', ['id' => $role->id]) }}", fd, "{{ route('RoleIndex') }}");

    }
</script>

@endsection

