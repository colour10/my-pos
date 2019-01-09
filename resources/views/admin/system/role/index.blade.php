@extends('admin.layout.main')

@section("content")
    <!-- Main content -->
    <section class="content head-table">

        <!-- 内容检索 Start -->
        <div class="container-fluid">
            <div class="pull-left" style="width:60%;">
                <form action="{{ route('RoleIndex') }}" method="get" id="form-search">
                    <div class="select-main">
                        <div class="form-select">
                            <label class="form-select-title">角色名称：</label>
                            <input type="text" class="form-control" placeholder="请输入角色名称" name="name">
                        </div>

                        <div class="form-select"> 
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-large btn-update">提交</button>
                        </div>

                        <div class="form-select"> 
                            &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                        </div>
                    </div>
                </form>
            
            </div>
            <div class="pull-right text-right" style="width:40%">
                <a href="{{ route('RoleCreate') }}" class="btn btn-large btn-add">角色创建</a>
            </div>
        </div>
        <!-- 内容检索 End -->


        <!-- Small boxes (Stat box) -->
        <div class="container-fluid">
            <div class="box">

                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 10px">序号</th>
                            <th>角色名称</th>
                            <th>角色描述</th>
                            <th>操作</th>
                        </tr>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{$role->id}}</td>
                                <td>{{$role->name}}</td>
                                <td>{{$role->description}}</td>
                                <td>
                                    <a href="{!! route('RolePermission', ['id' => $role->id]) !!}" class="btn btn-small btn-update">授权</a> 
                                    <a href="{!! route('RoleEdit', ['id' => $role->id]) !!}" class="btn btn-small btn-edit">编辑</a> 
                                    <a onclick="role_delete({{ $role->id }});return false;" class="btn btn-small btn-delete">删除</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="paging">
                    {!! $roles->appends(['name' => $name])->render() !!}
                </div>

            </div>
        </div>
    </section>

<script type="text/javascript">
    // 开户行删除
    function role_delete(id) {
        // 询问框
        layer.confirm('确认要删除吗，操作不可逆，请三思而后行', {
            btn: ['是','否'] //按钮
        }, function() {
            // 审核通过 ajax提交
            $.post("/admin/system/role/" + id, {
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