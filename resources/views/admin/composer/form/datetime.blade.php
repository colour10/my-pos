<div class="form-group col-md-12">
    <label>{{ $param['title'] }}</label>
    <div class="input-group
        @if($param['format'] == 'date')
            datepicker
        @elseif($param['format'] == 'time')
            timepicker
        @else
            datetimepicker
        @endif
    ">
        <input
                type="text"
                class="form-control"
                name="{{ $param['name'] }}"
                value="{{ !empty($param['data']) ? $param['data'] : '' }}"
        >
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div>
</div>

@push('style')
<link href="{{asset('plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet"/>
<link href="{{asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css')}}" rel="stylesheet"/>
@endpush

@push('scripts')
<script src="{{asset('plugins/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js')}}"></script>
<script>
    $(function () {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            locale: moment.locale('zh-cn')
        });
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            locale: moment.locale('zh-cn')
        });
        $('.timepicker').datetimepicker({
            format: ' HH:mm',
            locale: moment.locale('zh-cn')
        });
    });
</script>
@endpush
