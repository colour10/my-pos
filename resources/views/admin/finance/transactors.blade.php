@extends('admin.layout.main')

@section('content')
<div class="container marginTop2 marginTop3">

    <form action="{{ route('FinanceTransactorsstore') }}" method="post" name="transactors_form" id="transactors_form" enctype="multipart/form-data">

        @csrf

        <!--表单-->
        <div class="form-group">
            <br><br><br>
            <label class="form-title">请选择文件：</label>
            <input class="form-control" type="file" name="file" id="file" />
        </div>

        <div class="form-group form-submit">
            <!-- <button type="submit" id="addyh" class="btn-groups btn-submit">添加</button> -->
            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="transactors_check();return false;">添加</button>
            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('FinanceTransactor') }}';">放弃</button>
        </div>

        <div class="form-group margin">
            <p>V1.1版本特别说明：</p>
            <p>1、增加了excel每条记录的编号，对账更方便。</p>
            <p>　　最新模板，请点击此处下载<span><a href="{{ route('FinanceDownload') }}">“批量经办模板”</a></span></p>
            <p>2、模板必须为excel2003，.xls格式</a></span></p>
        </div>

    </form>

</div>

<script type="text/javascript">
    // 调账经办批量添加逻辑
    function transactors_check() {

        // 文件上传判断
        if (document.transactors_form.file.value=="") {
            layer.msg('请选择上传文件！');
            document.transactors_form.file.focus();
            return false;
        }
        
        // 如果都通过了，那么就ajax提交
        var fd = getFormData('transactors_form');

        $.ajax({
            type: 'post',
            url: '/admin/finance/transactors',
            data: fd,
            async: false, 
            cache: false, 
            contentType: false, 
            processData: false, 
            beforeSend: function() {
                index = layer.load(1, {
                    shade: [0.5,'#fff'] //0.1透明度的白色背景
                });
            },
            complete: function () {
                layer.close(index);
            },
            success : function (response, status, xhr) {

                // 把json字符串解析为js对象
                response = $.parseJSON(response);

                // 根据信息返回
                if (response.code == '0') {
                    layer.msg(response.msg, { icon: 1});
                    //3000毫秒后跳转
                    setTimeout("window.location.href='{{ route('FinanceTransactor') }}'", 3000);
                } else {
                    layer.msg(response.msg, { icon: 2});
                    //3000毫秒后跳转
                    // setTimeout("document.location.reload()", 3000);
                }
            },
        });

        return false;

    }
</script>

@endsection
