<!-- 浮动按钮 Start -->
<!DOCTYPE HTML>
<html>

<head>
    <title>浮动按钮</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <script src="/static/js/jquery-1.7.2.min.js"></script>
    <style type="text/css">
        .floatImg {
            width: 58px;
            height: 58px;
        }
        
        #pulicBtn {
            width: 60px;
            z-index: 99999;
            position: fixed;
            right: 10px;
            bottom: 5px;
        }
    </style>
</head>

<body>
    <div id="pulicBtn">
    <img class="floatImg" src="/static/images/home.png" onclick="returnIndex()"/>
    <img class="floatImg" src="/static/images/left.png" onclick="window.history.go(-1)"/>
    </div>
</body>
<script type="text/javascript">
    var totalW = 0; //网页宽度
    var totalH = 0; //网页高度

    var divW = 0; //div宽度
    var divH = 0; //div高度

    $(function() {
        totalW = $(window).width();
        totalH = $(window).height();
        divW = $("#pulicBtn").width();
        divH = $("#pulicBtn").height();
    });

    $('#pulicBtn').on('touchmove', function(e) {
        // 阻止其他事件
        e.preventDefault();
        // 判断触摸点数
        if (e.originalEvent.targetTouches.length == 1) {
            var touch = e.originalEvent.targetTouches[0];

            var scrollTop = document.body.scrollTop == 0 ? document.documentElement.scrollTop : document.body.scrollTop;

            //拖拽
            $("#pulicBtn").css({
                'left': (touch.pageX - divW) + 'px',
                'top': (touch.pageY - scrollTop - divH) + 'px',
                'bottom': "auto"
            });

            //校正
            revice();
        }
    });

    //矫正
    function revice() {
        var top = $("#pulicBtn").get(0).offsetTop;
        var left = $("#pulicBtn").get(0).offsetLeft;

        if (top < 0) {
            $("#pulicBtn").css({
                'top': 0 + 'px'
            });
        }

        if (left < 0) {
            $("#pulicBtn").css({
                'left': 0 + 'px'
            });
        }
    }

    // 跳转到首页
    function returnIndex() {
        location.href = '{{ route("wxindex") }}';
    }
</script>

</html>
<!-- 浮动按钮 End -->