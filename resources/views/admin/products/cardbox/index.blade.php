@extends('admin.layout.main')

@section('content')
<div class="container-fluid head-table">

    <!-- 内容检索 Start -->
    <div class="container-fluid">
        <div class="pull-left" style="width:84%;">
            <form method="get" id="form-search">

                <div class="select-main">
                    <div class="form-select">
                        <label class="form-select-title">卡片名称：</label>
                        <input type="text" class="form-control" placeholder="请输入卡片名称" name="merCardName">
                    </div>

                    <div class="form-select">
                        <label class="form-select-title"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;状态：</label>
                        <select class="form-control" name="status">
                            <option value=""@if ($request->get('status') === NULL) selected="selected"@endif>全部</option>
                            <option value="0"@if ($request->get('status') == '0') selected="selected"@endif>禁用</option>
                            <option value="1"@if ($request->get('status') == '1') selected="selected"@endif>启用</option>
                        </select>
                    </div>

                    <div class="form-select">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="submit" class="btn btn-large btn-update">提交</button>
                    </div>
                    <div class="form-select">
                        &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                    </div>

                </div>

            </form>
        
        </div>
        <div class="pull-right text-right" style="width:16%">
            <a href="{{ route('cardbox.create') }}" class="btn btn-large btn-add marginTop">银行卡添加</a>
        </div>
    </div>
    <!-- 内容检索 End -->

    <!-- 列表Start -->
    <div class="table-main">

        <form method="post" name="cardbox_form" id="myForm">

            @csrf

            <table class="table table-hover">
                <tr class="th">
                    <th>全选</th>
                    <th>卡片名称</th>
                    <th>渠道来源</th>
                    <th>排序</th>
                    <th>办卡返佣金额</th>
                    <th>推荐人返佣金额</th>
                    <th>推荐人上级返佣金额</th>
                    <th>办卡红色标识</th>
                    <th>卡片状态</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>

                @if ($cardboxes->count())
                @foreach ($cardboxes as $cardbox)
                <tr>
                    <td><input name="ids[]" type="checkbox" value="{{ $cardbox->id }}" /></td>
                    <td>{{ $cardbox->merCardName }}</td>
                    <td>{{ $cardbox->source }}</td>
                    <td>{{ $cardbox->sort }}</td>
                    <td>{{ $cardbox->cardBankAmount }}</td>
                    <td>{{ $cardbox->cardAmount }}</td>
                    <td>{{ $cardbox->cardTopAmount }}</td>
                    <td>{{ $cardbox->littleFlag }}</td>
                    <td>
                        @if ($cardbox->status == '0')
                            禁用
                        @else
                            启用
                        @endif
                    </td>
                    <td>{{ $cardbox->updated_at }}</td>
                    <td>
                        <a class="btn btn-small btn-update" onclick="cardbox_show({{ $cardbox->id }});return false;">详情</a> 
                        <a href="{{ route('cardbox.edit', ['id' => $cardbox->id]) }}" class="btn btn-small btn-check">变更</a> 
                        <!-- <a href="{{ route('cardbox.destroy', ['id' => $cardbox->id]) }}" class="btn btn-small btn-danger" onclick="cardbox_delete({{ $cardbox->id }});return false;">删除</a> -->
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="9">没有符合条件的记录</td>
                </tr>
                @endif


                @if ($cardboxes->count())
                <tr class="form-auto">
                    <td><input type="checkbox" name="checkAll" /></td>
                    <td class="red" colspan="1">←全选</td>
                    <td align="left" colspan="2">
                        <select class="form-control form-control2" name="i_method">
                            <option value="">选择操作</option>
                            <!-- <option value="1">删除</option> -->
                            <option value="2">启用</option>
                            <option value="3">禁用</option>
                        </select>
                        <input type="submit" class="btn btn-large btn-update margin2" name="i_do" value="执行操作" />
                    </td>
                    <td align="left" colspan="7">
                        注：排序按照排序数字倒序排列，数字越大排名越靠前~
                    </td>
                </tr>
                @endif

            </table>

        </form>

    </div>
    <!-- 列表End -->

    @if (isset($cardboxes))
    <!-- 分页Start -->
    <div class="paging">

        {!! $cardboxes->appends($request->all())->render() !!}　<p class="pull-right" style="padding:6px 12px;">共有{{ $cardboxes->total() }}条记录</p>

    </div>
    <!-- 分页End -->
    @endif

