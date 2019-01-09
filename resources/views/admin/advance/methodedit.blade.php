@extends('admin.layout.main')

@section('content')
<div class="container marginTop marginTop4">

    <form action="{{ route('AdvanceMethodupdate', ['id' => $model->id]) }}" method="post" name="method_form">

        @csrf
        {{ method_field('PUT') }}

        <!--表单-->
        <div class="form-group">
            <br><br><br>
            <label class="form-title">通道名称：</label>
            <input class="form-control form-control20 form-control-w20" type="text" name="name" value="{{ $model->name }}" />
        </div>

        <div class="form-group">
            <label class="form-title">支付通道账户号：</label>
            <input class="form-control form-control20 form-control-w20" type="text" name="acctno" value="{{ $model->acctno }}" /> 
            <span></span>
        </div>

        <div class="form-group">
            <label class="form-title">支付通道登录用户名：</label>
            <input class="form-control form-control20 form-control-w20" type="text" name="username" value="{{ $model->username }}" /> <span></span>
        </div>

        <div class="form-group">
            <label class="form-title">支付通道登录密码：</label>
            <input class="form-control form-control20 form-control-w20" type="text" name="password" value="{{ $model->password }}" /> <span></span>
        </div>

        <div class="form-group">
            <label class="form-title">支付通道商户代码：</label>
            <input class="form-control form-control20 form-control-w20" type="text" name="merchant_id" value="{{ $model->merchant_id }}" /> <span></span>
        </div>

        <div class="form-group">
            <label class="form-title">业务类型：</label>
            <input class="form-control form-control20" type="text" name="business_code" value="{{ $model->business_code }}" /> <span></span>
        </div>

        <div class="form-group">
            <label class="form-title">成本费率：</label>
            <input class="form-control form-control20" type="text" name="cost_rate" value="{{ $model->cost_rate }}" /> <span>%</span>
        </div>

        <div class="form-group">
            <label class="form-title">单笔最高限额：</label>
            <input class="form-control form-control20" type="text" name="max" value="{{ $model->max }}" /> <span>元</span>
        </div>

        <div class="form-group">
            <label class="form-title">单笔结算费用：</label>
            <input class="form-control form-control20" type="text" name="per_charge" value="{{ $model->per_charge }}" /> <span>元/笔</span>
        </div>

        <div class="form-group cursor">
            <label class="form-title">是否开启：</label>
            <input type="radio" name="status" id="inlineRadio1" value="1"@if ($model->status == '1') checked="checked"@endif>
            <label for="inlineRadio1"> 开启 </label>
            <input type="radio" name="status" id="inlineRadio2" value="0" @if ($model->status == '0') checked="checked"@endif>
            <label for="inlineRadio2"> 禁用 </label>
        </div>

        <div class="form-group form-submit">
            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="method_check(); return false;">修改</button>

            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('AdvanceMethod') }}';">放弃</button>
        </div>
    </form>

</div>

<script type="text/javascript">
    // 结算通道添加验证
    function method_check() {

        // 代付通道名称
        if (document.method_form.name.value=="") {
            layer.msg('请输入代付通道名称！');
            document.method_form.name.focus();
            return false;
        }

        // 账户号
        if (document.method_form.acctno.value=="") {
            layer.msg('请输入账户号！');
            document.method_form.acctno.focus();
            return false;
        }

        var isnum = /^[\+\-]?\d*?\.?\d*?$/;

        // 成本费率
        if (document.method_form.cost_rate.value=="") {
            layer.msg('请输入成本费率！');
            document.method_form.cost_rate.focus();
            return false;
        }

        if (!isnum.test(document.method_form.cost_rate.value)) {
            layer.msg('成本费率必须为数字！');
            document.method_form.cost_rate.focus();
            return false;
        }

        // 单笔最高限额
        if (document.method_form.max.value=="") {
            layer.msg('请输入单笔最高限额！');
            document.method_form.max.focus();
            return false;
        }

        if (!isnum.test(document.method_form.max.value)) {
            layer.msg('单笔最高限额必须为数字！');
            document.method_form.max.focus();
            return false;
        }

        // 单笔结算费用
        if (document.method_form.per_charge.value=="") {
            layer.msg('请输入单笔结算费用！');
            document.method_form.per_charge.focus();
            return false;
        }

        if (!isnum.test(document.method_form.per_charge.value)) {
            layer.msg('单笔结算费用必须为数字！');
            document.method_form.per_charge.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('method_form');
        ajax("{{ route('AdvanceMethodupdate', ['id' => $model->id]) }}", fd, "{{ route('AdvanceMethod') }}");

    }
</script>

@endsection
