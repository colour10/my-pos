@extends('admin.layout.main')

<style type="text/css">
    /*right*/
    .container{background: #fff;margin-left:11.4%;  height:100%;width:88.6%;position: absolute;margin-top: 60px;    }
    .container-main{margin: 8px 20px 124px;}
    .container .breadcrumbs { border-bottom: 1px solid #E5E5E5;background-color: #F5F5F5;  line-height: 49px;  display: block;color: #4C8FBD;
        font-size: 13px;text-align: left;padding-left: 20px;margin-bottom: 40px;}
    .container .page-header-{ border-bottom: 1px dotted #E2E2E2;  padding-bottom: 16px;  padding-top: 7px;font-size: 16px;  margin: 8px 20px 24px;}
    .active{background: #3992D0;color:#fff;}
    .container-main h2 {
        font-size: 1.5em;
        font-weight: bold;
    }
    .container-main h3 {
        font-size: 1.17em;
        font-weight: bold;
    }
</style>

@section('content')
<div class="container-fluid head-table" style="margin-top:20px;">

    <div class="container-main" >
        <h3 id="agent_available_money">合伙人总余额：<span></span></h3>
        <h3 id="finance_available_money">代付通道余额：<span></span></h3>
        <hr style=" height:2px;border:none;border-top:2px dotted #185599;" />
        <h2>代付充值信息：</h2>
        <h3>通联支付备付金账户信息:</h3>
        <span>
            户名：通联支付网络服务股份有限公司客户备付金</br>
            账号：121907679110858</br>
            开户行：招商银行股份有限公司南京分行营业部</br>
            行号：308301006029</br>
            入账支持：支持本行、跨行入账</br>
            <p style="color:red">备注信息: 200110000008201</p>
        </span>
    </div>

</div>

<script type="text/javascript">
    // 初始化
    $(function() {

        // 请求接口，重写合伙人总金额
        $.get('{{ route("FinanceGetAgentsAccount") }}', function(response) {
            // 测试返回信息
            // console.log(response);
            // 执行写入
            $('#agent_available_money').children('span').eq(0).text(response + ' 元');
        });

        // 请求接口，账户信息
        $.get('{{ route("FinanceGetFinanceAccount") }}', function(response) {
            // 测试返回信息
            // console.log(response);
            // 判断是否通过了服务器验证
            // 如果没有通过就记为0
            if (response.AIPG.ACQUERYREP === '') {
                $('#finance_available_money').children('span').eq(0).text('0.00 元');
            } else {
                $('#finance_available_money').children('span').eq(0).text((response.AIPG.ACQUERYREP.ACNODE.BALANCE / 100).toFixed(2) + ' 元');
            }
        });
        
    });
</script>

@endsection
