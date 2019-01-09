@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table">

    @include('admin.system.bank.search')

    <!-- 列表Start -->
    <div class="table-main">
        <table class="table table-hover">
            <tr class="th">
                <th>开户行ID</th>
                <th>开户行名称</th>
                <th>录入时间</th>
                <th>更新时间</th>
                <th>处理</th>
            </tr>

            @if ($banks->count())
            @foreach ($banks as $bank)
            <tr>
                <td>{{ $bank->id }}</td>
                <td>{{ $bank->name }}</td>
                <td>{{ $bank->created_at }}</td>
                <td>{{ $bank->updated_at }}</td>
                <td>
                    <a class="btn btn-small btn-update" onclick="bank_show({{ $bank }});return false;">详情</a> 

                    <a href="{{ route('BankEdit', ['id' =>$bank->id ]) }}" class="btn btn-small btn-edit">修改</a> 

                    <a class="btn btn-small btn-delete" onclick="bank_delete({{ $bank->id }});return false;">删除</a> 
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="5">没有符合条件的记录</td>
            </tr>
            @endif

        </table>
    </div>
    <!-- 列表End -->

    @if (isset($banks))
    <!-- 分页Start -->
    <div class="paging">

        {!! $banks->appends(['name'=>$name])->render() !!}

    </div>
    <!-- 分页End -->
    @endif

</div>

<script type="text/javascript">
    // 开户行删除
    function bank_delete(id) {
        // 询问框
        layer.confirm('确认要删除吗，操作不可逆，请三思而后行', {
            btn: ['是','否'] //按钮
        }, function() {
            // 审核通过 ajax提交
            $.post("/admin/system/bank/" + id, {
                "_token": "{{ csrf_token() }}",
                "_method": "delete",
            }, function(data) {
                if (data.code == 0) {
                    layer.msg(data.msg, { icon: 1});
                    setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                } else {
                    layer.msg(data.msg, { icon: 2});
                }
            });
        }, function() {
            // 点击否，无需进行任何操作
        });
    }

    //详情弹出页面层
    function bank_show(bank) {
        // console.log(bank);
        layer.open({
            type: 1,
            title: '开户行详情',
            area: ['600px', '360px'],
            shadeClose: true, //点击遮罩关闭
            content: '\<\div style="padding:20px;">\<p\>开户行ID：'+bank.id+'\<\/p\>\<p\>开户行名称：'+bank.name+'\<\/p\>\<p\>创建时间：'+bank.created_at+'\<\/p\>\<p\>更新时间：'+bank.updated_at+'\<\/div>'
        });
    }

</script>

@endsection
