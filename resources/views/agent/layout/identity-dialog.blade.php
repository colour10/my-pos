<!-- 未实名认证弹窗 Start -->
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/weui.css') }}">
    <div class="js_dialog" id="androidDialog1" style="display: none;">
        <div class="weui-mask" style="z-index: 10000;"></div>
        <div class="weui-dialog weui-skin_android" style="z-index: 99999;max-width: none;width: 80%;background-color: transparent;">
            <img id="tzBtn" style="width: 100%;" src="/static/images/apply.jpg">
            <img onclick="txHide()" style="height: 45px; margin-top: 20px;" src="/static/images/close.png">
        </div>
    </div>
    <!-- 未实名认证弹窗 End -->
