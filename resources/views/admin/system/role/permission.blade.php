@extends('admin.layout.main')

@section("content")
    <!-- Main content -->
    <section class="content head-table role-permission">
        <!-- Small boxes (Stat box) -->
        <div class="container-fluid">

            <div class="box">

                <div class="box-header with-border">
                    <h4 class="box-title">权限列表</h4>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <form action="{{ route('RoleAssignpermission', ['id' => $id]) }}" method="POST" name="assign_form">
                        {{csrf_field()}}
                        <div class="form-group">
                            @foreach($first_permissions as $first)
                                <div class="checkbox">
                                    <label>
                                        <input class="first-permissions" type="checkbox" data-id="{{ $first['id'] }}" name="permissions[]"
                                                @if ($myPermissions->contains($first))
                                                checked
                                                @endif
                                                value="{{$first['id']}}">
                                        {{$first['description']}}
                                    </label>

                                    <br>

                                    @foreach ($second_permissions as $second)
                                        @if ($second['pid'] == $first['id'])
                                        <label>
                                            <input class="second-permissions" type="checkbox" data-pid="{{ $second['pid'] }}" name="permissions[]"
                                                    @if ($myPermissions->contains($second))
                                                    checked
                                                    @endif
                                                    value="{{$second['id']}}">
                                            {{$second['description']}}
                                        </label>
                                        @endif
                                    @endforeach

                                    <br><br>

                                </div>

                            @endforeach
                        </div>

                        <!-- <div class="box-footer">
                            <button type="submit" class="btn btn-primary">提交</button>
                        </div> -->

                        <div class="form-group form-submit">
                            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="assign_permission(); return false;">提交</button>
                            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('RoleIndex') }}';">放弃</button>
                        </div>

                    </form>


                </div>
            </div>

        </div>
    </section>

<script type="text/javascript">
    // 角色分配权限验证
    function assign_permission() {

        // ajax提交
        var fd = getFormData('assign_form');
        ajax("{{ route('RoleAssignpermission', ['id' => $id]) }}", fd, "{{ route('RoleIndex') }}");

    }
</script>

@endsection