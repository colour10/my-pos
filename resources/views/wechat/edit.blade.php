<!doctype html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>回复公众号留言页面</title>
	<link rel="stylesheet" type="text/css" href="/wechat/css/default.css">
    <!-- CSS reset -->
	<link rel="stylesheet" href="/wechat/css/reset.css">
    <!-- Resource style -->
	<link rel="stylesheet" href="/wechat/css/style.css">
    <!-- Modernizr -->
	<script src="/wechat/js/modernizr.js"></script>
    <style>
        .messages {
            margin-top: 3em;
        }
        .news p, .cd-form .error-message p {
            text-align:left;
            line-height:1.36em;
        }
        .red {
            color:red;
        }
        .js .floating-labels div {
            margin:2em 0;
        }
    </style>
</head>
<body>
	<div class="container">

		<form class="cd-form floating-labels" method="post" name="wechat_answer">
            @csrf
			<fieldset>
				<legend>回复留言</legend>

                <div class="error-message">
                    <p>{{ $user_prefix }}【{{ $user_name }}】于{{ $wechat_message->created_at }}留言：{{ $wechat_message->ask_msg }}</p>
                </div>

				<!-- <div class="error-message">
					<p>请输入有效的电子邮件地址</p>
				</div>

				<div class="icon">
					<label class="cd-label" for="cd-name">名称</label>
					<input class="user" type="text" name="cd-name" id="cd-name" required>
			    </div> 

			    <div class="icon">
			    	<label class="cd-label" for="cd-company">公司</label>
					<input class="company" type="text" name="cd-company" id="cd-company">
			    </div> 

			    <div class="icon">
			    	<label class="cd-label" for="cd-email">邮箱</label>
					<input class="email error" type="email" name="cd-email" id="cd-email" required>
			    </div> -->
			</fieldset>

			<fieldset>
				<!-- <legend>项目信息</legend>

				<div>
					<h4>预算</h4>

					<p class="cd-select icon">
						<select class="budget">
							<option value="0">选择预算</option>
							<option value="1">&lt; $5000</option>
							<option value="2">$5000 - $10000</option>
							<option value="3">&gt; $10000</option>
						</select>
					</p>
				</div>  -->

				<!-- <div>
					<h4>项目类型</h4>

					<ul class="cd-form-list">
						<li>
							<input type="radio" name="radio-button" id="cd-radio-1" checked>
							<label for="cd-radio-1">Choice 1</label>
						</li>
							
						<li>
							<input type="radio" name="radio-button" id="cd-radio-2">
							<label for="cd-radio-2">Choice 2</label>
						</li>

						<li>
							<input type="radio" name="radio-button" id="cd-radio-3">
							<label for="cd-radio-3">Choice 3</label>
						</li>
					</ul>
				</div> -->

				<!-- <div>
					<h4>特征</h4>

					<ul class="cd-form-list">
						<li>
							<input type="checkbox" id="cd-checkbox-1">
							<label for="cd-checkbox-1">Option 1</label>
						</li>

						<li>
							<input type="checkbox" id="cd-checkbox-2">
							<label for="cd-checkbox-2">Option 2</label>
						</li>

						<li>
							<input type="checkbox" id="cd-checkbox-3">
							<label for="cd-checkbox-3">Option 3</label>
						</li>
					</ul>
				</div> -->

				<div class="icon">
					<label class="cd-label" for="answer_msg">回复内容</label>
	      			<textarea class="message" name="answer_msg" id="answer_msg" required></textarea>
				</div>

				<div>
			      	<input type="submit" value="发送信息" id="submit">
			    </div>
			</fieldset>

            <!-- 消息列表 Start -->
			<fieldset class="messages">
				<legend>消息列表</legend>
                <div class="news_list"></div>
			</fieldset>
            <!-- 消息列表 end -->

		</form>
	</div>
	
	<script src="/wechat/js/jquery-2.1.1.js"></script>
	<script src="/wechat/js/main.js"></script>
    <script src="/backend/layer/layer.js"></script>
    <script src="/backend/js/laravel.js"></script>
    <script type="text/javascript">
        // 初始化
        $(function() {
			// 判断当前消息是否进行了回复
			if (check_is_answered()) {
				layer.msg('温馨提示：该留言已被管理员回复');
			}

            // 消息列表初始化
            $.post('{{ route("wechat.wechatmsgs") }}', {
                '_token': "{{ csrf_token() }}",
                'ask_openid': "{{ $wechat_message->ask_openid }}",
            }, function(response) {
                // 测试结果
				console.log('-------取出当前用户的消息列表 start-------');
				console.log(response);
				console.log('-------取出当前用户的消息列表 end-------');
                // 逻辑
                var len = response.length;
                var html = '';
                for (var i=0; i<len; i++) {
                    html += '<div class="news">';
                    html += '<p>'+response[i].current_id+'、'+response[i].ask_user_prefix+'【'+response[i].ask_name+'】于'+response[i].created_at+'留言：'+response[i].ask_msg+'</p>';
                    html += '<p class="red">'+response[i].answer_msg_format+'</p>';
                    html += '</div>';
                }
                // 写入dom
                $('.news_list').html(html);
            });
        });

        // 提交按钮
        $('#submit').click(function() {
			// 表单form对象
			var fd = getFormData('wechat_answer');
			// 页面处理链接
			var url = "{{ route('wechat.wxanswer', ['id' => $wechat_message->id]) }}";
			// 页面跳转链接
			var jumpUrl = "{{ route('wechat.wxask', ['id' => $wechat_message->id]) }}";

            // 判断是否已经回复
            var answer_openid = "<?php echo $wechat_message->answer_openid; ?>";
            var id = "<?php echo $wechat_message->id; ?>";
			// 如果已经回复
            if (answer_openid) {
                //询问框
                layer.confirm('特别提示：您已经回复过该留言，确认发送会完全覆盖之前的回复并重新给用户推送，要继续吗？', {
                    btn: ['是','否'] //按钮
                }, function() {
					// 提交逻辑，后面设置1秒跳转
					ajax(url, fd, jumpUrl);
					// 禁止跳转并返回
					return false;
                }, function() {
                    // 点击否，什么都不做
                });
                // 返回
                return false;
            } else {
				// 提交逻辑，后面设置1秒跳转
				ajax(url, fd, jumpUrl);
				// 禁止跳转并返回
				return false;
			}
        });

		// 判断当前消息是否进行了回复
		function check_is_answered() {
			// 初始化
			var result = false;
			// 同步
			$.ajaxSetup({async:false});
			$.get('{{ route("wechat.wxcheckisanswer", ["id" => $wechat_message->id]) }}', function(response) {
				if (response.code == '1') {
					result = true;
				} else {
					result = false;
				}
			});
			// 返回
			return result;
		}
    </script>
	
</body>
</html>