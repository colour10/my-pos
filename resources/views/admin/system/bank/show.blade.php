@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table">

    <br><br>

    <!--表单-->
    <div class="form-group">
        <label class="form-title">开户行名称：</label>
        <input class="form-control" type="text" value="{{ $bank->name }}" />
    </div>

    <div class="form-group">
        <label class="form-title">录入时间：</label>
        <input class="form-control" type="text" value="{{ $bank->created_at }}" />
    </div>

    <div class="form-group">
        <label class="form-title">更新时间：</label>
        <input class="form-control" type="text" value="{{ $bank->updated_at }}" />
    </div>

</div>
@endsection
