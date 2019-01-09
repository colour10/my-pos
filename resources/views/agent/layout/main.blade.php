<?php
	// 关联
    use \App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Session;

    // 逻辑
	$controller = new Controller;
	$controller_action = $controller->getControllerAction();
    $path = Request::getPathInfo();
?>
<div class="tab_box row">
    <div class="tab_item col-sm-4 col-xs-4" onclick="location.href='{{ route("wxindex") }}'">
        @if ($path == '/agent/wx')
        <img src="/static/images/syy.png" alt="">
        @else
        <img src="/static/images/sy.png" alt="">
        @endif
        <span>首 页</span>
    </div>
    <div class="tab_item col-sm-4 col-xs-4"  onclick="window.location.href='{{ route("wxinvitation") }}'">
        <img src="/static/images/jlj.png" alt="">
        <span>邀 请</span>
    </div>
    <div class="tab_item col-sm-4 col-xs-4" onclick="location.href='{{ route("wxmine") }}'">
        @if ($path == '/agent/wx/mine')
        <img src="/static/images/wdd.png" alt="">
        @else
        <img src="/static/images/wd.png" alt="">
        @endif
        <span class="choosed">我 的</span>
    </div>
</div>