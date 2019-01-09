<!-- 内容检索 Start -->
<div class="container-fluid">
    <div class="pull-left" style="width:60%;">
        <form action="{{ route('BankIndex') }}" method="get" id="form-search">
            <div class="select-main">
                <div class="form-select">
                    <label class="form-select-title" >开户行名称：</label>
                    <input type="text" class="form-control" placeholder="请输入开户行名称" name="name">
                </div>

                <div class="form-select"> 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-large btn-update">提交</button>
                </div>

                <div class="form-select"> 
                    &nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-large btn-default">重置</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：开户行不需要特别录入，用户在绑卡的时候会自动添加。
                </div>
            </div>
        </form>
    
    </div>
    <div class="pull-right text-right" style="width:40%">
        <a href="{{ route('BankCreate') }}" class="btn btn-large btn-add">新增开户行</a>
    </div>
</div>

<!-- 内容检索 End -->
