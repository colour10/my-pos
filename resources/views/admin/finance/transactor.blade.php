<?php
    $url = \Request::getRequestUri();
?>

@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table t0">

    <!-- 内容检索 Start -->
    <div class="container-fluid">
        <div class="pull-left" style="width:90%;">
            <form action="{{ route('FinanceTransactor') }}" method="get" id="form-search">
                <div class="select-main">
                    <div class="form-select">
                        <label class="form-select-title">姓名/手机号码：</label>
                        <input type="text" class="form-control" placeholder="请输入合伙人姓名/手机号码" name="keyword">
                    </div>

                    <div class="form-select"> 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-large btn-update">提交</button>
                    </div>

                    <div class="form-select"> 
                        &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                    </div>

                    <div class="form-select p22">
                        <p>如需同时对多个商户进行经办，可点击此处<span><a href="{{ route('FinanceTransactors') }}">"批量经办"</a></span>进行操作。</p>
                    </div>

                </div>
            </form>
        
        </div>

    </div>
    <!-- 内容检索 End -->


    @if ($keyword && $agent)
    <!-- 列表Start -->
    <div class="table-main">

        <form action="{{ route('FinanceTransactorstore') }}" method="post" name="transactor_form" id="transactor_form">

            @csrf

            <table class="table table-hover">

                <tr class="th">
                    <th>合伙人ID</th>
                    <th>合伙人姓名</th>
                    <th>手机号码</th>
                    <th>账户类型</th>
                    <th>账户余额</th>
                    <th>调账类型</th>
                    <th>调账金额</th>
                    <th>调账原因</th>
                    <th>操作</th>
                </tr>


                <tr>
                    <td>{{ $agent->sid }}</td>
                    <td>{{ $agent->name }}</td>
                    <td>{{ $agent->mobile }}</td>
                    <td id="account_type">
                        @foreach ($accounts as $account)
                            <label for="a{{ $account->id }}"><input type="radio" name="account_type" id="a{{ $account->id }}" value="{{ $account->id }}"> {{ $account->name }} </label> <br>
                        @endforeach
                    </td>
                    <td>
                        @if ($agent->agentaccount)
                            {{ $agent->agentaccount->sum_money }} 元   <br>
                            冻结资金 {{ $agent->agentaccount->frozen_money }} 元 <br>
                            可用余额 {{ $agent->agentaccount->available_money }} 元 <br>
                            提 现 中 {{ $agent->agentaccount->cash_money }} 元
                        @else
                            0 元   <br>
                            冻结资金 0 元 <br>
                            可用余额 0 元 <br>
                            提 现 中 0 元
                        @endif
                    </td>
                    <td>
                        <label for="t1"><input type="radio" name="type" value="1" id="t1" checked="checked"> 调入</label><br>
                        <label for="t2"><input type="radio" name="type" value="2" id="t2"> 调出</label>
                    </td>
                    <td>
                        <input type="text" name="amount" id="" class="white" />
                    </td>
                    <td>
                        <input type="text" name="description" id="" />
                    </td>
                    <td class="text-center">
                        <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                        <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="transactor_check(); return false;">经办</button>
                    </td>
                </tr>


            </table>
        </form>

    </div>
    <!-- 列表End -->
    @elseif ($keyword && !$agent)
    <p class="text-left text-indent2">该商户不存在!</p>
    @else
    <p class="text-left text-indent2 hidden">请输入待查询的合伙人手机号码</p>
    @endif

</div>

<script type="text/javascript">
    // 调账经办添加逻辑
    function transactor_check() {
        // 验证调账金额
        if (document.transactor_form.amount.value==""){
            layer.msg('请填写调账金额！');
            document.transactor_form.amount.focus();
            return false;
        }
        if (!isNumber(parseInt(document.transactor_form.amount.value))) {
            layer.msg('调账金额必须是数字！');
            document.transactor_form.amount.focus();
            return false;
        }
        if (parseInt(document.transactor_form.amount.value) <= 0) {
            layer.msg('调账金额必须大于0！');
            document.transactor_form.amount.focus();
            return false;
        }

        // 调账原因
        if (document.transactor_form.description.value=="") {
            layer.msg('请填写调账原因！');
            document.transactor_form.description.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('transactor_form');
        ajax("{{ route('FinanceTransactorstore') }}", fd, "{{ $url }}");

    }
</script>

@endsection
