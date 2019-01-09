@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table">

    <!-- 内容检索 Start -->
    <div class="container-fluid">
        <div class="pull-left" style="width:84%;">
            <form action="{{ route('agents.search') }}" method="get" id="form-search">

                <div class="select-main">
                    <div class="form-select">
                        <label class="form-select-title" >姓名：</label>
                        <input type="text" class="form-control" placeholder="请输入姓名" name="name">
                    </div>
                    <div class="form-select">
                        <label class="form-select-title" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;手机号：</label>
                        <input type="text" class="form-control" placeholder="请输入手机号"  name="mobile">
                    </div>
                    <div class="form-select">
                        <label class="form-select-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;合伙人ID：</label>
                        <input type="text" class="form-control" placeholder="请输入合伙人ID" name="sid">
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
                        <label class="form-select-title"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;状态：</label>
                        <select class="form-control" name="status">
                            <option value=""@if ($request->get('status') === NULL) selected="selected"@endif>全部</option>
                            <option value="0"@if ($request->get('status') == '0') selected="selected"@endif>待审核</option>
                            <option value="1"@if ($request->get('status') == '1') selected="selected"@endif>审核通过</option>
                            <option value="2"@if ($request->get('status') == '2') selected="selected"@endif>未通过</option>
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
            <a href="{{ route('agents.create') }}" class="btn btn-large btn-add marginTop">合伙人开户</a>
        </div>
    </div>
    <!-- 内容检索 End -->



    <!-- 列表Start -->
    <div class="table-main">

        <form action="{{ route('FinanceTransactorstore') }}" method="post" name="transactor_form" id="myForm">

            @csrf

            <table class="table table-hover">
                <tr class="th">
                    <th>全选</th>
                    <th>合伙人ID</th>
                    <th>合伙人简称</th>
                    <th>姓名</th>
                    <th>手机号</th>
                    <th>注册途径</th>
                    <th>注册时间</th>
                    <th>审核状态</th>
                    <th>分润余额</th>
                    <th>处理</th>
                </tr>

                @foreach ($agents as $agent)
                <tr>
                    <td><input name="ids[]" type="checkbox" value="{{ $agent->id }}" /></td>
                    <td>{{ $agent->sid }}</td>
                    <td>{{ $agent->sname }}</td>
                    <td>{{ $agent->name }}</td>
                    <td>{{ $agent->mobile }}</td>
                    <td>{{ $agent->method_name }}</td>
                    <td>{{ $agent->created_at }}</td>
                    <td>{{ $agent->status_name }}</td>
                    <td>
                    @if ($agent->agentaccount)
                        {{ $agent->agentaccount->available_money }}
                    @else
                        0
                    @endif
                    </td>
                    <td>
                        <a class="btn btn-small btn-update" onclick="agent_show({{ json_encode($agent) }});return false;">详情</a> 
                        @if ($agent->status == 0)
                        <a class="btn btn-small btn-edit" onclick="agent_review({{ $agent->id }});return false;">审核</a> 
                        @endif
                        <a href="{{ route('agents.edit', ['id' => $agent->id]) }}" class="btn btn-small btn-check">变更</a> 
                    </td>
                </tr>
                @endforeach


                @if ($agents->count())
                <tr class="form-auto">
                    <td><input type="checkbox" name="checkAll" /></td>
                    <td class="red" colspan="1">←全选</td>
                    <td align="left" colspan="2">
                        <select class="form-control form-control2" name="i_method">
                            <!-- <option value="">选择操作</option> -->
                            <option value="1">审核</option>
                        </select>
                        <input type="submit" class="btn btn-large btn-update margin2" name="i_do" value="执行操作" />
                    </td>
                </tr>
                @endif

            </table>

        </form>

    </div>
    <!-- 列表End -->

    @if (isset($agents))
    <!-- 分页Start -->
    <div class="paging">

        @if ($controller_action['action'] == 'search')
            {!! $agents->appends($request->all())->render() !!}　<p class="pull-right" style="padding:6px 12px;">共有{{ $agents->total() }}条记录</p>
        @else
            {!! $agents->links() !!}　<p class="pull-right" style="padding:6px 12px;">共有{{ $agents->total() }}条记录</p>
        @endif

    </div>
    <!-- 分页End -->
    @endif

