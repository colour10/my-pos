<div class="form-group col-md-12">
    <label>{{ $param['title'] }}</label>
    <div class="checkbox-group">
        @foreach ($param['range'] as $range)
            <label>
                <input type="checkbox" name="{{ $param['name'] }}[]"
                       value="{{ $range['id'] }}"
                        {{ isset($param['data']) && in_array($range['id'], $param['data']) ? 'checked' : '' }}>
                {{ $range['name'] }}
            </label>
        @endforeach
    </div>
</div>
