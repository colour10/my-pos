<div class="form-group col-md-12">
    <label>{{ $param['title'] }}</label>
    <div class="radio-group">
        @foreach ($param['range'] as $range)
            <label>
                <input type="radio"
                       name="{{ $param['name'] }}"
                       value="{{ $range['id'] }}"
                        {{ isset($param['data']) && $range['id'] == $param['data'] ? 'checked' : '' }}>
                {{ $range['name'] }}
            </label>
        @endforeach
    </div>
</div>