</div>

<script type="text/javascript">
    // 合伙人审核
    function agent_review(id) {
        //询问框
        layer.confirm('您是想审核通过还是不通过呢？', {
            btn: ['通过','不通过'] //按钮
        }, function() {
            // 审核通过 ajax提交
            $.post("/admin/agents/review/" + id + "/successed", {
                "_token": "{{ csrf_token() }}"
            }, function(data) {
                if (data.code == 0) {
                    layer.msg(data.msg, { icon: 1});
                    setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                }
            });
        }, function() {
            // 审核不通过 ajax提交
            $.post("/admin/agents/review/" + id + "/failed", {
                "_token": "{{ csrf_token() }}"
            }, function(data) {
                if (data.code == 0) {
                    layer.msg(data.msg, { icon: 2});
                    setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                }
            });
        });
    }

    //详情弹出页面层
    function agent_show(agent) {
        // 打印数据
        // console.log(agent);
        // console.log('---------- 打印下线合伙人 Start ----------');
        // console.log(agent.second_level);
        // console.log('---------- 打印下线合伙人 End ----------');
        // 初始化变量
        var html = '';
        var data = agent.second_level;
        var len = data.length;
        // 开始循环
        if (len > 0) {
            html += '<p>下级合伙人：'+len+'人</p>';
            for (var i=0; i<len; i++) {
                html += '<p>姓名：'+data[i].name+'　手机号：'+data[i].mobile+'　上级合伙人：'+data[i].parent_mobile+'（'+data[i].parent_name+'）　层级：'+data[i].level+'</p>';
            }
        } else {
            html += '<p>下级合伙人：无</p>';
        }

        // 展示数据
        layer.open({
            type: 1,
            title: '合伙人详情',
            area: ['660px', '400px'],
            shadeClose: true, //点击遮罩关闭
            content: "<div style='padding:20px;'><p>合伙人ID："+agent.sid+"</p><p>简称："+agent.sname+"</p><p>姓名："+agent.name+"</p><p>微信openid："+agent.openid+"</p><p>身份证号："+agent.id_number+"</p><p>联系电话："+agent.mobile+"</p><p>开户行："+agent.bank_name+"</p><p>支行名称："+agent.branch+"</p><p>银行卡号："+agent.card_number+"</p><p>审核状态："+agent.status_name+"</p><p>上级合伙人："+agent.parentopenid_name+"</p>"+html+"</div>"
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
                layer.confirm('你是想批量审核通过还是不通过呢？', {
					btn: ['通过','不通过'] //按钮
					}, function() {
                        // 执行通过逻辑
                        $.ajax({
                            type: "post",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "ids" : checkID,
                            },
                            url: "{{ route('agents.multi.successed') }}",
                            timeout: 999999,
                            beforeSend: function() {
                                index = layer.load(1, {
                                    shade: [0.5,'#fff'] //0.1透明度的白色背景
                                });
                            },
                            complete: function () {
                                layer.close(index);
                            },
                            success: function(response) {
                                if (response.code == '0') {
                                    layer.msg(response.msg, { icon: 1});
                                    setTimeout("document.location.reload()", 3000); // 3000毫秒后跳转
                                } else {
                                    layer.msg(response.msg, { icon: 2});
                                    // setTimeout("document.location.reload()", 3000);  // 3000毫秒后跳转
                                }
                            },
                            error: function(response) {
                                // console.log(response);
                            }
                        });
					}, function() {

                        // 执行不通过逻辑
                        $.ajax({
                            type: "post",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "ids" : checkID,
                            },
                            url: "{{ route('agents.multi.failed') }}",
                            timeout: 999999,
                            beforeSend: function() {
                                index = layer.load(1, {
                                    shade: [0.5,'#fff'] //0.1透明度的白色背景
                                });
                            },
                            complete: function () {
                                layer.close(index);
                            },
                            success: function(response) {
                                if (response.code == '0') {
                                    layer.msg(response.msg, { icon: 1});
                                    setTimeout("document.location.reload()", 3000); // 3000毫秒后跳转
                                } else {
                                    layer.msg(response.msg, { icon: 2});
                                    // setTimeout("document.location.reload()", 3000);  // 3000毫秒后跳转
                                }
                            },
                            error: function(response) {
                                // console.log(response);
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
