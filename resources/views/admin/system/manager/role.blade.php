@extends('admin.layout.main')

@section('content')
<div class="container marginTop marginTop2">
    <form role="form" action="{{ route('ManagerAssignrole', ['id' => $id ]) }}" method="POST" name="agent_form" class="role-form">
    
        {{csrf_field()}}
        {{ method_field('PUT') }}
        
        @foreach($roles as $role)
        <div class="checkbox">
            <label>
                <input type="checkbox" name="roles[]"
                @if ($myRoles->contains($role))
                checked
                @endif
                value="{{$role->id}}">
                {{$role->name}}
            </label>
        </div>
        @endforeach
        <div class="box-footer">
            <button type="submit" class="btn btn-primary" name="i_do">提交</button>
            <button type="button" class="btn btn-info" onclick="window.location.href='../';">放弃</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    // 分配角色
    $('button[name=i_do]').click(function() {
		//判断没有ID被选中
		if (!$('input[type=checkbox]').is(':checked')) {
			layer.msg('呃呃...请务必选择角色！');
			return false;
		}

        // 定义一个空数组
        var checkID = [];
        // 把所有被选中的复选框的值存入数组
        $("input[name='roles[]']:checked").each(function(i) {
            checkID[i] = $(this).val();
        });

        // 输出
        // console.log(checkID);

        // ajax提交
        var fd = getFormData('agent_form');
        ajax("{{ route('ManagerAssignrole', ['id' => $id ]) }}", fd, "{{ route('ManagerIndex') }}");
        return false;

    });
</script>

@endsection 