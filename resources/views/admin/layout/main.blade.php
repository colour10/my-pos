<?php
	// 关联
    use \App\Http\Controllers\Controller;
	use \App\Models\Agent;
	use \App\Models\Manager;
	use \App\Models\Role;
    use \App\Models\Permission;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\Request;
    use Illuminate\Support\Facades\Redis;

    // 逻辑
	$controller = new Controller;
	$controller_action = $controller->getControllerAction();
    $path = Request::getPathInfo();
    // echo '<pre>';
    // print_r($controller_action);
    // print_r($path);

	// 取出用户登录ID
    $id = Session::get('admin')['admin_id'];
    $manager = Manager::find($id);

?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title>{{ $page_title }} - 意远代理商分润管理系统</title>
<link rel="stylesheet" href="/backend/css/bootstrap.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
body, form, p, ul, li, img, dl, dt, dd, th {
	margin:0;
	padding:0;
	border:none;
	list-style:none;
	font-weight:normal;
}
body {
	color:#777;
	font-size:14px;
	background:#fff;
	font-family:Microsoft YaHei, Arial;
}
a {
	color:#777;
	text-decoration: none;
	text-align: center
}
a:hover {
	text-decoration: none;
}
/*头部*/
.header {
	width:100%;
	margin:0 auto;
	background: #3992D0;
	border-color: #e7e7e7;
	position: absolute;
}
.header img {
	float: left;
	vertical-align:bottom;
	padding-top: 5px;
}
.header ul {
	margin-left: 201px;
	max-width:1600px;
	min-width:800px;
}
.header ul li {
	display: inline-block;
	width:160px;
	font-size: 16px;
	float: left;
}
.header ul li a {
	line-height: 60px;
	display: block;
	width: 90px;
	color:#fff;
}
.header ul li a:hover {
	color:#fff;
}
.header span {
	text-decoration:none;
	color:#f0F000;
	font-weight:normal;
	font-size:19px;
	position:absolute;
	top:23px;
	margin-left: 250px;
	line-height:20px;
	border-left:2px solid #F0f0f0;
	padding-left:20px;
}
/*left*/
.leftsidebar_box {
	position: absolute;
	width: 200px;
	background: #FAFAFA;
	height:100%;
	border: 1px solid #eee;
	margin-top: 60px;
}
.leftsidebar_box dl {
	border-bottom:1px solid #e5e5e5;
}
.leftsidebar_box dt {
	padding-left:40px;
	padding-right:10px;
	color:#585858;
	font-size:14px;
	position:relative;
	line-height:48px;
}
.leftsidebar_box dd {
	line-height:40px;
	background: #fff;
}
.leftsidebar_box dd a {
	line-height: 50px;
	display: block;
}
.leftsidebar_box dd a span {
	width:100px;
	text-align: left;
	margin:0 auto;
	display: block;
}
.leftsidebar_box dt img {
	position:absolute;
	right:10px;
	top:20px;
}
.leftsidebar_box dl dd:hover {
	background: #f1f5f9;
}
.leftsidebar_box dl>dt:hover {
	border-left: 3px solid #3992D0;
}
/*right*/
.container {
	background: #fff;
	margin-left:190px;
	width:88%;
	height:100%;
	position: absolute;
	margin-top: 60px;
}
.container2 {
	margin-left:100px;
	margin-top:20px;
	position:static;
}
.container-main- {
	margin: 8px 20px 124px;
}
.container .breadcrumbs {
	border-bottom: 1px solid #E5E5E5;
	background-color: #F5F5F5;
	line-height: 49px;
	display: block;
	color: #4C8FBD;
	font-size: 13px;
	text-align: left;
	padding-left: 20px;
	margin-bottom: 0px;
	/* width:99.8%; */
	width:103.2%;
}
.container .page-header- {
	border-bottom: 1px dotted #E2E2E2;
	padding-bottom: 16px;
	padding-top: 7px;
	font-size: 16px;
	margin: 8px 20px 24px;
}
.active {
	background: #3992D0;
	color:#fff;
}
/*** 表单 ***/
.form-group {
	margin-bottom:20px;
	overflow:hidden;
	font-size: 14px;
}
.form-group .form-control {
	border:1px solid #e5e6e7;
	border-radius:2px;
	width:12%;
	float: left;
	height:32px;
	padding:0 12px;
}
.form-group .form-title {
	float:left;
	width:30%;
	padding:7px;
	text-align:right;
}
.layui-form-mid {
	float: left;
	padding: 8px 0;
	margin:auto 10px;
}
.form-group .checkbox-inline {
	display:inline-block;
	padding-left:10px;
	line-height:35px;
	float: left;
}
.form-group textarea {
	border:1px solid #e5e6e7;
}
.form-group select {
	height:34px;
	line-height: 34px;
}
.btn-groups {
	float:left;
	width:16.33%;
	max-width:90px;
	text-align: center;
	padding: 5px 12px;
	font-size: 14px;
	line-height: 22px;
	border-radius: 8px;
}
.btn-submit {
	margin-left: 60px;
	background-color: #1E9FFF;
	color:#fff;
	border: 1px solid transparent;
}
.btn-reset {
	margin-left: 18px;
	border: 1px solid #ddd;
	color:#777;
}
.form-submit {
	margin-left: 19.5%;
}
.range-width {
	width: 50px;
	border:1px solid #e5e6e7;
	border-radius:2px;
	float: left;
	height:32px;
	padding:0 12px;
}
/* 重写css */
/* .header, .leftsidebar_box {
	position: static;
	overflow:hidden;
}
.leftsidebar_box {
	margin-top:0;
	width:14%;
} */
.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
	background-color:#3992D0;
	border-color:#3992D0;
}
.paging .pagination {
	float: right;
	margin-top: 0;
}
.marginTop .form-control {
	width:30%;
}
.marginTop2 {
	margin-top:20px;
}
.marginTop2 .form-control {
	width:20%;
}
.head-table {
	margin-top: 30px;
}
.head-table .select-main {
	margin-bottom:0;
}
.rt {
	padding: 0 15px;
	line-height: 36px;
}
/*-----------表格----------*/
.table-main {
	width: 100%;
	overflow-x: auto;
	overflow-y: hidden;
}
.table {
	border:0;
	border-spacing:0;
	border-collapse:collapse;
	overflow: hidden;
	width:100%;
}
.table>thead>tr>td, .table>thead>tr>th, .table>tbody>tr>td, .table>tbody>tr>th {
	border:1px solid #e7e7e7;
	line-height:1.42857;
	padding:6px;
	vertical-align:middle;
	white-space:nowrap;
	text-align: center;
}
/*表头背景色*/.table>thead .th {
	background: #eee;
}
/*斑马线*/.table>tbody>tr:nth-of-type(odd) {
background-color:#fbfbfb;
}
/*-----------分页-----------*/
.page {
	float:right;
	margin-top: 20px;
	line-height: 1.42857;
	border:1px solid #ddd;
	border-radius: 4px;
}
.page .current {
	float:left;
	background:#34B9FE;
	padding:5px 10px;
	border:#34B9FE;
	color: #fff;
}
.page a, .page .num {
	float:left;
	display: inline;
	border-right:1px solid #DDD;
	margin-left: -1px;
	padding: 4px 10px;
	position: relative;
	text-decoration: none;
	color:#777;
}
.head-table .table-main tr td, .head-table .table-main tr th {
	font-size:14px;
}
.head-table .table-main tr th {
	font-weight:bold;
}
.paging .pagination {
	float:right;
	margin-top:0;
}
/* 第三种表单*/
.select-main {
	margin-bottom:100px;
	margin-left: 20px;
	max-width:1600px;
	min-width:1200px;
}
.form-select {
	float: left;
	font-size: 14px;
	overflow:hidden;
}
.form-select .form-control {
	border:1px solid #e5e6e7;
	border-radius:2px;
	width:200px;
	float: left;
	height:32px;
	padding:0 12px;
}
.form-select .form-select-title {
	line-height: 32px;
	float: left;
}
.form-select:not(:first-child) {
margin-left:0px;
}
/*-----------按钮-----------*/
.btn {
	display: inline-block;
	border: 1px solid transparent;
	color:#fff;
	line-height: 22px;
	border-radius: 3px;
}
.btn:hover {
	opacity: .8;
	filter: alpha(opacity=80);
}
.btn-large {
	padding: 5px 12px;
	font-size: 14px;
}
.btn-small {
	padding: 0 7px;
	font-size: 12px;
}
/*默认按钮*/  .btn-default {
	border: 1px solid #C9C9C9;
	background-color: #fff;
	color: #777;
}
/*修改按钮*/  .btn-update {
	background-color: #009688;
}
/*编辑按钮*/  .btn-edit {
	background-color: #1E9FFF;
}
/*查看按钮*/  .btn-check {
	background-color: #F7B824;
}
/*删除按钮*/  .btn-delete {
	background-color: #FF5722;
}
/*添加按钮*/  .btn-add {
	border:1px solid #FF5722;
	color:#FF5722;
	background: #fff;
}
/*分配按钮*/  .btn-assign {
	background-color: #3992D0;
}
.btn + .btn {
	margin-left: 10px;
}
#form-search {
	margin-bottom:20px;
	overflow:hidden;
}
/* .table.table-bordered .btn {
	padding:0;
} */
.role-permission .box {
	width:95%;
	margin:0 auto;
}
.header span.logout {
	right:2px;
	top:2px;
	border:none;
	font-size:1em;
}
.marginTop .form-control20 {
	width:20%;
}
.head-table2 .select-main {
	margin-bottom:20px;
}
/*有边框的表单*/
.form-group .form-label {
	width:8.33333333%;
	float: left;
	display: block;
	height: 34px;
	line-height: 34px;
	border: 1px solid #e6e6e6;
	border-radius: 2px 0 0 2px;
	text-align: center;
	background-color: #FBFBFB;
	box-sizing: border-box;
	margin-right: -1px;
}
.container3 .form-group .form-control {
	width:25%;
}
.role-form {
	margin-left:280px;
	margin-top:50px;
}
.role-form .box-footer {
	margin-top:20px;
}
.t0 .btn-groups {
	width:auto;
}
.t0 .text-center {
	text-align:center;
}
.t0 .btn-submit {
	margin-left:0;
}
.t0 .btn-groups {
	float:none;
}
.t0 label {
	cursor:pointer;
}
.text-indent2 {
	text-indent:2.5em;
	color:red;
}
.hidden {
	display:none;
}
.form-control2 {
	width:60%;
	display:inline;
}
.margin2 {
	margin-left:10px;
}
#myForm .table>thead>tr>td,#myForm .table>thead>tr>th,#myForm .table>tbody>tr>td, #myForm .table>tbody>tr>th {
	padding:10px;
}
.white {
	background-color:white;
}
#myForm input[type="radio"],
#myForm input[type="checkbox"] {
	width:15px;
	height:15px;
}
.p22 p {
	line-height:32px;
	height:32px;
	margin-left:20px;
	color:red;
}
.marginTop3 .form-submit {
	margin-left:25.5%;
}
.marginTop3 .margin {
	margin-left:22%;
	color:red;
}
.marginTop3 .margin p {
	line-height:2em;
}
.marginTop4 .form-control20 {
	width:10%;
}
.marginTop4 .form-group span {
	line-height:1.4;
	margin-left:5px;
	position:relative;
	top:5px;
}
/* checkbox 去掉虚框*/
:focus {
    outline: 0 !important;
}
input::-moz-focus-inner {
    border-color: transparent !important;
}
.cursor label {
	cursor:pointer;
}
#inlineRadio2 {
	margin-left:10px;
}
.card-img, span.red {
	margin-left:10px;
	display:none;
}
.form-group span.red {
	color:red;
	text-align:left;
}
.form-group span.show {
    display:inline;
}
.marginTop4 .form-control-w20 {
    width: 20%;
}
em.red {
    font-style:normal;
    color:red;
}
.form-group .form-title.text-left {
    text-align:left;
    font-weight:normal;
}
.form-group textarea {
    width:50%;
    height: 30rem;
}
.form-group .form-control.file {
    padding:3px 12px;
}
.container12 .form-group .form-label {
    width:12.33333333%;
}
</style>
<script src="/backend/js/jquery-1.12.3.min.js"></script>
<script src="/backend/js/bootstrap.min.js"></script>
<script src="/backend/js/ajaxfileupload.js"></script>
<script src="/backend/layer/layer.js"></script>
<script src="/backend/laydate/laydate.js"></script>
<script src="/backend/js/laravel.js"></script>
</head>
<body>
<div class="header" style="Margin:suto"><a href="{{ route('index') }}"><img src="/backend/images/logo2.png" class="nav-img" /></a>
    <ul>
        <li><a href="{{ route('cardbox.index') }}">产品管理</a></li>
        <li><a href="javascript:void(0);">库存管理</a></li>
        <li><a href="javascript:void(0);">财务管理</a></li>
        <li><a href="javascript:void(0);">工资管理</a></li>
        @if (\Auth::user())
        <li><span>您好！{{ \Session::get('admin')['admin_name'] }}</span> <span class="logout"><a href="{{ route('logout') }}">退出登录</a></span></li>
        @endif
    </ul>
