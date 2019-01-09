@extends('admin.layout.main')

@section('content')
<div class="container marginTop">

    <form action="{{ route('Accountstore') }}" method="post" name="account_form">

        @csrf

        <!--表单-->
        <div class="form-group">
            <br><br><br>
            <label class="form-title">账户类型名称：</label>
            <input class="form-control form-control20" type="text" name="name" />
        </div>

        <div class="form-group form-submit">
            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="account_check(); return false;">添加</button>

            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('AccountIndex') }}';">放弃</button>
        </div>
    </form>

</div>

<script type="text/javascript">
    // 代付通道校验
    function account_check() {

        // 代付通道名称
        if (document.account_form.name.value=="") {
            layer.msg('请输入账户类型名称！');
            document.account_form.name.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('account_form');
        ajax("{{ route('Accountstore') }}", fd, "{{ route('AccountIndex') }}");

    }
</script>

@endsection
