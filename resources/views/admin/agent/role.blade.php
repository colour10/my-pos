@extends('admin.layout.main')

@section('content')
<div class="container marginTop marginTop2">

    <form role="form" action="assignrole" method="POST" name="agent_form">
    
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
            <button type="submit" class="btn btn-primary">提交</button> <button type="button" class="btn btn-info" onclick="window.location.href='../';">放弃</button>
        </div>
    </form>

</div>

@endsection