</div>
<div class="leftsidebar_box">

    <!-- 根据控制器选择展示不同的模块 -->
    @if (($controller_action['controller'] == 'AgentController') || ($controller_action['controller'] == 'BenefitController') || ($controller_action['controller'] == 'BenefitController') || ($controller_action['controller'] == 'FinanceController') || ($controller_action['controller'] == 'AdvanceController') || ($controller_action['controller'] == 'SystemController'))
    @include('admin.layout.defaultmain')
    @elseif ($controller_action['controller'] == 'ProductController')
    @include('admin.layout.productmain')
    @endif

</div>
<div class="container" style="min-width:600px">
	<span class="breadcrumbs"> 当前位置： <a href="/admin/system"><i class="fa fa-dashboard"></i> 后台首页</a> > {{ $page_title }}</span>
	@component('admin.composer.alert')
    @endcomponent
    @yield('content')
</div>

<script type="text/javascript">

    $(".leftsidebar_box dt").css({"background-color":"#FAFAFA"});
    $(".leftsidebar_box dt img").attr("src", "/backend/images/arrow_right.png");

    $(function() {
		// 默认隐藏菜单
        $(".leftsidebar_box dd").hide();
        $(".leftsidebar_box dt").click(function(){
            $(".leftsidebar_box dt").css({"background-color":"#FAFAFA"})
            $(this).css({"background-color": "#FAFAFA"});
            $(this).parent().find('dd').removeClass("menu_chioce");
            $(".leftsidebar_box dt img").attr("src","/backend/images/arrow_right.png");
            $(this).parent().find('img').attr("src","/backend/images/arrow_top.png");
            $(".menu_chioce").slideUp();
            $(this).parent().find('dd').slideToggle();
            $(this).parent().find('dd').addClass("menu_chioce");
        });

		// 查询结果放入缓存
		var modules = $('.leftsidebar_box').children('dl');

		<?php if ($controller_action['controller'] == 'AgentController') { ?>
		// 如果是Agent模块，那么合伙人管理模块开启
		$module = modules.eq(0).children('dd');
		$module.each(function() {
			$(this).show();
		});
		<?php } ?>

		<?php if ($controller_action['controller'] == 'BenefitController') { ?>
		// 如果是Benefit模块，那么分润管理模块开启
		$module = modules.eq(1).children('dd');
		$module.each(function() {
			$(this).show();
		});
		<?php } ?>

		<?php if ($controller_action['controller'] == 'FinanceController') { ?>
		// 如果是Finance模块，那么财务管理模块开启
		$module = modules.eq(2).children('dd');
		$module.each(function() {
			$(this).show();
		});
		<?php } ?>

		<?php if ($controller_action['controller'] == 'AdvanceController') { ?>
		// 如果是Advance模块，那么代付管理模块开启
		$module = modules.eq(3).children('dd');
		$module.each(function() {
			$(this).show();
		});
		<?php } ?>

		<?php if ($controller_action['controller'] == 'SystemController') { ?>
		// 如果是system模块，那么系统管理模块开启
		$module = modules.eq(4).children('dd');
		$module.each(function() {
			$(this).show();
		});
		<?php } ?>

        <?php
            if ($controller_action['controller'] == 'ProductController') {
                if (strpos($path, 'cardbox') !== false) {
        ?>
		// 如果是cardbox，那么产品管理模块开启
		$module = modules.eq(0).children('dd');
		$module.each(function() {
			$(this).show();
		});
        <?php
                } elseif (strpos($path, 'applycards') !== false) {
        ?>
		// 如果是Product模块，那么产品管理模块开启
		$module = modules.eq(1).children('dd');
		$module.each(function() {
			$(this).show();
		});
        <?php
                }
            }
        ?>

		// 开始时间
		laydate.render({
			elem: '#test1',
			type: 'datetime',
			// range: true //或 range: '~' 来自定义分割字符
		});

		// 截止时间
		laydate.render({
			elem: '#test2',
			type: 'datetime',
			// range: true //或 range: '~' 来自定义分割字符
		});

		// 分配权限逻辑
		var first_permissions = $('.first-permissions');
		var second_permissions = $('.second-permissions');
		var first_len = first_permissions.length;
		var second_len = second_permissions.length;
		// 只要不是分配权限页面，就无需执行本代码
		if (first_len > 0) {
			// 循环
			// 控制按钮
			first_permissions.each(function() {
				$(this).click(function() {
					// 找到当前元素的data-id值
					// 然后将其下面data-pid = 当前id的checkbox进行同步
					var id = $(this).attr('data-id');
					var checked_status = $(this).prop('checked');
					$('input[data-pid = '+id+']').prop('checked', checked_status);
				});
			});

			if (second_len > 0) {
				second_permissions.each(function() {
					// 逻辑2，如果下面的二级权限有一个取消了，那么上面的父类权限也要取消其权限选中状态的哦~
					$(this).click(function() {
						// 找到当前元素的data-pid值
						// 然后将其父类data-id = 当前pid的checkbox进行同步,但是是单方向的哦
						var pid = $(this).attr('data-pid');
						var checked_status = $(this).prop('checked');
						var parent_permission = $('input[data-id = '+pid+']');
						var son_permissions = $('input[data-pid = '+pid+']');
						// 如果其中一个子权限取消了选中，那么其父类权限选框也要取消选中
						if (!checked_status) {
							parent_permission.prop('checked', checked_status);
						} else {
							// 如果某一个一级权限下面所有的二级权限都选中了，那么其一级权限也要选中
							// 首先要判断这个一级菜单下面的二级菜单是否已经全部选中了
							// 判断个数即可，只要吻合说明已经全部选中
							if (son_permissions.length == $('input[data-pid = '+pid+']:checked').length) {
								parent_permission.prop('checked', checked_status);
							}
						}
					});
				});
			}
		}

		// 默认资金账户
		if ($('#account_type')) {
			$('#account_type').find('input').eq(0).attr('checked', 'checked');
		}

	});

</script>
</body>
</html>
