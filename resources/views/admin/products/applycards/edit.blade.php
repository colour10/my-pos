@extends('admin.layout.main')

@section('content')

<div class="container container3 container12">

    <form method="post" name="applycard_form" id="applycard_form" autocomplete="off">

        @csrf
        {{ method_field('PUT') }}

        <div style="margin-left: 20%;">
            <div class="form-group">
                <label class="form-label">申请人姓名：</label>
                <input class="form-control" type="text" name="name" value="{{ $applycard->user_name }}" readonly="readonly" />
            </div>
            <div class="form-group">
                <label class="form-label">申请人手机：</label>
                <input class="form-control" type="text" name="mobile" value="{{ $applycard->user_phone }}" readonly="readonly" />
            </div>
            <div class="form-group">
                <label class="form-label">申请的卡片：</label>
                <input class="form-control" type="text" name="password" value="{{ $applycard->cardbox->merCardName }}" readonly="readonly" />
            </div>
            <div class="form-group">
                <label class="form-label">申请的时间：</label>
                <input class="form-control" type="text" name="email" value="{{ $applycard->created_at }}" readonly="readonly" />
            </div>
            <div class="form-group">
                <label class="form-label">状态：</label>
                <select class="form-control" name="status">
                    <option value="1">审核通过</option>
                    <option value="2">未通过</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" id="addyh" class="btn-groups btn-submit" onclick="applycard_edit(); return false;">修改</button>
                <button type="reset" class="btn-groups btn-reset" onclick="window.location.href='{{ route('applycards.index') }}';">放弃</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    // 银行卡更新验证
    function applycard_edit() {

        // 如果都通过了，那么就ajax提交
        var fd = getFormData('applycard_form');
        ajax("{{ route('applycards.update', ['id' => $applycard->id]) }}", fd, "{{ route('applycards.index') }}");

    }
</script>

@endsection
