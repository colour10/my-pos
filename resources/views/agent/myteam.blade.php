<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">   
    <title>我的团队</title>
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/main.css') }}">
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/dropload.css') }}">
    <style>
        /* 新增css */
        .team_list_item span, .pullUpLabel {
            font-size: .64rem;
        }
        /* 补充css */
        #id .content {
            display:block;
        }
        .reflectList_item.content {
            display:none;
        }
        .content {
            text-indent:2em;
            height:auto;
        }
        .content span {
            width:95%;
            display:block;
            margin:0 auto;
            text-align:left;
            font-size:0.6rem;
            color:red;
            text-indent:0;
            padding-top: 0.8rem;
        }
    </style>
</head>
<body>
	<div class="team_list_item team_list_title">
	   		<span>手机号</span>
	   		<span>姓名</span>
	   		<span>关系</span>
	   		<!-- <span>激励金</span>
	   		<span>卡状态</span> -->
	   	</div>
	<div class="team_list"></div>
	<div id="wrapper">
		<div id="scroller">
			<ul id="thelist"></ul>
			<!-- <div id="pullUp">
				<span class="pullUpLabel">下拉加载更多</span>
			</div> -->
		</div>
	</div>
	
    @include('agent.layout.floatbtn')

   	<script src="/static/js/jquery.js"></script>
	<script src='/static/js/bootstrap.js'></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/jweixin-1.0.0.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/common.js') }}"></script>
	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
   	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
   	<script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/iscroll.js') }}"></script>
	<script type="text/javascript">

        // 赋值
        var openid = "{{ $user['id'] }}";

        // 调用数据
        getTeamData();

        // 获得数据列表
        function getTeamData() {
            $.post('{{ route("wxshowsingleagenttree") }}', {
                'openid': openid,
                '_token': "{{ csrf_token() }}",
            }, function(response) {
                console.log('---------- 打印合伙人列表 Start ----------');
                console.log(response);
                console.log('---------- 打印合伙人列表 End ----------');
                var len = response.length;
                // 判断是否为空，如果为空
                if (len == '0') {
                    var html = "<div class='data_null'><img src='/static/images/nodata.png' /></div>";
                    $("#thelist").append(html);
                    // document.getElementById("wrapper").style.height = '0';
                    // $("#pullUp").hide();
                    html = "";
                } else {
                    // 如果不为空则填充数据
                    var html = '';
                    var level_name = '';
                    for (var i=0; i<len; i++) {
                        html += '<div class="team_list_item" onclick="goshow(this);">';
                        html += '   <span>'+response[i].hide_mobile+'</span>';
                        html += '   <span>'+response[i].hide_name+'</span>';

                        // 修改等级名称
                        if (response[i].parentopenid == openid) {
                            level_name = '直接';
                        } else {
                            level_name = '间接';
                        }

                        html += '   <span class="item_state lineHeight status_name">'+level_name+'</span>';
                        // html += '   <div class="item_right lineHeight">';
                        // html += '       <img src="/static/images/you.png" />';
                        // html += '   </div>';
                        html += '</div>';
                        html += '<div class="reflectList_item content">';
                        html += '<span class="item_money lineHeight">注册时间：'+response[i].created_at+'<br>上级合伙人：'+response[i].hide_parent_mobile+'（'+response[i].hide_parent_name+'）</span>';
                        html += '</div>';
                    }
                    // 首先清空原来的dom
                    $("#thelist").empty();
                    // 写入dom
                    $("#thelist").append(html);
                }
            });
        }

        // 合伙人详情显示
        function goshow(indexNode) {
            var ns = indexNode.nextSibling;
            if (ns) {
                if (ns.style.display == 'block') {
                    ns.style.display = 'none';
                } else {
                    ns.style.display = 'block';
                }
            }
        }


        // var myScroll,pullDownEl, pullDownOffset,pullUpEl, pullUpOffset,generatedCount = 0;

        // function loaded() {
        //     //动画部分
        //     pullUpEl = document.getElementById('pullUp');
        //     pullUpOffset = pullUpEl.offsetHeight;
        //     myScroll = new iScroll('wrapper', {
        //         useTransition: true,
        //         topOffset: pullDownOffset,
        //         onRefresh: function () {
        //             if (pullUpEl.className.match('loading')) {
        //                 pullUpEl.className = '';
        //                 pullUpEl.querySelector('.pullUpLabel').innerHTML = '下拉加载更多';
        //             }
        //         },
        //         onScrollMove: function () {
                
        //             if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
        //                 pullUpEl.className = 'flip';
        //                 pullUpEl.querySelector('.pullUpLabel').innerHTML = '释放刷新';
        //                 this.maxScrollY = this.maxScrollY;
        //             } else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
        //                 pullUpEl.className = '';
        //                 pullUpEl.querySelector('.pullUpLabel').innerHTML = 'Pull up to load more...';
        //                 this.maxScrollY = pullUpOffset;
        //             }
        //         },
        //         onScrollEnd: function () {
        //             if (pullUpEl.className.match('flip')) {
        //                 pullUpEl.className = 'loading';
        //                 pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载中';				
        //                 pullUpAction();	// Execute custom function (ajax call?)
        //             }
        //         }
        //     });
            
        //     loadAction();
        // }
        // document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);//阻止冒泡
        // document.addEventListener('DOMContentLoaded', function () { setTimeout(loaded, 0); }, false);

        //初始状态，加载数据
        function loadAction() {
            getTeamData();
            myScroll.refresh();
        }

        // //下拉刷新当前数据
        // function pullDownAction () {
        //     setTimeout(function () {
        //         //这里执行刷新操作
        //         getTeamData();
        //         myScroll.refresh();	
        //     }, 400);
        // }

        // //上拉加载更多数据
        // function pullUpAction () {
        //     setTimeout(function () {
        //         getTeamData();
        //         myScroll.refresh();
        //     }, 400);
        // }
	</script>
</body>
</html>