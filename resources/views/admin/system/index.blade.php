@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table">

    <div class="rt">

        <p>意远代理商分润管理系统后台基于laravel5.6 + Mysql5.7开发~</p>
        <p><strong>功能列表：</strong></p>
        <p>合伙人管理：包含合伙人开户、合伙人查询等功能。</p>
        <p>分润管理：包含分润明细查询、分润提现记录、分润余额查询等功能。</p>
        <p>财务处理：包含账户信息、资金冻结、分润制单、分润复核、调账经办、调账查询等功能。</p>
        <p>代付管理：包含代付通道管理、代付记录、代付账户充值等功能。</p>
        <p>系统管理：包含员工管理、系统设置、公告管理、开户行管理、本人信息维护、角色管理、权限管理、后台首页等功能。</p>

    </div>

</div>

@if (isset($error_msg))
<script type="text/javascript">
    $(function() {
        layer.msg('{{ $error_msg }}');
    });
</script>
@endif

@endsection
