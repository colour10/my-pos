<dl>
        <dt>办卡银行管理<img src="/backend/images/arrow_right.png"></dt>
        
        <dd class="first_dd"><a href="{{ route('cardbox.create') }}"@if ($path == '/admin/products/cardbox/create') class="active"@endif><span>银行添加</span></a></dd>
        
        <dd><a href="{{ route('cardbox.index') }}"@if ($path != '/admin/products/cardbox/create' && strpos($path, 'cardbox') !== false) class="active"@endif><span>银行列表</span></a></dd>
        
    </dl>

    <dl>
        <dt>申请卡片管理<img src="/backend/images/arrow_right.png"></dt>
        
        <dd><a href="{{ route('applycards.index') }}"@if (strpos($path, 'applycards') !== false && strpos($path, 'applycards/') === false) class="active"@endif><span>待审核列表</span></a></dd>

        <dd><a href="{{ route('applycards.finished') }}"@if (strpos($path, 'applycards/finished') !== false) class="active"@endif><span>已完毕列表</span></a></dd>

    </dl>

