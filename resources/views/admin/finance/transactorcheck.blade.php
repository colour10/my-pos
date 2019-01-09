@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table t0">

    <!-- 内容检索 Start -->
    <div class="container-fluid">
        <div class="pull-left" style="width:84%;">
            <form action="{{ route('FinanceBenefitcheck') }}" method="get" id="form-search">

                <div class="select-main">

                    <div class="form-select">
                        <label class="form-select-title" >手机号：</label>
                        <input type="text" class="form-control" placeholder="请输入手机号"  name="mobile">
                    </div>

                    <div class="form-select">
                        <label class="form-select-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;账户类型：</label>
                        <select class="form-control" name="account_type">
                            <option value="" selected="selected">全选</option>
                            <option value="1">资金账户</option>
                        </select>
                    </div>

                    <div class="form-select">
                        <label class="form-select-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;调账类型：</label>
                        <select class="form-control" name="type">
                            <option value="" selected="selected">全选</option>
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
    </div>
    <!-- 内容检索 End -->

    @if ($finances)
    <!-- 列表Start -->
    <div class="table-main">

        <form action="{{ route('FinanceTransactorstore') }}" method="post" name="transactor_form" id="myForm">

            @csrf

            <table class="table table-hover">

                <tr class="th">
                    <th>全选</th>
                    <th>序号</th>
                    <th>Excel编号</th>
                    <th>合伙人编号</th>
                    <th>合伙人名称</th>
                    <th>经办人</th>
                    <th>经办日期</th>
                    <th>账户类型</th>
                    <th>调账类型</th>
                    <th>调账金额</th>
                    <th>调账原因</th>
                    <th>操作</th>
                </tr>

                @if ($finances->count()) 
                @foreach ($finances as $finance)
                <tr>
                    <td><input name="ids[]" type="checkbox" value="{{ $finance->id }}" /></td>
                    <td>{{ $finance->id }}</td>
                    <td>{{ $finance->excel_id }}</td>
                    <td>{{ $finance->sid }}</td>
                    <td>{{ $finance->name }}</td>
                    <td>{{ $finance->creater_name }}</td>
                    <td>{{ $finance->created_at }}</td>
                    <td>{{ $finance->at_name }}</td>
                    <td>{{ $finance->type_name }}</td>
                    <td>{{ $finance->amount }}</td>
                    <td>{{ $finance->description }}</td>
                    <td class="text-center">
                        <input type="hidden" name="agent_id" value="{{ $finance->id }}">
                        @if ($finance->fs == 0)
                            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="check({{ $finance->id }});return false;">审核</button>
                        @endif
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="12"><em class="red">没有符合条件的记录</em></td>
                </tr>
                @endif

                @if ($finances->count()) 
				<tr class="form-auto">
                    <td><input type="checkbox" name="checkAll" /></td>
                    <td class="red" colspan="1">←全选</td>

                    <td align="left" colspan="3">
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


    @if (isset($finances))
    <!-- 分页Start -->
    <div class="paging">

        {!! $finances->appends($request->all())->render() !!}　<p class="pull-right" style="padding:6px 12px;">共有{{ $finances->total() }}条记录　当前总调账金额：{{ $sum }} 元</p>

    </div>
    <!-- 分页End -->
    @endif


    @elseif (($request->get('mobile') || $request->get('account_type') || $request->get('type') || $request->get('start_time') || $request->get('end_time')) && !$finance)
    <p class="text-left text-indent2">该合伙人不存在!</p>
    @else
    <p class="text-left text-indent2 hidden">请输入查询条件</p>
    @endif

</div>

