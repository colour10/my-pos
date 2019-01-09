<div class="form-group col-md-12">
    <label>{{ $param['title'] }}</label>
    <input
            type="file"
            class="fileinput file-loading"
            width="100%"
            max_size="10mb"
            accept="jpg,jpeg,png,gif,bmp"
            name="{{ $param['name'] }}"
            upload="{{ $param['upload'] }}"
            value="{{ !empty($param['data']) ? $param['data'] : '' }}"
            placeholder="{{ isset($param['note']) ? $param['note'] : '' }}"
    >
</div>

@push('style')
    <link href="{{asset('plugins/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet"/>
@endpush

@push('scripts')
    <script src="{{asset('plugins/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-fileinput/js/locales/zh.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // 文件上传
            $('.fileinput').each(function () {
                var parentNode = $(this).parent();
                var name = $(this).attr('name');
                var param = {};
                param.autoReplace = true;
                param.paramuploadAsync = true;
                param.overwriteInitial = false;
                // 限制文件格式
                var accept = $(this).attr('accept').split(',');
                param.allowedFileExtensions = accept;
                // 限制上传数量
                var maxCount = $(this).attr('max-count');
                if (maxCount) {
                    param.maxFileCount = maxCount;
                }
                // 文件上传接口
                param.showUpload = false;
                param.initialPreviewShowDelete = false;
                var upload = $(this).attr('upload');
                if (upload) {
                    param.uploadUrl = upload;
                    param.showUpload = true;
                    param.initialPreviewShowDelete = true;
                }
                // 原始文件显示
                if ($(this).attr('value')) {
                    var value = $(this).attr('value');
                    value = JSON.parse(value);
                    param.initialPreview = [];
                    param.initialPreviewConfig = [];
                    for (var key in value) {
                        param.initialPreviewConfig.push(
                            {
                                'caption': value[key].caption,
                                'url': value[key].url,
                                'key': value[key].key,
                                'extra': {}
                            }
                        );
                        param.initialPreview.push('<img width="100%" key="' + value[key].id + '" src="' + value[key].file + '" alt="' + value[key].caption + '" title="' + value[key].caption + '"/>');
                    }
                }
                $(this).fileinput(param);
                $(this).on('fileuploaded', function(event, data, previewId, index) {
                    var form = data.form, files = data.files, extra = data.extra,
                        response = data.response, reader = data.reader;
                    // console.log('File uploaded triggered');
                    var extra = response.initialPreviewConfig[0].extra;
                    var input = $("<input type=hidden>");
                    input.attr("name", name + '_file_path');
                    input.val(extra.path);
                    parentNode.append(input);
                    var input = $("<input type=hidden>");
                    input.attr("name", name + '_file_name');
                    input.val(extra.name);
                    parentNode.append(input);
                });
            });
        });
    </script>
@endpush