</div>

<script type="text/javascript">
    //详情弹出页面层
    function cardbox_show(id) {
        // 调用单个接口
        $.get('/admin/products/cardbox/'+id, function(response) {
            // 取出结果
            // console.log(response);
            // 判断逻辑
            if (response.code == '0') {
                layer.open({
                    type: 1,
                    title: '卡片详情',
                    area: ['600px', '360px'],
                    shadeClose: true, //点击遮罩关闭
                    content: "<div style='padding:20px;'>\
                    <p>卡片ID："+response.data.id+"</p>\
                    <p>卡片名称："+response.data.merCardName+"</p>\
                    <p>渠道来源："+response.data.source+"</p>\
                    <p>卡片图片：<br><img src='"+response.data.merCardImg+"'></p>\
                    <p>广告封面图片：<br><img style='width:100px;' src='"+response.data.advertiseImg+"'></p>\
                    <p>排序："+response.data.sort+"</p>\
                    <p>办卡返佣金额："+response.data.cardBankAmount+"</p>\
                    <p>推荐人返佣金额："+response.data.cardAmount+"</p>\
                    <p>推荐人上级返佣金额："+response.data.cardTopAmount+"</p>\
                    <p>卡片简介："+response.data.cardContent+"</p>\
                    <p>办卡URL链接地址："+response.data.creditCardUrl+"</p>\
                    <p>办卡红色标识："+response.data.littleFlag+"</p>\
                    <p>查询卡片申请进度URL地址："+response.data.creditCardJinduUrl+"</p>\
                    </div>"
                });
            } else {
                layer.open({
                    type: 1,
                    title: '卡片详情',
                    area: ['600px', '360px'],
                    shadeClose: true, //点击遮罩关闭
                    content: "<div style='padding:20px;'>无此卡片</div>"
                });
            }
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

        // 定义一个空数组
        var checkID = [];
        // 把所有被选中的复选框的值存入数组
        $("input[name='ids[]']:checked").each(function(i){
            checkID[i] = $(this).val();
        });

		//switch流程判断，记住这里confirm必须用return返回，否则永远为true;
		switch (i_method) {
            // 删除，用启用禁用代替
            // case 1:
            //     // layer处理
            //     layer.confirm('真的要删除吗，删除不可逆，请三思而后行！', {
			// 		btn: ['是','否'] //按钮
			// 	}, function() {
            //         defaultajax('{{ route("cardbox.destroys") }}', {
            //             '_token': "{{ csrf_token() }}",
            //             'ids': checkID,
            //         });
			// 	}, function() {
            //         // 如果否，则不操作
			// 	});
            //     break;
            // 启用
            case 2:
                // 询问启用
                layer.confirm('确认要启用吗？', {
                    btn: ['是','否'] //按钮
                }, function() {
                    // ajax批量启用
                    defaultajax('{{ route("cardbox.enables") }}', {
                        '_token': "{{ csrf_token() }}",
                        'ids': checkID,
                    });
                }, function() {
                    // 点击否，无需进行任何操作
                });
                break;
            // 禁用
            case 3:
                // 询问禁用
                layer.confirm('确认要禁用吗？', {
                    btn: ['是','否'] //按钮
                }, function() {
                    // ajax批量禁用
                    defaultajax('{{ route("cardbox.disables") }}', {
                        '_token': "{{ csrf_token() }}",
                        'ids': checkID,
                    });
                }, function() {
                    // 点击否，无需进行任何操作
                });
                break;
			default:
                layer.msg('请选择操作', { icon: 2 });
        }
        // 禁止自动跳转
        return false;
	});

    // 单个删除
    // function cardbox_delete(id) {
    //     // layer处理
    //     layer.confirm('真的要删除吗，删除不可逆，请三思而后行！', {
    //         btn: ['是','否'] //按钮
    //     }, function() {
    //         // 删除逻辑
    //         defaultajax('/admin/products/cardbox/'+id, {
    //             '_token': "{{ csrf_token() }}",
    //             '_method': 'DELETE',
    //         });
    //     }, function() {
    //         // 如果否，则不操作
    //     });
    //     // 取消自动提交
    //     return false;
    // }

</script>

@endsection