<script type="text/javascript">

    // 表单模型
    var fd = getFormData('transactor_form');

    // 经办审核
    function check(id) {
        //询问框
        layer.confirm('您是想审核通过还是不通过呢？', {
            btn: ['通过','不通过'] //按钮
        }, function() {
            // 发送验证码短信
            $.get("{{ route('FinanceCreatecode') }}", function(response) {
                // 发送逻辑
                console.log(response);
                sendmsg('2002', response);
                // layer输入确认
                layer.prompt({title: '请输入短信验证码', formType: 2}, function(text, index) {
                    layer.close(index);
                    // 判断两者是否相等
                    if (response != text) {
                        layer.msg('短信验证码不正确，请重新发送验证码', { icon: 2});
                    } else {
                        // 执行审核通过逻辑
                        // layer.msg('短信验证码输入正确', { icon: 1});
                        // 审核通过 ajax提交
                        $.post("/admin/finance/benefitcheck/" + id + "/successed", {
                            "_token": "{{ csrf_token() }}"
                        }, function(data) {
                            // 打印执行结果
                            console.log('==== 打印单条审核通过结果 Start ====');
                            console.log(data);
                            console.log('==== 打印单条审核通过结果 End ====');
                            // 逻辑
                            if (data.code == 0) {
                                layer.msg(data.msg, { icon: 1});
                                setTimeout("document.location.reload()", 3000); // 3000毫秒后跳转
                            } else {
                                layer.msg(data.msg, { icon: 2});
                                // 3000毫秒后跳转
                                // setTimeout("document.location.reload()", 3000);
                            }
                        });
                    }
                });
            });
        }, function() {
            // 发送验证码短信
            $.get("{{ route('FinanceCreatecode') }}", function(response) {
                // 发送逻辑
                console.log(response);
                sendmsg('2002', response);
                // layer输入确认
                layer.prompt({title: '请输入短信验证码', formType: 2}, function(text, index) {
                    layer.close(index);
                    // 判断两者是否相等
                    if (response != text) {
                        layer.msg('短信验证码不正确，请重新发送验证码', { icon: 2});
                    } else {
                        // 执行审核不通过逻辑
                        // layer.msg('短信验证码输入正确', { icon: 1});
                        // 审核不通过 ajax提交
                        $.post("/admin/finance/benefitcheck/" + id + "/failed", {
                            "_token": "{{ csrf_token() }}"
                        }, function(data) {
                            // 打印执行结果
                            console.log('==== 打印单条审核不通过结果 Start ====');
                            console.log(data);
                            console.log('==== 打印单条审核不通过结果 End ====');
                            // 逻辑                            
                            if (data.code == 0) {
                                layer.msg(data.msg, { icon: 1});
                                setTimeout("document.location.reload()", 3000); // 3000毫秒后跳转
                            } else {
                                layer.msg(data.msg, { icon: 2});
                                // 3000毫秒后跳转
                                // setTimeout("document.location.reload()", 3000);
                            }
                        });
                    }
                });
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
                    // 发送验证码短信
                    $.get("{{ route('FinanceCreatecode') }}", function(response) {
                        // 发送逻辑
                        console.log(response);
                        sendmsg('2002', response);
                        // layer输入确认
                        layer.prompt({title: '请输入短信验证码', formType: 2}, function(text, index) {
                            layer.close(index);
                            // 判断两者是否相等
                            if (response != text) {
                                layer.msg('短信验证码不正确，请重新发送验证码', { icon: 2});
                            } else {
                                // 执行审核通过逻辑
                                // layer.msg('短信验证码输入正确', { icon: 1});
                                // 执行通过逻辑
                                $.post("{{ route('FinanceBenefitcheckssuccessed') }}", {
                                    "_token": "{{ csrf_token() }}",
                                    "ids" : checkID,
                                }, function(data) {
                                    // 打印结果
                                    console.log('==== 打印批量审核通过结果 Start ====');
                                    console.log(data);
                                    console.log('==== 打印批量审核通过结果 End ====');
                                    // 逻辑
                                    if (data.code == 0) {
                                        layer.msg(data.msg, { icon: 1});
                                        // 3000毫秒后跳转
                                        setTimeout("document.location.reload()", 3000);
                                    } else {
                                        layer.msg(data.msg, { icon: 2});
                                        // 3000毫秒后跳转
                                        // setTimeout("document.location.reload()", 3000);
                                    }
                                });
                            }
                        });
                    });
				}, function() {
                    // 发送验证码短信
                    $.get("{{ route('FinanceCreatecode') }}", function(response) {
                        // 发送逻辑
                        console.log(response);
                        sendmsg('2002', response);
                        // layer输入确认
                        layer.prompt({title: '请输入短信验证码', formType: 2}, function(text, index) {
                            layer.close(index);
                            // 判断两者是否相等
                            if (response != text) {
                                return layer.msg('短信验证码不正确，请重新输入！');
                                // layer.msg('短信验证码不正确，请重新发送验证码', { icon: 2});
                            } else {
                                // 执行审核通过逻辑
                                // layer.msg('短信验证码输入正确', { icon: 1});
                                // 执行通过逻辑
                                $.post("/admin/finance/benefitchecks/failed", {
                                    "_token": "{{ csrf_token() }}",
                                    "ids" : checkID,
                                }, function(data) {
                                    // 打印结果
                                    console.log('==== 打印批量审核不通过结果 Start ====');
                                    console.log(data);
                                    console.log('==== 打印批量审核不通过结果 End ====');
                                    // 逻辑                                    
                                    if (data.code == 0) {
                                        layer.msg(data.msg, { icon: 1});
                                        // 3000毫秒后跳转
                                        setTimeout("document.location.reload()", 3000);
                                    } else {
                                        layer.msg(data.msg, { icon: 2});
                                        // 3000毫秒后跳转
                                        // setTimeout("document.location.reload()", 3000);
                                    }
                                });
                            }
                        });
                    });
				});
				// 取消自动提交
				return false;
                break;
			default:
				return layer.msg('请选择操作！');
		}
    });
    
    // 发送短信
    function sendmsg(id, msg) {
        // 发送短信
        $.post("{{ route('FinanceSendMsg') }}", {
            'sendid': id,
            'sendmsg': msg,
            "_token": "{{ csrf_token() }}",
        }, function(response) {
            // 数据返回测试
            // console.log(response);
            // 逻辑
            if (response.errcode == '0') {
                // 打印结果
                // console.log('短信发送成功');
                return layer.msg('短信发送成功', { icon: 1});
            } else {
                // 打印结果
                // console.log('短信发送失败');
                // 逻辑判断
                if (response.errcode == '1') {
                    layer.msg('手机号码格式错误', { icon: 2});
                } else if (response.errcode == '2') {
                    layer.msg('IP被拒绝', { icon: 2});
                } else if (response.errcode == '3') {
                    layer.msg('短信模版ID不存在或审核未通过', { icon: 2});
                } else if (response.errcode == '4') {
                    layer.msg('appkey不存在', { icon: 2});
                } else if (response.errcode == '5') {
                    layer.msg('param内容数据格式错误（与短信模版的变量数量不相符）', { icon: 2});
                } else if (response.errcode == '6') {
                    layer.msg('必填参数不正确', { icon: 2});
                } else if (response.errcode == '7') {
                    layer.msg('用户余额不足', { icon: 2});
                } else if (response.errcode == '8') {
                    layer.msg('param内容不合规或含违禁词', { icon: 2});
                } else if (response.errcode == '9') {
                    layer.msg('param内容长度超限', { icon: 2});
                } else if (response.errcode == '10') {
                    layer.msg('发送超频，请稍后再试...', { icon: 2});
                } else if (response.errcode == '-1') {
                    layer.msg('其他原因发送失败，请联系我们', { icon: 2});
                } else {
                    layer.msg('未知错误', { icon: 2});
                }
                // 5秒后关闭所有弹出层，这个去掉了，通过技术手段可以访问验证码
                // setTimeout("layer.closeAll()", 5000);
            }
        }).error(function(response) {
            // console.log(response);
        });
    }

</script>

@endsection





