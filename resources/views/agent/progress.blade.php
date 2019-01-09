<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">  
    <title>进度查询</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/mine.css') }}">
</head>
<body>
	<div class="progress_prompt">
		<img class="progress_prompt_img" src="/static/images/prompt.png" />
		<span class="progress_prompt_text">提示：提交7个工作日后可查询办卡结果！</span>
	</div>
	<div class="progress_banner">
		<img class="nopoint" src="/static/images/head_banner.png" />
	</div>
	<div class="bankGrogress">
	</div>

	<!-- <div class="service_telphone">
		<a href="tel:4000421110">客服电话：400-042-1110</a>
	</div> -->

    @include('agent.layout.floatbtn')

	<script type="text/javascript" src="/static/js/jquery.js"></script>
	<script type="text/javascript" src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
    <script type="text/javascript">
        // 逻辑
        $.post('{{ route("wxcardboxes") }}', {
            '_token': '{{ csrf_token() }}',
        }, function(response) {
            if (response.code == '0') {
                var html = '';
                var len = response.data.length;
                for (var i = 0; i < len; i++) {
                    html+='<div class="grogress_item col-sm-6 col-xs-6" data-href="'+response.data[i].creditCardJinduUrl+'">';
                    html+='<img src="'+response.data[i].merCardOrderImg+'" />';
                    html+='<text>'+response.data[i].merCardName+'</text></div>';
                }

                // 写入dom
                $('.bankGrogress').append(html);

                // 办卡点击跳转逻辑
                $('.grogress_item').each(function() {
                    $(this).click(function() {
                        location.href = $(this).attr('data-href');
                    });
                });

            } else {

            }
        });
    </script>
</body>
</html>
