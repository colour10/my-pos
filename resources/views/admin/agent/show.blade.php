@extends('admin.layout.main')

@section('content')
<div class="container marginTop">

    <div class="form-group">
        <br><br><br>
        <p>合伙人ID：{{ $agent->sid }}</p>
    </div>

    <div class="form-group">
        <p>简称：{{ $agent->sname }}</p>
    </div>

    <div class="form-group">
        <p>姓名：{{ $agent->name }}</p>
    </div>

    <div class="form-group">
        <p>身份证号：{{ $agent->id_number }}</p>
    </div>

    <div class="form-group">
        <p>联系电话：{{ $agent->mobile }}</p>
    </div>
    
    <div class="form-group">
        <p>开户行：{{ $agent->bank->name }}</p>
    </div>

    <div class="form-group">
        <p>支行名称：{{ $agent->branch }}</p>
    </div>

    <div class="form-group">
        <p>银行卡号：{{ $agent->card_number }}</p>
    </div>

    <div class="form-group">
        <p>审核状态：
            @if ($agent->status == 0)
                未审核
            @elseif ($agent->status == 1)
                审核通过
            @else
                审核未通过
            @endif
        </p>
    </div>

    <div class="form-group form-submit">
        <button type="button" id="addyh" class="btn-groups btn-submit" onclick="window.location.href='/admin/agent';">返回列表</button>
    </div>

</div>

<style>
.marginTop {
    margin-top:20px;
}
.marginTop .form-control {
    width:20%;
}
</style>

@endsection

