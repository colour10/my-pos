<dl>
        <dt>合伙人管理<img src="/backend/images/arrow_right.png"></dt>
        
        <dd class="first_dd"><a href="{{ route('agents.create') }}"@if ($path == '/admin/agents/create') class="active"@endif><span>合伙人开户</span></a></dd>
        
        <dd><a href="{{ route('agents.index') }}"@if ($path != '/admin/agents/create' && strpos($path, 'agents') !== false) class="active"@endif><span>合伙人列表</span></a></dd>
    </dl>

    <dl>
        <dt>分润管理<img src="/backend/images/arrow_right.png"></dt>

        <dd class="first_dd"> <a href="{{ route('BenefitInfo') }}"@if (strpos($path, '/benefit/info') !== false) class="active"@endif><span>分润明细查询</span></a></dd>

        <dd><a href="{{ route('BenefitWithdraw') }}"@if (strpos($path, '/benefit/withdraw') !== false) class="active"@endif><span>分润提现记录</span></a></dd>

        <dd><a href="{{ route('BenefitBalance') }}"@if (strpos($path, '/benefit/balance') !== false) class="active"@endif><span>分润余额查询</span></a></dd>
        
    </dl>

    <dl>
        <dt>财务处理<img src="/backend/images/arrow_right.png"></dt>

        <dd class="first_dd"><a href="{{ route('FinanceShow') }}"@if (strpos($path, '/finance/show') !== false) class="active"@endif><span>账户信息</span></a></dd>

        <dd><a href="{{ route('FinanceFreeze') }}"@if (strpos($path, '/finance/freeze') !== false) class="active"@endif><span>资金冻结</span></a></dd>

        <dd><a href="{{ route('FinanceBenefitbill') }}"@if (strpos($path, '/finance/benefitbill') !== false) class="active"@endif><span>分润制单</span></a></dd>

        <dd><a href="{{ route('FinanceTransactor') }}"@if (strpos($path, '/finance/transactor') !== false) class="active"@endif><span>调账经办</span></a></dd>

        <dd><a href="{{ route('FinanceBenefitcheck') }}"@if (strpos($path, '/finance/benefitcheck') !== false) class="active"@endif><span>分润复核</span></a></dd>

        <dd><a href="{{ route('FinanceTransactquery') }}"@if (strpos($path, '/finance/transactquery') !== false) class="active"@endif><span>调账查询</span></a></dd>

    </dl>

    <dl>
        <dt>代付管理<img src="/backend/images/arrow_right.png"></dt>

        <dd class="first_dd"><a href="{{ route('AdvanceMethod') }}"@if (strpos($path, '/advance/method') !== false) class="active"@endif><span>代付通道管理</span></a></dd>

        <dd><a href="{{ route('AdvanceList') }}" @if (strpos($path, '/advance/list') !== false || strpos($path, '/advance/search') !== false) class="active"@endif><span>代付记录</span></a></dd>

        <dd><a href="{{ route('AdvanceRecharge') }}"@if (strpos($path, '/advance/recharge') !== false) class="active"@endif><span>代付账户充值</span></a></dd>
    </dl>

    <dl>
        <dt>系统管理<img src="/backend/images/arrow_right.png"></dt>

        <dd class="first_dd"><a href="{{ route('ManagerIndex') }}"@if (strpos($path, '/system/manager') !== false) class="active"@endif><span>员工管理</span></a></dd>

        <dd><a href="{{ route('SetupIndex') }}"@if (strpos($path, '/system/setup') !== false) class="active"@endif><span>系统设置</span></a></dd>

        <dd><a href="{{ route('NoticeIndex') }}"@if (strpos($path, '/system/notice') !== false) class="active"@endif><span>公告管理</span></a></dd>

        <dd><a href="{{ route('BankIndex') }}"<?php if (strpos($path, 'bank') !== false) { echo ' class="active"'; } ?>><span>开户行管理</span></a></dd>

        <dd><a href="{{ route('PersonalIndex') }}"@if (strpos($path, '/system/personal') !== false) class="active"@endif><span>本人信息维护</span></a></dd>

        <dd><a href="{{ route('RoleIndex') }}"@if (strpos($path, '/system/role') !== false) class="active"@endif><span>角色管理</span></a></dd>

        <dd><a href="{{ route('PermissionIndex') }}"@if (strpos($path, '/system/permission') !== false) class="active"@endif><span>权限管理</span></a></dd>

        <dd><a href="{{ route('AccountIndex') }}"@if (strpos($path, '/system/account') !== false) class="active"@endif><span>账户类型管理</span></a></dd>

        <dd><a href="{{ route('index') }}"@if (strpos($path, '/system/index') !== false) class="active"@endif><span>后台首页</span></a></dd>
    </dl>
    