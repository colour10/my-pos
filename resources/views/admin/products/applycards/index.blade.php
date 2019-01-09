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

                        <br><br>

                        <div class="form-select">
                            <label class="form-select-title">申请人检索：</label>
                            <select class="form-control" name="method" style="width:150px;">
                                <option value="">== 请选择 ==</option>
                                <option value="1">姓名</option>
                                <option value="2">手机</option>
                                <option value="3">身份证</option>
                            </select>
                            <input type="text" class="form-control" placeholder="" name="applyer"
                                   style="margin-left:10px; width:150px;">
                        </div>

                        @if (strpos(\Illuminate\Support\Facades\Request::path(), 'finished') !== false)

                        <br><br>

                        <div class="form-select">
                            <label class="form-select-title">状态检索：</label>
                            <select class="form-control" name="status" style="width:150px;">
                                <option value="">== 请选择 ==</option>
                                <option value="0">未审核</option>
                                <option value="1">已通过</option>
                                <option value="2">未通过</option>
                                <option value="3">无记录</option>
                            </select>
                        </div>
                        @endif

                        <div class="form-select">
                            <button type="submit" class="btn btn-large btn-update" style="margin-left:10px;">提交</button>
                        </div>
                        <div class="form-select">
                            &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                        </div>

                    </div>

                </form>

            </div>

            <!-- 导出 -->
            @if ($card_status == 0)
                <div class="pull-right text-right" style="width:16%; margin-top:25px;">
                    <a href="/admin/products/applycards/unaudited/export?<?php echo $_SERVER['QUERY_STRING']; ?>"
                       class="btn btn-large btn-add marginTop">导出excel</a>
                </div>
            @else
                <div class="pull-right text-right" style="width:16%; margin-top:25px;">
                    <a href="/admin/products/applycards/finished/export?<?php echo $_SERVER['QUERY_STRING']; ?>"
                       class="btn btn-large btn-add marginTop">导出excel</a>
                </div>
            @endif

        </div>
        <!-- 内容检索 End -->

        <!-- 列表Start -->
        <div class="table-main">

            <form method="post" name="applycard_form" id="myForm">

                @csrf

                <table class="table table-hover">
                    <tr class="th">
                        <th>全选</th>
                        <th>订单号</th>
                        <th>卡片名称</th>
                        <th>渠道来源</th>
                        <th>申请人</th>
                        <th>申请人手机号</th>
                        <th>申请人身份证</th>
                        <th>申请状态</th>
                        <th>申请时间</th>
                        <th>邀请人姓名</th>
                        <th>邀请人上级姓名</th>
                        <th>预计邀请人返佣</th>
                        <th>预计邀请人上级返佣</th>
                        <th>操作</th>
                    </tr>

                    @if ($applycards->count())
                        @foreach ($applycards as $applycard)
                            <tr>
                                <td><input name="ids[]" type="checkbox" value="{{ $applycard->id }}"/></td>
                                <td>{{ $applycard->order_id }}</td>
                                <td>{{ $applycard->merCardName }}</td>
                                <td>{{ $applycard->source }}</td>
                                <td>{{ $applycard->name }}</td>
                                <td>{{ $applycard->mobile }}</td>
                                <td>{{ $applycard->id_number }}</td>
                                <td>
                                    @if ($applycard->status == '0')
                                        审核中
                                    @elseif ($applycard->status == '1')
                                        通过
                                    @elseif ($applycard->status == '2')
                                        未通过
                                    @else
                                        无记录
                                    @endif
                                </td>
                                <td>{{ $applycard->created_at }}</td>
                                <td>{{ $applycard->parent_name }}</td>
                                <td>{{ $applycard->top_name }}</td>
                                <td>{{ $applycard->invite_money }}</td>
                                <td>{{ $applycard->top_money }}</td>
                                <td>
                                    <a class="btn btn-small btn-update"
                                       onclick="applycard_show({{ $applycard->id }});return false;">详情</a>
                                    @if (strpos(Request::path(), 'finished') === false)
                                        <a onclick="applycard_review({{ $applycard->id }});return false;"
                                           class="btn btn-small btn-check">审核</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="14">没有符合条件的记录</td>
                        </tr>
                    @endif


                    @if ($applycards->count() && strpos(Request::path(), 'finished') === false)
                        <tr class="form-auto">
                            <td><input type="checkbox" name="checkAll"/></td>
                            <td class="red" colspan="1">←全选</td>
                            <td align="left" colspan="2">
                                <select class="form-control form-control2" name="i_method">
                                    <option value="">选择操作</option>
                                    <option value="1">批量通过</option>
                                    <option value="2">批量不通过</option>
                                    <option value="3">批量无记录</option>
                                </select>
                                <input type="submit" class="btn btn-large btn-update margin2" name="i_do" value="执行操作"/>
                            </td>
                            <td align="left" colspan="10">
                                注：排序按照申请时间倒序排列，最新申请的排名靠前~
                            </td>
                        </tr>
                    @endif

                </table>

            </form>

        </div>
        <!-- 列表End -->

    @if (isset($applycards))
        <!-- 分页Start -->
            <div class="paging">

                {!! $applycards->appends($request->all())->render() !!}　<p class="pull-right" style="padding:6px 12px;">
                    共有{{ $applycards->total() }}条记录</p>

            </div>
            <!-- 分页End -->
        @endif

    </div>

    <script type="text/javascript">

        // 卡审核
        function applycard_review(id) {
            // 开始审核，加入一个无记录功能
            layer.confirm('您要审核为？', {
                btn: ['通过', '不通过', '无记录'],
                btn1: function () {
                    // 询问用户是否为首卡
                    layer.confirm('是否为首卡？', {
                        btn: ['是', '否'] //按钮
                    }, function () {
                        // 执行首卡逻辑
                        // 首卡，审核通过 ajax提交
                        $.post("/admin/products/applycards/review/" + id + "/firstsuccessed", {
                            "_token": "{{ csrf_token() }}"
                        }, function (data) {
                            if (data.code == 0) {
                                layer.msg(data.msg, {icon: 1});
                                setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                            }
                        });
                    }, function () {
                        // 执行非首卡逻辑
                        // 非首卡，审核通过 ajax提交
                        $.post("/admin/products/applycards/review/" + id + "/successed", {
                            "_token": "{{ csrf_token() }}"
                        }, function (data) {
                            if (data.code == 0) {
                                layer.msg(data.msg, {icon: 1});
                                setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                            }
                        });
                    });
                },
                btn2: function () {
                    // 审核不通过 ajax提交
                    $.post("/admin/products/applycards/review/" + id + "/failed", {
                        "_token": "{{ csrf_token() }}"
                    }, function (data) {
                        if (data.code == 0) {
                            layer.msg(data.msg, {icon: 2});
                            setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                        }
                    });
                },
                btn3: function () {
                    // 审核无记录 ajax提交
                    $.post("/admin/products/applycards/review/" + id + "/norecord", {
                        "_token": "{{ csrf_token() }}"
                    }, function (data) {
                        if (data.code == 0) {
                            layer.msg(data.msg, {icon: 2});
                            setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
                        }
                    });
                }
            });


            // //询问框
            // layer.confirm('您要审核为？', {
            //     btn: ['通过','不通过','无记录'] //按钮
            // }, function() {
            //     // 询问用户是否为首卡
            //     layer.confirm('是否为首卡？', {
            //         btn: ['是','否'] //按钮
            //     }, function() {
            //         // 执行首卡逻辑
            //         // 首卡，审核通过 ajax提交
            //         $.post("/admin/products/applycards/review/" + id + "/firstsuccessed", {
            //             "_token": "{{ csrf_token() }}"
            //         }, function(data) {
            //             if (data.code == 0) {
            //                 layer.msg(data.msg, { icon: 1});
            //                 setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
            //             }
            //         });
            //     }, function() {
            //         // 执行非首卡逻辑
            //         // 非首卡，审核通过 ajax提交
            //         $.post("/admin/products/applycards/review/" + id + "/successed", {
            //             "_token": "{{ csrf_token() }}"
            //         }, function(data) {
            //             if (data.code == 0) {
            //                 layer.msg(data.msg, { icon: 1});
            //                 setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
            //             }
            //         });
            //     });

            // }, function() {
            //     // 审核不通过 ajax提交
            //     $.post("/admin/products/applycards/review/" + id + "/failed", {
            //         "_token": "{{ csrf_token() }}"
            //     }, function(data) {
            //         if (data.code == 0) {
            //             layer.msg(data.msg, { icon: 2});
            //             setTimeout("document.location.reload()", 3000);//3000毫秒后跳转
            //         }
            //     });
            // });


        }

        //详情弹出页面层
        function applycard_show(id) {
            // 调用单个接口
            $.get('/admin/products/applycards/' + id, function (response) {
                // 取出结果
                console.log(response);
                // 判断逻辑
                if (response.code == '0') {
                    layer.open({
                        type: 1,
                        title: '申请卡片详情',
                        area: ['600px', '360px'],
                        shadeClose: true, //点击遮罩关闭
                        content: "<div style='padding:20px;'>\
                    <p>订单号：" + response.data.order_id + "</p>\
                    <p>卡片ID：" + response.data.id + "</p>\
                    <p>卡片名称：" + response.data.merCardName + "</p>\
                    <p>渠道来源：" + response.data.source + "</p>\
                    <p>申请人姓名：" + response.data.name + "</p>\
                    <p>申请人手机：" + response.data.mobile + "</p>\
                    <p>申请人身份证：" + response.data.id_number + "</p>\
                    <p>申请状态：" + response.data.status_name + "</p>\
                    <p>申请时间：" + response.data.created_at + "</p>\
                    <p>预计推荐人返佣：" + response.data.invite_money + "</p>\
                    <p>预计推荐人上级返佣：" + response.data.top_money + "</p>\
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
                for (var i = 0; i < fm.elements.length; i++) {
                    var e = fm.elements[i];
                    if (e.name != 'checkAll') {
                        e.checked = checkAll.checked;
                    }
                }
            }
        }

        // 针对全选之后的操作
        $('input[name=i_do]').click(function () {
            var i_method = parseInt($('select[name=i_method] option:selected').val());
            //判断没有ID被选中
            if (!$('input[type=checkbox]').is(':checked')) {
                layer.msg('呃呃...请务必选择待操作的记录！');
                return false;
            }

            // 定义一个空数组
            var checkID = [];
            // 把所有被选中的复选框的值存入数组
            $("input[name='ids[]']:checked").each(function (i) {
                checkID[i] = $(this).val();
            });

            //switch流程判断，记住这里confirm必须用return返回，否则永远为true;
            switch (i_method) {
                // 通过
                case 1:
                    // 询问通过
                    layer.confirm('确认要审核通过吗？', {
                        btn: ['是', '否'] //按钮
                    }, function () {
                        // ajax批量启用
                        // 成功操作逻辑
                        defaultajax('{{ route("applycards.enables") }}', {
                            '_token': "{{ csrf_token() }}",
                            'ids': checkID,
                        });
                    }, function () {
                        // 点击否，无需进行任何操作
                    });
                    break;
                // 不通过
                case 2:
                    // 询问不通过
                    layer.confirm('确认要审核不通过吗？', {
                        btn: ['是', '否'] //按钮
                    }, function () {
                        // ajax批量禁用
                        // 成功操作逻辑
                        // ajax批量禁用
                        defaultajax('{{ route("applycards.disables") }}', {
                            '_token': "{{ csrf_token() }}",
                            'ids': checkID,
                        });
                    }, function () {
                        // 点击否，无需进行任何操作
                    });
                    break;
                // 无记录
                case 3:
                    // 询问无记录
                    layer.confirm('确认要审核为批量无记录吗？', {
                        btn: ['是', '否'] //按钮
                    }, function () {
                        // ajax批量无记录
                        // 成功操作逻辑
                        // ajax批量无记录
                        defaultajax('{{ route("applycards.norecords") }}', {
                            '_token': "{{ csrf_token() }}",
                            'ids': checkID,
                        });
                    }, function () {
                        // 点击否，无需进行任何操作
                    });
                    break;
                default:
                    layer.msg('请选择操作', {icon: 2});
            }
            // 禁止自动跳转
            return false;
        });

    </script>

@endsection
