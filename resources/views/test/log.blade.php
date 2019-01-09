<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="/backend/js/jquery-1.12.3.min.js"></script>
</head>
<body>


<input type="button" class="inlineLeft marginRight" value="download log" onClick="demoDownloadLog()">

<input type="button" class="inlineLeft marginRight" value="get log" onClick="demoGetLog()" style="width:100px;">

<input type="button" class="inlineLeft marginRight" value="tail" onClick="demoTail()" style="width:100px;">

<section class="marginBottom">
    <div class="col full marginBottom marginTop">
        <h4 class="weight300">Console window:</h4>
        <div class="code-contain marginBottom lime" style="min-height:500px">
            <code id="codeVisualizer">// Use a method above and output will be displayed here</code>
        </div>
    </div>
    <input type="button" id="hqyzmBtni" class="getVerificationCode" value="获取验证码" class="hqyzmBtn" />
</section>


<script src="/static/js/debugout.js"></script>

<script>
    // 创建Debugout对象
    var bugout = new debugout();
    bugout.log('My name is liuzongyang');

    // 输出log
    function displayOutput(output) {
        // simulate console
        console.log(output);
        // format for html
        output = output.replace(/\n/g, '<br>').replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
        document.getElementById('codeVisualizer').innerHTML = output;
    }
    
    // 下载log
    function demoDownloadLog() {
        // var output = "bugout.downloadLog();";
        bugout.downloadLog();
        // displayOutput(output);
    }

    function demoGetLog() {
        var output = "bugout.getLog();\n";
        output += bugout.getLog();
        displayOutput(output);
    }

    // 获取验证码逻辑
    $('#hqyzmBtni').click(function() {
        // 发送验证码短信
        $.get("{{ route('wxcreatecode') }}", function(response) {
            // 打印验证码
            console.log(response);
        });
    });
</script>

</body>
</html>