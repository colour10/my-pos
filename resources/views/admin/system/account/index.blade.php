@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table">

    @include('admin.system.account.search')

    <!-- 列表Start -->
    <div class="table-main">
        <table class="table table-hover">
            <tr class="th">
                <th>账户类型ID</th>
                <th>账户类型名称</th>
                <th>录入时间</th>
                <th>更新时间</th>
                <th>处理</th>
            </tr>

            @foreach ($accounts as $account)
            <tr>
                <td>{{ $account->id }}</td>
                <td>{{ $account->name }}</td>
                <td>{{ $account->created_at }}</td>
                <td>{{ $account->updated_at }}</td>
                <td>
                    <a class="btn btn-small btn-update" onclick="account_show({{ $account }});return false;">详情</a> 

                    <a href="{{ route('AccountEdit', ['id' =>$account->id ]) }}" class="btn btn-small btn-edit">修改</a> 

                    <a class="btn btn-small btn-delete" onclick="account_delete({{ $account->id }});return false;">删除</a> 
                </td>
            </tr>
            @endforeach

        </table>
    </div>
    <!-- 列表End -->

    @if (isset($accounts))
    <!-- 分页Start -->
    <div class="paging">

        {!! $accounts->appends(['name'=>$name])->render() !!}

    </div>
    <!-- 分页End -->
    @endif

</div>

<script type="text/javascript">
    // 账户类型删除
    function account_delete(id) {
        // 询问框
        layer.confirm('确认要删除吗，操作不可逆，请三思而后行', {
            btn: ['是','否'] //按钮
        }, function() {
            // 审核通过 ajax提交
            $.post("/admin/system/account/" + id, {
                "_token": "{{ csrf_token() }}",
                "_method": "delete",
            }, function(data) {
                if (data.code == 0) {
                    layer.msg(data.msg, { icon: 1});
                    setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                }
            });
        }, function() {
            // 点击否，无需进行任何操作
        });
    }

    //详情弹出页面层
    function account_show(account) {
        // console.log(account);
        layer.open({
            type: 1,
            title: '账户类型详情',
            area: ['600px', '360px'],
            shadeClose: true, //点击遮罩关闭
            content: '\<\div style="padding:20px;">\<p\>账户类型ID：'+account.id+'\<\/p\>\<p\>账户类型名称：'+account.name+'\<\/p\>\<p\>创建时间：'+account.created_at+'\<\/p\>\<p\>更新时间：'+account.updated_at+'\<\/div>'
        });
    }

</script>

@endsection
