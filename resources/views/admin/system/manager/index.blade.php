@extends('admin.layout.main')

@section("content")
<div class="container-fluid head-table head-table2">

    <!-- 内容检索 Start -->
    <form action="{{ route('ManagerSearch') }}" method="get" id="form-search">
        <div class="select-main">
            <div class="form-select">
                <label class="form-select-title" >姓名：</label>
                <input type="text" class="form-control" placeholder="请输入姓名" name="name">
            </div>
            <div class="form-select">
                <label class="form-select-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;手机号：</label>
                <input type="text" class="form-control" placeholder="请输入手机号" name="mobile">
            </div>
            <div class="form-select">
                <label class="form-select-title"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;状态：</label>
                <select class="form-control" name="status">
                    <option value=""@if ($request->get('status') === NULL) selected="selected"@endif>全部</option>
                    <option value="1"@if ($request->get('status') == '1') selected="selected"@endif>正常</option>
                    <option value="0"@if ($request->get('status') == '0') selected="selected"@endif>停用</option>
                </select>
            </div>
            <div class="form-select">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                <button type="submit" class="btn btn-large btn-update">提交</button>
            </div>
            <div class="form-select">
                &nbsp;&nbsp;&nbsp;<a class="btn btn-large btn-add" href="{{ route('ManagerCreate') }}">添加</a>
            </div>

        </div>
    </form>
    <!-- 内容检索 End -->


    <!-- 列表Start -->
    <div class="table-main">
        <table class="table table-hover">
            <tr class="th">
                <th>员工ID</th>
                <th>姓名</th>
                <th>手机号</th>
                <th>邮箱</th>
                <th>创建人</th>
                <th>创建时间</th>
                <th>最后登录时间</th>
                <th>状态</th>
                <th>处理</th>
            </tr>

            @foreach ($managers as $manager)
            <tr>
                <td>{{ $manager->id }}</td>
                <td>{{ $manager->name }}</td>
                <td>{{ $manager->mobile }}</td>
                <td>{{ $manager->email }}</td>
                <td>{{ $manager->creater_name }}</td>
                <td>{{ $manager->created_at }}</td>
                <td>{{ $manager->last_login_at }}</td>
                <td>{{ $manager->status_name }}</td>
                <td>
                    <a href="{{ route('ManagerEdit', ['id' => $manager->id]) }}" class="btn btn-large btn-check">修改</a> 
                    <a href="{{ route('ManagerRole', ['id' => $manager->id]) }}" class="btn btn-large btn-delete">授权</a>
                </td>
            </tr>
            @endforeach

        </table>
    </div>
    <!-- 列表End -->

    @if (isset($managers))
    <!-- 分页Start -->
    <div class="paging">

        @if ($controller_action['action'] == 'search')
            {!! $managers->appends($request->all())->render() !!}
        @else
            {!! $managers->links() !!}
        @endif

    </div>
    <!-- 分页End -->
    @endif

</div>
@endsection