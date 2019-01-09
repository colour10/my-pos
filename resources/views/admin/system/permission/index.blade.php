@extends('admin.layout.main')

@section("content")
    <!-- Main content -->
    <section class="content head-table">

        <!-- 内容检索 Start -->
        <div class="container-fluid">
            <div class="pull-left" style="width:60%;">
                <form action="{{ route('PermissionIndex') }}" method="get" id="form-search">
                    <div class="select-main">
                        <div class="form-select">
                            <label class="form-select-title">权限名称：</label>
                            <input type="text" class="form-control" placeholder="请输入权限名称" name="name">
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
                <a href="{{ route('PermissionCreate') }}" class="btn btn-large btn-add">权限创建</a>
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
                            <th style="width:5%">序号</th>
                            <th>权限名称</th>
                            <th>所属权限</th>
                            <th>权限描述</th>
                            <th>操作</th>
                        </tr>
                        @foreach($permissions as $permission)
                            <tr>
                                <td>{{$permission->id}}</td>
                                <td>{{$permission->name}}</td>
                                <td>{{$permission->pid}}</td>
                                <td>{{$permission->description}}</td>
                                <td>
                                    <a class="btn btn-small btn-update" onclick="permission_show({{ $permission }});return false;">详情</a> 
                                    <a href="{!! route('PermissionEdit', ['id' => $permission->id]) !!}" class="btn btn-small btn-edit">修改</a> 
                                    <a onclick="permission_delete({{ $permission->id }});return false;" class="btn btn-small btn-delete">删除</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="paging">
                    {!! $permissions->appends(['name' => $name])->render() !!}
                </div>
                
            </div>

        </div>
    </section>

<script type="text/javascript">
    // 权限删除
    function permission_delete(id) {
        // 询问框
        layer.confirm('确认要删除吗，操作不可逆，请三思而后行', {
            btn: ['是','否'] //按钮
        }, function() {
            // 审核通过 ajax提交
            $.post("/admin/system/permission/" + id, {
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

    //详情弹出页面层
    function permission_show(permission) {
        // console.log(permission);
        layer.open({
            type: 1,
            title: '权限详情',
            area: ['600px', '360px'],
            shadeClose: true, //点击遮罩关闭
            content: '\<\div style="padding:20px;">\<p\>权限ID：'+permission.id+'\<\/p\>\<p\>所属权限：'+permission.pid+'\<\/p\>\<p\>权限名称：'+permission.name+'\<\/p\>\<p\>权限简介：'+permission.description+'\<\/p\>\<p\>创建时间：'+permission.created_at+'\<\/p\>\<p\>更新时间：'+permission.updated_at+'\<\/div>'
        });
    }

</script>

@endsection