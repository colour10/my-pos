<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    @include('agent.layout.csrf')

    @include('agent.layout.write-openid')

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>用卡攻略</title>
    <link rel="stylesheet" href="{{ $controller->AutoVersion('/static/css/public.css') }}">
    <style>
        .strategy_img {
            width: 100%;
            height: auto;
        }
        .img-responsive {
            display: block;
            max-width: 100%;
            height: auto;
            margin-bottom: 1.5rem;
        }
        .center-block {
            display: block;
            margin-right: auto;
            margin-left: auto;
        }
    </style>
</head>

<body>

    <div id="strategy">
        <!-- <img class="strategy_img" src="/static/images/yongkagonglue.png" /> -->
        <P>很多朋友在申请信用卡的时候根本不明白银行到底审核了我们哪些个人信息，在没有完全准备好的情况下就去胡乱申请信用卡，有部分卡友申请信用卡被拒后，不知道问题出在哪里，也不去咨询申请不过的原因，以致于再申请还是没有通过。这个时候，就要警惕了，被拒次数多了，也会影响审批的。</P>
        <P>今天，意远就来说说信用卡初审的一些不为人知的事情。</P>
        <img src="/static/images/b1.png" class="img-responsive center-block">
        <P><strong>一、查询个人征信</strong></P>
        <P>提交信用卡申请后，会先看下人行给的整体评价，是中等偏上还是偏下，是良好A还是良好B。这算是印象分。如果遇到风控吃紧的时候，就可以不用仔细看其他评价，直接就筛选掉一批。</P>
        <img src="/static/images/b2.png" class="img-responsive center-block">
        <P>个人信用报告包括哪些内容?</P>
        <P>1、个人基本信息 (身份信息、配偶信息、居住信息、职业信息)</P>
        <P>2、信息概要 (信用提示、信用及违约信息概要、授信及负债信息概要)</P>
        <P>3、信贷交易信息明细 (资产处置信息、保证人代偿信息、贷款、借记卡)</P>
        <P>4、公共信息明细 (住房公积金参缴记录、养老保险金缴存记录</P>
        <P>5、查询记录 (查询记录汇总、非互联网查询记录明细)</P>
        <P><strong>二、内部评审系统进行评估</strong></P>
        <P>不仅仅只看我们个人征信系统里的记录，还有其他的内部评审系统和人行是相通的，里面可能评估到你更丰富的个人信息。最后，会给出一个结果，通过还是拒绝。主要包括</P>
        <P><strong>1、历史逾期情况</strong></P>
        <img src="/static/images/b3.png" class="img-responsive center-block">
        <P>逾期情况，会在征信上体现出来。贷款逾期会看征信上5年内的记录，信用卡逾期会看近2年的情况。所以，近两年信用卡使用情况良好，但是也覆盖不了之前的贷款逾期记录。如果之前贷款逾期严重，这也是难通过的原因。</P>
        <P><strong>2、征信查询次数</strong></P>
        <P>除了会查看征信上的逾期记录，征信查询次数也是要参考的。比如，近6个月人行查询次数&gt;=6或者&gt;=10等，达到这样的查询次数，算是较高的查询次数，如果其他资质是一般及以下，那么很可能因为这个查询次数遭拒。有的银行是近3个月“硬查询”&gt;=6次，申请就会直接被拒了。</P>
        <P><strong>3、信息真实情况</strong></P>
        <P>这一项如果出现问题，就是挑战银行的权威了，不被拒就是奇迹了。自己的基本信息、工作证明等都要如实填写。不过对于年收入这块儿，可以根据岗位性质适当写高点。比如，高级工程师岗位，年收入写20万，一点都不会夸张，即使你的年收入离20万还差点，工作性质在那里，不会被怀疑。但是如果直接写年收入100万，这可太假了，没有提供资产证明的话，那就有谎报的嫌疑了。</P>
        <P><strong>4、学历情况</strong></P>
        <P>有工作有固定的收入，你觉得学历就不重要了吗?要知道，学历也是个通行证。在校大学生没有工作、没有固定的收入，仅凭高学历就可以申请到一张信用卡，别管额度多少，至少证明学历是有用的。</P>
        <P><strong>三、持卡情况</strong></P>
        <P>之前已经有信用卡了，再申请的时候，就会注意你现在的持卡时间是大于6个月还是小于6个月。这个也许不是审核的重点，但是也是会参考的。要知道，使用时间越长，信用越好，越有利于银行进行参考。有卡历史也是最有力的信用凭证。</P>
        <P>最后建议，对自己征信情况不自信的，先自己查看下自己的征信，处理好逾期的信用卡和贷款，祝大家都可以申请到想要的信用卡，让我们一起愉快的玩耍吧。</P>
        <img src="/static/images/b4.png" class="img-responsive center-block">
        <P>信用卡申请通过之后会有面签的环节，网申首次下卡后需要到柜台面签，也有的银行需要先进行面签，面签成功之后才会邮寄卡片。(面签有两种，一种是先发卡后面签，另一种是先面签后发卡。)面签时记得带上本人身份证、信用卡、卡函，去柜台签字、拍照，以上程序走一遍基本上就OK了，当然去面签前一定要记住申请资料，以免面签不通过，浪费之前的努力。</P>
    </div>

    @include('agent.layout.floatbtn')

    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src='/static/js/bootstrap.js'></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/fastclick.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/rem.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/vue.js') }}"></script>
    <script type="text/javascript" src="{{ $controller->AutoVersion('/static/js/public.js') }}"></script>
</body>

</html>