@extends('admin.layout.main')

@section('content')
<div class="container marginTop">

    <!-- form start -->
    <form role="form" action="{{ route('PermissionStore') }}" method="POST" name="permission_form">

        @csrf

        <div class="form-group">
            <label class="form-title">权限名称：</label>
            <input class="form-control" type="text" name="name" />
        </div>

        <div class="form-group">
            <label class="form-title">所属权限：</label>
            <select name="pid" class="form-control">
                <option value="0">=设为顶级权限=</option>
                @foreach ($parent_permissions as $permission)
                <option value="{{ $permission->id }}">{{ $permission->description }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-title">权限描述：</label>
            <input class="form-control" type="text" name="description" />
        </div>

        <div class="form-group form-submit">
            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="permission_check(); return false;">提交</button>
            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('PermissionIndex') }}';">放弃</button>
        </div>

    </form>

</div>

<script type="text/javascript">
    // 权限录入
    function permission_check() {
        // 权限名称
        if (document.permission_form.name.value=="") {
            layer.msg('请输入权限名称！');
            document.permission_form.name.focus();
            return false;
        }
        // 权限描述
        if (document.permission_form.description.value=="") {
            layer.msg('请输入权限描述！');
            document.permission_form.description.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('permission_form');
        ajax("{{ route('PermissionStore') }}", fd, "{{ route('PermissionIndex') }}");

    }
</script>

@endsection

