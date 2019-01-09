<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    
    <textarea id="editor_id" name="content" style="width:700px;height:300px;"></textarea>

    @include('kindeditor::editor',['editor'=>'editor_id'])

</body>
</html>