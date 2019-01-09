@extends('admin.layout.main')

@section('content')
<div class="container marginTop">

    <form action="{{ route('BankStore') }}" method="post" name="bank_form">

        @csrf

        <!--表单-->
        <div class="form-group">
            <br><br><br>
            <label class="form-title">开户行名称：</label>
            <input class="form-control form-control20" type="text" name="name" />
        </div>


        <div class="form-group form-submit">
            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="bank_check(); return false;">添加</button>

            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('BankIndex') }}';">放弃</button>
        </div>
    </form>

</div>


<script type="text/javascript">
    // 开户行添加验证
    function bank_check() {

        // 开户行名称
        if (document.bank_form.name.value=="") {
            layer.msg('请输入开户行名称！');
            document.bank_form.name.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('bank_form');
        ajax("{{ route('BankStore') }}", fd, "{{ route('BankIndex') }}");

    }
</script>


@endsection
