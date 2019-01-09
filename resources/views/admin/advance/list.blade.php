@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table">

    <!-- 内容检索 Start -->
    <div class="container-fluid">
        <div class="pull-left" style="width:84%;">
            <form action="{{ route('AdvanceSearch') }}" method="get" id="advance_form">

                <div class="select-main">

                    <div class="form-select">
                        <label class="form-select-title" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;姓名：</label>
                        <input type="text" class="form-control" placeholder="请输入姓名"  name="name">
                    </div>

                    <div class="form-select">
                        <label class="form-select-title" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;手机号：</label>
                        <input type="text" class="form-control" placeholder="请输入手机号"  name="mobile">
                    </div>

                    <div class="form-select">
                        <label class="form-select-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;合伙人ID：</label>
                        <input type="text" class="form-control" placeholder="请输入合伙人ID" name="sid">
                    </div>

                    <br><br>

                    <div class="form-select">
                        <br>
                        <label class="form-select-title" >查询时间：</label>
                        <input class="form-control" type="text" name="start_time" id="test1" />
                        <div class="layui-form-mid">-</div>
                        <input class="form-control" type="text" name="end_time" id="test2" />
                    </div>

                    <div class="form-select">
                        <br>
                        <label class="form-select-title"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;状态：</label>
                        <select class="form-control" name="status">
                            <option value=""@if ($request->get('status') === NULL) selected="selected"@endif>全部</option>
                            <option value="0"@if ($request->get('status') == '0') selected="selected"@endif>结算中</option>
                            <option value="1"@if ($request->get('status') == '1') selected="selected"@endif>结算成功</option>
                            <option value="2"@if ($request->get('status') == '2') selected="selected"@endif>结算失败</option>
                        </select>
                    </div>

                    <div class="form-select">
                        <br>
                        <label class="form-select-title"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;结算方式：</label>
                        <select class="form-control" name="method_id">
                            @foreach ($methods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-select">
                        <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="submit" class="btn btn-large btn-update">提交</button>
                    </div>
                    <div class="form-select">
                        <br>
                        &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                    </div>

                </div>

            </form>
        
        </div>

        <div class="pull-right text-right" style="width:16%">
            <a href="/admin/advance/export?<?php echo $_SERVER['QUERY_STRING']; ?>" class="btn btn-large btn-add marginTop">导出excel</a>
        </div>

    </div>
    <!-- 内容检索 End -->


    <br><br>

    <!-- 列表Start -->
    <div class="table-main">
        <table class="table table-hover">
            <tr class="th">
                <th>结算订单号</th>
                <th>合伙人ID</th>
                <th>合伙人姓名</th>
                <th>手机号</th>
                <th>结算金额</th>
                <th>手续费</th>
                <th>到账金额</th>
                <th>结算通道</th>
                <th>结算时间</th>
                <th>结算状态</th>
                <th>结算银行</th>
                <th>结算卡号</th>
                <th>卡类型</th>
                <th>处理</th>
            </tr>

            @foreach ($lists as $list)
            <tr>
                <td>{{ $list->cash_id }}</td>
                <td>{{ $list->sid }}</td>
                <td>{{ $list->agentName }}</td>
                <td>{{ $list->mobile }}</td>
                <td>{{ $list->sum }}</td>
                <td>{{ $list->charge }}</td>
                <td>{{ $list->account }}</td>
                <td>{{ $list->methodName }}</td>
                <td>{{ $list->updated_at }}</td>
                <td>{{ $list->statusName }}</td>
                <td>{{ $list->bankName }}</td>
                <td>{{ $list->cardNumber }}</td>
                <td>{{ $list->cardType }}</td>
                <td>
                    <a class="btn btn-large btn-check" onclick="cash_show({{ json_encode($list) }});return false;">详情</a>
                </td>
            </tr>
            @endforeach

        </table>
    </div>
    <!-- 列表End -->

    @if (isset($lists))
    <!-- 分页Start -->
    <div class="paging">

        @if ($controller_action['action'] == 'search')
            {!! $lists->appends($request->all())->render() !!}　<p class="pull-right" style="padding:6px 12px;">共有{{ $lists->total() }}条记录　当前总结算金额：{{ $sum }} 元</p>
        @else
            {!! $lists->links() !!}　<p class="pull-right" style="padding:6px 12px;">共有{{ $lists->total() }}条记录　当前总结算金额：{{ $sum }} 元</p>
        @endif

    </div>
    <!-- 分页End -->
    @endif

</div>

<script type="text/javascript">

    //详情弹出页面层
    function cash_show(list) {
        if (list.err_msg == null) {
            list.err_msg = '暂无';
        }
        layer.open({
            type: 1,
            title: '转账详情',
            area: ['600px', '360px'],
            shadeClose: true, //点击遮罩关闭
            content: '\<\div style="padding:20px;">\<p\>合伙人ID：'+list.sid+'\<\/p\>\<p\>结算订单号：'+list.cash_id+'\<\/p\>\<p\>合伙人姓名：'+list.agentName+'\<\/p\>\<p\>结算金额：'+list.sum+'\<\/p\>\<p\>手续费：'+list.charge+'\<\/p\>\<p\>到账金额：'+list.account+'\<\/p\>\<p\>结算时间：'+list.updated_at+'\<\/p\>\<p\>结算状态：'+list.statusName+'\<\/p\>\<p\>失败原因：'+list.err_msg+'\<\/p\>\<p\>结算银行：'+list.bankName+'\<\/p\>\<p\>结算卡号：'+list.cardNumber+'\<\/p\>\<p\>卡类型：'+list.cardType+'\<\/p\>\<\/div>'
        });
    }

</script>

@endsection
