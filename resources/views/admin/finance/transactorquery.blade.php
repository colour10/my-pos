@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table t0">

    <!-- 内容检索 Start -->
    <div class="container-fluid">
        <div class="pull-left" style="width:84%;">
            <form action="{{ route('FinanceTransactquery') }}" method="get" id="form-search">

                <div class="select-main">

                    <div class="form-select">
                        <label class="form-select-title">合伙人姓名：</label>
                        <input type="text" class="form-control" placeholder="请输入合伙人姓名"  name="name">
                    </div>

                    <div class="form-select" style="margin-left:1em;">
                        <label class="form-select-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;手机号：</label>
                        <input type="text" class="form-control" placeholder="请输入手机号"  name="mobile">
                    </div>

                    <div class="form-select">
                        <label class="form-select-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;账户类型：</label>
                        <select class="form-control" name="account_type">
                            <option value="" selected="selected">全部</option>
                            <option value="1">资金账户</option>
                        </select>
                    </div>

                    <div class="form-select">
                        <label class="form-select-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;调账类型：</label>
                        <select class="form-control" name="type">
                            <option value="" selected="selected">全部</option>
                            <option value="1">调账调入</option>
							<option value="2">调账调出</option>
                        </select>
                    </div>
                    

                    <div class="form-select">
                        <br>
                        <label class="form-select-title" >查询时间：</label>
                        <input class="form-control" type="text" name="start_time" id="test1" />
                        <div class="layui-form-mid">-</div>
                        <input class="form-control" type="text" name="end_time" id="test2" />
                    </div>

                    <div class="form-select">
                        <br>
                        <label class="form-select-title"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;调账状态：</label>
                        <select class="form-control" name="status">
                            <option value="" selected="selected">全部</option>
                            <option value="0">待审核</option>
                            <option value="1">审核通过</option>
                            <option value="2">未通过</option>
                        </select>
                    </div>

                    <div class="form-select">
                        <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="submit" class="btn btn-large btn-update">提交</button>
                    </div>
                    <div class="form-select">
                        <br>
                        &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                    </div>

                </div>

            </form>
        
        </div>

        <div class="pull-right text-right" style="width:16%">
            <a href="/admin/finance/export?<?php echo $_SERVER['QUERY_STRING']; ?>" class="btn btn-large btn-add marginTop">导出excel</a>
        </div>

    </div>
    <!-- 内容检索 End -->

    @if ($finances)
    <!-- 列表Start -->
    <div class="table-main">

        <form action="{{ route('FinanceTransactorstore') }}" method="post" name="transactor_form" id="myForm">

            @csrf

            <table class="table table-hover">

                <tr class="th">
                    <th>序号</th>
                    <th>合伙人编号</th>
                    <th>合伙人名称</th>
                    <th>账户类型</th>
                    <th>调账类型</th>
                    <th>调账金额</th>
                    <th>经办人</th>
                    <th>经办日期</th>
                    <th>审核人</th>
                    <th>审核日期</th>
                    <th>调账状态</th>
                    <th>调账原因</th>
                </tr>

                @if ($finances->count()) 
                @foreach ($finances as $finance)
                <tr>
                    <td>{{ $finance->id }}</td>
                    <td>{{ $finance->sid }}</td>
                    <td>{{ $finance->name }}</td>
                    <td>{{ $finance->at_name }}</td>
                    <td>{{ $finance->type_name }}</td>
                    <td>{{ $finance->amount }}</td>
                    <td>{{ $finance->creater_name }}</td>
                    <td>{{ $finance->created_at }}</td>
                    <td>{{ $finance->operater_name }}</td>
                    <td>{{ $finance->operated_at }}</td>
                    <td>{{ $finance->status_name }}</td>
                    <td>{{ $finance->description }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="12"><em class="red">没有符合条件的记录</em></td>
                </tr>
                @endif


            </table>
        </form>

    </div>
    <!-- 列表End -->


    @if (isset($finances))
    <!-- 分页Start -->
    <div class="paging">

        {!! $finances->appends($request->all())->render() !!}　<p class="pull-right" style="padding:6px 12px;">共有{{ $finances->total() }}条记录　当前总调账金额：{{ $sum }} 元</p>

    </div>
    <!-- 分页End -->
    @endif


    @else
    <p class="text-left text-indent2">该合伙人不存在!</p>
    @endif

</div>

<script type="text/javascript">
    // 经办审核
    function check(id) {
        //询问框
        layer.confirm('您是想审核通过还是不通过呢？', {
            btn: ['通过','不通过'] //按钮
        }, function() {
            // 审核通过 ajax提交
            $.post("/admin/finance/benefitcheck/" + id + "/successed", {
                "_token": "{{ csrf_token() }}"
            }, function(data) {
                if (data.code == 0) {
                    layer.msg(data.msg, { icon: 1});
                    setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                } else {
                    layer.msg(data.msg, { icon: 2});
                    setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                }
            });
        }, function() {
            // 审核不通过 ajax提交
            $.post("/admin/finance/benefitcheck/" + id + "/failed", {
                "_token": "{{ csrf_token() }}"
            }, function(data) {
                if (data.code == 0) {
                    layer.msg(data.msg, { icon: 1});
                    setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                } else {
                    layer.msg(data.msg, { icon: 2});
                    setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                }
            });
        });
    }


	//全选验证，通用模块
	var fm = document.getElementById('myForm');
	var checkAll = document.getElementsByName('checkAll')[0];
	if (checkAll) {
		checkAll.onclick = function () {
			for (var i=0; i<fm.elements.length; i++) {
				var e = fm.elements[i];
				if (e.name != 'checkAll') {
					e.checked = checkAll.checked;
				}
			}
		}
	}

	//分润复核模块，针对全选之后的操作
	$('input[name=i_do]').click(function() {
		var i_method = parseInt($('select[name=i_method] option:selected').val());
		//判断没有ID被选中
		if (!$('input[type=checkbox]').is(':checked')) {
			layer.msg('呃呃...请务必选择待操作的记录！');
			return false;
		}

		//switch流程判断，记住这里confirm必须用return返回，否则永远为true;
		switch (i_method) {
            case 1:
                // 定义一个空数组
                var checkID = [];
                // 把所有被选中的复选框的值存入数组
                $("input[name='ids[]']:checked").each(function(i){
                    checkID[i] = $(this).val();
                });

                // layer处理
                layer.confirm('你真的要审核通过吗？', {
					btn: ['通过','不通过'] //按钮
					}, function() {
						// 执行通过逻辑
						// 审核通过 ajax提交
						$.post("/admin/finance/benefitchecks/successed", {
                            "_token": "{{ csrf_token() }}",
                            "ids" : checkID,
						}, function(data) {
							if (data.code == 0) {
								layer.msg(data.msg, { icon: 1});
								setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
							} else {
								layer.msg(data.msg, { icon: 2});
								setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
							}
						});
					}, function() {
                        // 执行不通过逻辑
						$.post("/admin/finance/benefitchecks/failed", {
                            "_token": "{{ csrf_token() }}",
                            "ids" : checkID,
						}, function(data) {
							if (data.code == 0) {
								layer.msg(data.msg, { icon: 1});
								setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
							} else {
								layer.msg(data.msg, { icon: 2});
								setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
							}
						});
				});
				// 取消自动提交
				return false;
                break;
			default:
				layer.msg('请选择操作！');
				return false;
		}
	});

</script>

@endsection





