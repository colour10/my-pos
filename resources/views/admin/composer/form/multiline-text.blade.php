<div class="form-group col-md-12">
    <label>{{ $param['title'] }}</label>
    <textarea name="{{ $param['name'] }}" class="form-control"
              rows="{{ (isset($param['rows'])) ?  $param['rows'] : 5 }}"
              placeholder="{{ isset($param['note']) ? $param['note'] : '' }}"
    >{{(isset($param['data'])) ? $param['data'] : ''}}</textarea>
</div>
