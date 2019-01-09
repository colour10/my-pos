@extends('admin.layout.main')

@section('content')
<div class="container marginTop2 marginTop3">

    <form action="{{ route('cardbox.store') }}" method="post" name="cardbox_form" id="cardbox_form" autocomplete="off">

        @csrf

        <!--表单-->
        <div class="form-group">
            <br><br><br>
            <label class="form-title">卡片名称：</label>
            <input class="form-control" type="text" name="merCardName" autocomplete="off" />　<span class="form-title red show">(*必填)</span>
        </div>
        <div class="form-group">
            <label class="form-title">办卡封面：</label>
            <input class="form-control file" type="file" name="merCardImg" autocomplete="off" />　<span class="form-title red show">(*必填 240*96)</span>
        </div>

        <div class="form-group">
            <label class="form-title">广告封面：</label>
            <input class="form-control file" type="file" name="advertiseImg" autocomplete="off" />　<span class="form-title red show"></span>
        </div>

        <!-- <div class="form-group">
            <label class="form-title">进度封面：</label>
            <input class="form-control file" type="file" name="merCardJinduImg" autocomplete="off" />　<span class="form-title red show">(*必填 110*60)</span>
        </div> -->

        <div class="form-group">
            <label class="form-title">订单封面：</label>
            <input class="form-control file" type="file" name="merCardOrderImg" autocomplete="off" />　<span class="form-title red show"></span>
        </div>

        <div class="form-group">
            <label class="form-title">办卡返佣金额（元）：</label>
            <input class="form-control" type="text" name="cardBankAmount" autocomplete="off" /> 　<span class="form-title red show">(*必填)</span>
        </div>

        <div class="form-group">
            <label class="form-title">推荐人返佣金额（元）：</label>
            <input class="form-control" type="text" name="cardAmount" autocomplete="off" value="0.00" /> 　<span class="form-title red show">(*必填)</span>
        </div>

        <div class="form-group">
            <label class="form-title">推荐人上级返佣金额（元）：</label>
            <input class="form-control" type="text" name="cardTopAmount" autocomplete="off" value="0.00" /> 　<span class="form-title red show">(*必填)</span>
        </div>

        <div class="form-group">
            <label class="form-title">卡片简介：</label>
            <textarea type="text" name="cardContent" autocomplete="off" /></textarea>
        </div>
        <div class="form-group">
            <label class="form-title">办卡地址：</label>
            <input class="form-control" type="text" name="creditCardUrl" autocomplete="off" />　<span class="form-title red show">(*必填)</span>
        </div>
        <div class="form-group">
            <label class="form-title">卡片标识：</label>
            <input class="form-control" type="text" name="littleFlag" autocomplete="off" />　<span class="form-title red show">(*必填)</span>
        </div>
        <div class="form-group">
            <label class="form-title">卡片申请进度地址：</label>
            <input class="form-control" type="text" name="creditCardJinduUrl" autocomplete="off" />　<span class="form-title red show">(*必填)</span>
        </div>
        <div class="form-group">
            <label class="form-title">渠道来源：</label>
            <input class="form-control" type="text" name="source" autocomplete="off" />　<span class="form-title red show">(*必填)</span>
        </div>
        
        <div class="form-group">
            <label class="form-title">下卡比率：</label>
            <input class="form-control" type="text" name="rate" autocomplete="off" />　<span class="form-title red show">(*单位：%)</span>
        </div>          
        
        <div class="form-group">
            <label class="form-title">结算方式：</label>
            <input class="form-control" type="text" name="method" autocomplete="off" />　<span class="form-title red show"></span>
        </div>    

        <div class="form-group form-submit">
            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="cardbox_create();return false;">添加</button>
            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('cardbox.index') }}';">放弃</button>
        </div>
    </form>

</div>

<script type="text/javascript">
    // 银行卡添加验证
    function cardbox_create() {

        // 卡片名称
        if (document.cardbox_form.merCardName.value==""){
            layer.msg('请填写卡片名称！');
            document.cardbox_form.merCardName.focus();
            return false;
        }

        // 办卡封面
        if (document.cardbox_form.merCardImg.value==""){
            layer.msg('请上传办卡封面！');
            document.cardbox_form.merCardImg.focus();
            return false;
        }

        // // 进度封面
        // if (document.cardbox_form.merCardJinduImg.value==""){
        //     layer.msg('请上传进度封面！');
        //     document.cardbox_form.merCardJinduImg.focus();
        //     return false;
        // }

        // // 订单封面
        // if (document.cardbox_form.merCardOrderImg.value==""){
        //     layer.msg('请上传进度封面！');
        //     document.cardbox_form.merCardOrderImg.focus();
        //     return false;
        // }

        // 办卡返佣
        if (document.cardbox_form.cardBankAmount.value==""){
            layer.msg('请输入办卡返佣数额！');
            document.cardbox_form.cardBankAmount.focus();
            return false;
        }

        // 推荐人返佣
        if (document.cardbox_form.cardAmount.value==""){
            layer.msg('请输入推荐人返佣数额！');
            document.cardbox_form.cardAmount.focus();
            return false;
        }

        // 推荐人返佣不能大于办卡返佣
        if (parseInt(document.cardbox_form.cardAmount.value) > parseInt(document.cardbox_form.cardBankAmount.value)) {
            layer.msg('推荐人返佣不能大于办卡返佣！');
            document.cardbox_form.cardAmount.focus();
            return false;
        }

        // 推荐人上级返佣
        if (document.cardbox_form.cardTopAmount.value==""){
            layer.msg('请输入推荐人上级返佣数额！');
            document.cardbox_form.cardTopAmount.focus();
            return false;
        }

        // 办卡地址
        if (document.cardbox_form.creditCardUrl.value==""){
            layer.msg('请输入办卡URL地址！');
            document.cardbox_form.creditCardUrl.focus();
            return false;
        }

        // 卡片标识
        if (document.cardbox_form.littleFlag.value==""){
            layer.msg('请输入卡片标识！');
            document.cardbox_form.littleFlag.focus();
            return false;
        }

        // 卡片申请进度地址
        if (document.cardbox_form.creditCardJinduUrl.value=="") {
            layer.msg('请输入卡片申请进度地址！');
            document.cardbox_form.creditCardJinduUrl.focus();
            return false;
        }

        // 渠道来源
        if (document.cardbox_form.source.value==""){
            layer.msg('请输入渠道来源！');
            document.cardbox_form.source.focus();
            return false;
        }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('cardbox_form');
        ajax("{{ route('cardbox.store') }}", fd, "{{ route('cardbox.index') }}");

    }
</script>

@endsection

