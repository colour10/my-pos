@if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissible animated lightSpeedIn" role="alert" id="mynotice">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ $errors->first() }}&nbsp;&nbsp;<span>(<strong id="numDiv">5</strong>秒后自动关闭)</span>
        </div>
@endif
@if (session('success'))
    <div class="alert alert-success alert-dismissible animated lightSpeedIn" role="alert" id="mynotice">
        {{ session('success') }}&nbsp;&nbsp;<span>(<strong id="numDiv">5</strong>秒后自动关闭)</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif
@if (session('msg'))
    <div class="alert alert-info alert-dismissible animated lightSpeedIn" role="alert" id="mynotice">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('msg') }}&nbsp;&nbsp;<span>(<strong id="numDiv">5</strong>秒后自动关闭)</span>
    </div>
@endif
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible animated lightSpeedIn" role="alert" id="mynotice">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('warning') }}&nbsp;&nbsp;<span>(<strong id="numDiv">5</strong>秒后自动关闭)</span>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible animated lightSpeedIn" role="alert" id="mynotice">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('error') }}&nbsp;&nbsp;<span>(<strong id="numDiv">5</strong>秒后自动关闭)</span>
    </div>
@endif


<script type="text/javascript">
    var num = 5;
    var numDiv = document.getElementById('numDiv');
    var mynotice = document.getElementById('mynotice');
    if (mynotice && numDiv) {
        var interval = setInterval(function() {
        if(num == 0) {
            clearInterval(interval);
            mynotice.style.display = 'none';
        }
        numDiv.innerHTML = num--;
        },1000);
    }
</script>

