<div class="form-group col-md-12">
    <label>{{ $param['title'] }}</label>
    <input
            type="text"
            class="form-control"
            name="{{ $param['name'] }}"
            value="{{ !empty($param['data']) ? $param['data'] : '' }}"
            placeholder="{{ isset($param['note']) ? $param['note'] : '' }}"
    >
</div>
