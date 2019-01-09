@extends('admin.layout.main')

@section('content')
<div class="container marginTop2 marginTop3">

    <form action="update" method="post" name="agent_form" id="agent_form">

        @csrf

        {{ method_field('PUT') }}

        <!--表单-->
        <div class="form-group">
            <br><br><br>
            <label class="form-title">简称：</label>
            <input class="form-control" type="text" name="sname" value="{{ $agent->sname }}" />　<span class="form-title red show">(*必填)</span>
        </div>
        <div class="form-group">
            <label class="form-title">姓名：</label>
            <input class="form-control" type="text" name="name" value="{{ $agent->name }}" />　<span class="form-title red show">(*必填)</span>
        </div>
        <div class="form-group">
            <label class="form-title">身份证号：</label>
            <input class="form-control" type="text" name="id_number" value="{{ $agent->id_number }}" />
        </div>
        <div class="form-group">
            <label class="form-title">联系电话：</label>
            <input class="form-control" type="text" name="mobile" value="{{ $agent->mobile }}" />　<span class="form-title red show">(*必填)</span>
        </div>
        
        @if ($cardinfo)
            <div class="form-group">
                <label class="form-title">银行卡号：</label>
                <input class="form-control" type="text" name="card_number" value="{{ $agent->card->card_number }}" />

                @if ($cardinfo['validated'] == false)
                    <img src="" alt="" class="card-img" style="display:none;">
                    <span class="red form-title">卡号错误</span>
                @else
                    <img src="<?php echo $cardinfo['bankImg']; ?>" alt="<?php echo $cardinfo['bankName']; ?>" class="card-img" style="display:inline;">
                    <span class="red form-title" style="display:none;">卡号错误</span>
                @endif

            </div>
        @else
            <div class="form-group">
                <label class="form-title">银行卡号：</label>
                <input class="form-control" type="text" name="card_number" value="" />
            </div>
        @endif

        @if ($cardinfo)
            <div class="form-group">
                <label class="form-title">支行名称：</label>
                <input class="form-control" type="text" name="branch" value="{{ $agent->card->branch }}" />
            </div>
        @else
            <div class="form-group">
                <label class="form-title">支行名称：</label>
                <input class="form-control" type="text" name="branch" value="" />
            </div>
        @endif

        <div class="form-group">
            <label class="form-title">登陆密码：</label>
            <input class="form-control" type="password" name="password" />　<span class="form-title red show">(*默认123456)</span>
        </div>

        <div class="form-group form-submit">
            <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="agent_edit();return false;">确认修改</button>
            <button type="button" class="btn-groups btn-reset" onclick="window.location.href='{{ route('agents.index') }}';">放弃</button>
        </div>

    </form>

</div>


<script type="text/javascript">

    // 验证
    $(function() {
        // 银行卡显示图标
        $('input[name=card_number]').blur(function() {
            // 请求卡号接口
            $.get("/admin/agents/checkcard/"+$('input[name=card_number]').val(), {}, function(data) {
                // console.log(data);
                if (data.validated == false) {
                    $('.card-img').hide();
                    $('.red').show();
                } else {
                    $('.red').hide();
                    $('.card-img').attr('src', data.bankImg);
                    $('.card-img').show();
                }
            });
        });
    });

    // 合伙人修改验证
    function agent_edit() {

        // 验证简称
        if (document.agent_form.sname.value==""){
            layer.msg('请填写简称！');
            document.agent_form.sname.focus();
            return false;
        }

        // 验证姓名
        if (document.agent_form.name.value==""){
            layer.msg('请填写姓名！');
            document.agent_form.name.focus();
            return false;
        }
        var name = /^[\u4e00-\u9fa5]{2,6}$/;
        if (!name.test(document.agent_form.name.value)){
            layer.msg('姓名格式不正确，请重新填写！');
            document.agent_form.name.focus();
            return false;
        }

        // // 身份证号码
        // if (document.agent_form.id_number.value=="") {
        //     layer.msg('请填写身份证号码！');
        //     document.agent_form.id_number.focus();
        //     return false;
        // }
        // var ifid = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
        // if (!ifid.test(document.agent_form.id_number.value)){
        //     layer.msg('身份证号码格式不正确，请重新填写！');
        //     document.agent_form.id_number.focus();
        //     return false;
        // }

        // 手机号码
        if (document.agent_form.mobile.value=="") {
            layer.msg('请填写联系电话！');
            document.agent_form.mobile.focus();
            return false;
        }
        var mob = /^1[3,4,5,6,7,8]\d{9}$/;
        if (!mob.test(document.agent_form.mobile.value)){
            layer.msg('联系电话格式不正确，请重新填写！');
            document.agent_form.mobile.focus();
            return false;
        }

        // // 银行卡卡号
        // if (document.agent_form.card_number.value=="") {
        //     layer.msg('请填写银行卡卡号！');
        //     document.agent_form.card_number.focus();
        //     return false;
        // }
        // var ifcard = /^(\d{16}|\d{19}|\d{17})$/;
        // if (!ifcard.test(document.agent_form.card_number.value)){
        //     layer.msg('卡号格式不正确，请重新填写！');
        //     document.agent_form.card_number.focus();
        //     return false;
        // }

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('agent_form');
        ajax("{{ route('agents.update', ['id' => $agent->id]) }}", fd, "{{ route('agents.index') }}");

    }
</script>


@endsection

