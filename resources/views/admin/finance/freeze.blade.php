@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table t0">

    <!-- 内容检索 Start -->
    <div class="container-fluid">
        <div class="pull-left" style="width:60%;">
            <form action="{{ route('FinanceFreeze') }}" method="get" id="form-search">
                <div class="select-main">
                    <div class="form-select">
                        <label class="form-select-title">手机号码：</label>
                        <input type="text" class="form-control" placeholder="请输入合伙人手机号码" name="mobile">
                    </div>

                    <div class="form-select"> 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-large btn-update">提交</button>
                    </div>

                    <div class="form-select"> 
                        &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                    </div>

                    <div class="form-select"> 
                        <label class="form-select-title" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：如果需要取消冻结，针对特定合伙人，将冻结金额设置为0即可。</label>
                    </div>

                </div>
            </form>
        
        </div>

    </div>
    <!-- 内容检索 End -->


    @if ($mobile && $agent)
    <!-- 列表Start -->
    <div class="table-main">

        <form action="{{ route('FinanceFreezestore') }}" method="post" name="freeze_form" id="freeze_form">

            @csrf

            <table class="table table-hover">

                <tr class="th">
                    <th>合伙人ID</th>
                    <th>合伙人姓名</th>
                    <th>手机号码</th>
                    <th>账户类型</th>
                    <th>账户余额</th>
                    <th>冻结金额</th>
                    <th>冻结原因</th>
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
                            {{ $agent->agentaccount->sum_money }}   <br>
                            冻结资金 {{ $agent->agentaccount->frozen_money }} 元 <br>
                            可用余额 {{ $agent->agentaccount->available_money }} 元
                        @else
                            0   <br>
                            冻结资金 0 元 <br>
                            可用余额 0 元
                        @endif
                    </td>
                    <td>
                        <input type="text" name="amount" id="" class="white" />
                    </td>
                    <td>
                        <input type="text" name="description" id="" />
                    </td>
                    <td class="text-center">
                        <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                        <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="freeze_check(); return false;">确认</button>
                    </td>
                </tr>

            </table>
        </form>

    </div>
    <!-- 列表End -->
    @elseif ($mobile && !$agent)
    <p class="text-left text-indent2">该商户不存在!</p>
    @else
    <p class="text-left text-indent2 hidden">请输入待查询的合伙人手机号码</p>
    @endif

</div>

<script type="text/javascript">
    // 冻结经办添加逻辑
    function freeze_check() {

        // 验证冻结金额
        if (document.freeze_form.amount.value==""){
            layer.msg('请填写冻结金额！');
            document.freeze_form.amount.focus();
            return false;
        }
        if (!isNumber(parseInt(document.freeze_form.amount.value))) {
            layer.msg('冻结金额必须是数字！');
            document.freeze_form.amount.focus();
            return false;
        }
        if (parseInt(document.freeze_form.amount.value) < 0) {
            layer.msg('冻结金额必须大于等于0！');
            document.freeze_form.amount.focus();
            return false;
        }

        // 冻结原因
        if (document.freeze_form.description.value=="") {
            layer.msg('请填写冻结原因！');
            document.freeze_form.description.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('freeze_form');

        // 取消冻结
        if (document.freeze_form.amount.value == 0) {
            //询问框
            layer.confirm('您真的要取消冻结吗？', {
                btn: ['确认冻结','取消冻结'] //按钮
            }, function() {
                // 删除冻结
                ajax("{{ route('FinanceFreezestore') }}", fd);
            }, function() {
                // 不进行任何操作
            });
        } else {
            // 添加冻结记录
            ajax("{{ route('FinanceFreezestore') }}", fd);
        }

    }
</script>

@endsection
