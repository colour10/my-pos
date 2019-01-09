<!-- 内容检索 Start -->
<div class="container-fluid">
    <div class="pull-left" style="width:60%;">
        <form action="{{ route('AccountIndex') }}" method="get" id="form-search">
            <div class="select-main">
                <div class="form-select">
                    <label class="form-select-title" >账户类型名称：</label>
                    <input type="text" class="form-control" placeholder="请输入账户类型名称" name="name">
                </div>

                <div class="form-select"> 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-large btn-update">提交</button>
                </div>

                <div class="form-select"> 
                    &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                </div>
            </div>
        </form>
    
    </div>
    <div class="pull-right text-right" style="width:40%">
        <a href="{{ route('AccountCreate') }}" class="btn btn-large btn-add">新增账户类型</a>
    </div>
</div>

<!-- 内容检索 End -->
