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


    @if ($keyword && $agents)
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

                @foreach ($agents as $agent)
                <tr class="tr_agent" id="agent{{ $agent->id }}">
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
                        <input type="text" name="amount" id="amout{{ $account->id }}" class="white" />
                    </td>
                    <td>
                        <input type="text" name="description" id="desc{{ $account->id }}" />
                    </td>
                    <td class="text-center">
                        <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                        <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="transactor_check(); return false;">经办</button>
                    </td>
                </tr>
                @endforeach

            </table>
        </form>

    </div>
    <!-- 列表End -->
    @elseif ($keyword && !$agents)
    <p class="text-left text-indent2">该商户不存在!</p>
    @else
    <p class="text-left text-indent2 hidden">请输入待查询的合伙人手机号码</p>
    @endif

</div>
<style>
    .layui-layer-page .layui-layer-content {
        padding:20px;
    }
</style>
<script type="text/javascript">

    // 如果存在多条记录，只能选择一个进入
    $(function() {
        var infos = '';
        var agents = '{!! $agents !!}';
        var keyword = '{{ $keyword }}';
        var agents_obj = JSON.parse(agents);
        var tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
        console.log(agents_obj);
        if (agents_obj.length > 1 && keyword) {
            // 循环账户信息
            for (var i=0;i<agents_obj.length;i++) {
                infos += '<a style="cursor: pointer;" onclick="choose('+agents_obj[i].id+');return false;">ID：'+agents_obj[i].sid+tab+'姓名：'+agents_obj[i].name+tab+'手机号：'+agents_obj[i].mobile+'</a><br><br>';
            }
            //信息框
            //自定页
            layer.open({
                type: 1,
                title: '请选择待操作的合伙人账户',
                skin: 'layui-layer-demo', //样式类名
                closeBtn: 0, //不显示关闭按钮
                anim: 2,
                shadeClose: false, //开启遮罩关闭
                content: infos,
            });
        }
    });

    // 隐藏其他合伙人
    function choose(agent_id) {
        $('.tr_agent').each(function() {
            $(this).hide();
            if ($(this).attr('id') == 'agent'+agent_id) {
                $(this).show();
            }
            // 再把所有的hidden元素彻底删除
            if ($(this).is(':hidden')) {
                $(this).remove();
            }
        });
        layer.closeAll();
    }

    // 调账经办添加逻辑
    function transactor_check() {
        // 资金账户
        var account_type = $('input:radio[name="account_type"]:checked').val();
        if(account_type==null){
            layer.msg('请选择账户类型！');
            return false;
        }
        // 调账类型
        var type = $('input:radio[name="type"]:checked').val();
        if(type==null){
            layer.msg('请选择调账类型！');
            return false;
        }
        // 验证调账金额
        if (document.transactor_form.amount.value == '') {
            layer.msg('调账金额不能为空');
            document.transactor_form.amount.focus();
            return false;
        }
        if (!isNumber(parseInt(document.transactor_form.amount.value))) {
            layer.msg('调账金额必须是数字！');
            document.transactor_form.amount.focus();
            return false;
        }
        if (document.transactor_form.amount.value <= 0) {
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
