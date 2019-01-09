@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table">

    @include('admin.advance.search')

    <!-- 列表Start -->
    <div class="table-main">
        <table class="table table-hover">
            <tr class="th">
                <th>通道ID</th>
                <th>通道名称</th>
                <th>成本费率</th>
                <th>单笔最高限额</th>
                <th>单笔结算费用</th>
                <th>通道状态</th>
                <th>录入时间</th>
                <th>更新时间</th>
                <th>处理</th>
            </tr>

            @foreach ($methods as $method)
            <tr>
                <td>{{ $method->id }}</td>
                <td>{{ $method->name }}</td>
                <td>{{ $method->cost_rate }}</td>
                <td>{{ $method->max }}</td>
                <td>{{ $method->per_charge }}</td>
                <th>
                    @if ($method->status == 1)
                        开启
                    @else
                        禁用
                    @endif
                </th>
                <td>{{ $method->created_at }}</td>
                <td>{{ $method->updated_at }}</td>
                <td>
                    <a href="{{ route('AdvanceMethodedit', ['id' => $method->id]) }}" class="btn btn-small btn-edit">修改</a> 
                    <!-- <a class="btn btn-small btn-delete" onclick="method_delete({{ $method->id }});return false;">删除</a>  -->
                </td>
            </tr>
            @endforeach

        </table>
    </div>
    <!-- 列表End -->

    @if (isset($methods))
    <!-- 分页Start -->
    <div class="paging">

        {!! $methods->appends(['name'=>$name])->render() !!}

    </div>
    <!-- 分页End -->
    @endif

</div>

<script type="text/javascript">
    // 通道删除
    function method_delete(id) {
        // 询问框
        layer.confirm('确认要删除吗，操作不可逆，请三思而后行', {
            btn: ['是','否'] //按钮
        }, function() {
            // 审核通过 ajax提交
            $.post("/admin/advance/method/" + id, {
                "_token": "{{ csrf_token() }}",
                "_method": "delete",
            }, function(data) {
                if (data.code == 0) {
                    layer.msg(data.msg, { icon: 1});
                    setTimeout("document.location.reload()", 1000);//1000毫秒后跳转
                }
            });
        }, function() {
            // 点击否，无需进行任何操作
        });
    }

</script>

@endsection
