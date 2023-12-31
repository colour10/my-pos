<!DOCTYPE HTML>
<html>

<head>
    <title>意远使用服务协议</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @include('agent.layout.csrf')

    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <style type="text/css">
        .btn2 {
            width: 120px;
            height: 30px;
            line-height: 30px;
            background: #0D7DF6;
            color: white;
            border: 0;
            text-align: center;
            border-radius: 3px;
        }
    </style>
    <script src="/static/js/jquery-1.7.2.min.js"></script>
    <script>
        var browser;
        $(function() {
            browser = {
                versions: function() {
                    var u = navigator.userAgent,
                        app = navigator.appVersion;
                    return { //移动终端浏览器版本信息
                        webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                        gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
                        mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                        ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                        android: u.indexOf('Android') > -1 ||
                            u.indexOf('Linux') > -1, //android终端或uc浏览器
                        qqbrowser: u.indexOf(" QQ") > -1, //qq内置浏览器
                    };
                }(),
                language: (navigator.browserLanguage || navigator.language)
                    .toLowerCase()
            };

            if (isWeiXin()) {
                if (browser.versions.ios) {
                    $("#head").css("background", "#1a1c20");
                } else if (browser.versions.android) {
                    $("#head").css("background", "#393a3e");
                }
            } else {
                $("#head").css("background", "#ffcc00");
                $("#pulicBtn").hide();
            }
        });

        function isWeiXin() {
            var ua = window.navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == 'micromessenger') {
                return true;
            } else {
                return false;
            }
        }
    </script>
</head>

<body style="margin:0 auto;font-size:13px;color:black;">
    <div id="head">
        <img style="display: block" src="/static/images/43f7d452-f153-4b3d-8e53-d3971560ac1a.png" width="100%">
    </div>

    <input type="hidden" id="yxTime" value="null" />
    <div style="width: 100%;max-width: 640px;margin: 0 auto">
        <div style="width: 100%;height: 30px;"></div>
        <h3 align="center">意远网站用户注册协议</h3>
        <div style="width: 100%;height: 30px;"></div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            请您在使用意远服务前仔细阅读本注册协议， 您只有完全同意所有注册协议，才能成为意远的用户（"用户"）并使用相应服务。您在注册为意远用户过程中点击"同意意远用户注册协议"按钮即表示您已仔细阅读并明确同意遵守本注册协议以及经参引而并入其中的所有条款、政策以及指南，并受该等规则的约束（合称"本注册协议"）。我们可能根据法律法规的要求或业务运营的需要，对本注册协议不时进行修改。除非另有规定，否则任何变更或修改将在修订内容于意远发布之时立即生效，您对意远的使用、继续使用将表明您接受此等变更或修改。如果您不同意本注册协议（包括我们可能不定时对其或其中引述的其他规则所进行的任何修改）的全部规定，则请勿使用意远提供的所有服务，或您可以主动取消意远提供的服务。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            为了便于您了解适用于您使用意远的条款和条件，我们将在意远上发布我们对本注册协议的修改，您应不时地审阅本注册协议以及经参引而并入其中的其他规则。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            一、服务内容
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            意远的具体服务内容由我们根据实际情况提供并不时更新，包括但不限于信息、图片、文章、评论、链接等，我们将定期或不定期根据用户的意愿以电子邮件、短信、电话或站内信等方式为用户提供活动信息，并向用户提供相应服务。我们对提供的服务拥有最终解释权。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            1.1 意远服务仅供个人用户使用。除我们书面同意，您或其他用户均不得将意远上的任何信息用于商业目的。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            1.2 您使用意远服务时所需的相关的设备以及网络资源等（如个人电脑及其他与接入互联网或移动网有关的装置）及所需的费用（如为接入互联网而支付的电话费及上网费）均由您自行负担。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            二、信息提供和隐私保护
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            您在访问、使用意远或申请使用意远服务时，必须提供本人真实的个人信息，且您应该根据实际变动情况及时更新个人信息。保护用户隐私是我们的重点原则，我们通过各种技术手段和强化内部管理等办法提供隐私保护服务功能，充分保护您的个人信息安全。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            2.1 意远不负责审核您提供的个人信息的真实性、准确性或完整性，因信息不真实、不准确或不完整而引起的任何问题及其后果，由您自行承担，且您应保证我们免受由此而产生的任何损害或责任。若我们发现您提供的个人信息是虚假、不准确或不完整的，我们有权自行决定终止向您提供服务。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            2.2 您已明确授权，为提供服务、履行协议、解决争议、保障交易安全等目的，我们对您提供的、我们自行收集的及通过第三方收集的您的个人信息、您申请服务时的相关信息、您在使用服务时储存在意远的非公开内容以及您的其他个人资料（以下简称“个人资料”）享有留存、整理加工、使用和披露的权利，具体方式包括但不限于：
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            2.3 （1）出于为您提供服务的需要在本网站公示您的个人资料；
            <br/>　　　　（2）由人工或自动程序对您的个人资料进行获取、评估、整理、存储；
            <br/>　　　　（3）使用您的个人资料以改进本网站的设计和推广；
            <br/>　　　　（4）使用您提供的联系方式与您联络并向您传递有关服务和管理方面的信息；
            <br/>　　　　（5）对您的个人资料进行分析整合并向为您提供服务的第三方提供为完成该项服务必要的信息。当为您提供服务的第三方与您电话核实信息时，为保证为您服务的质量，你同意意远对上述核实电话进行录音。
            <br/>　　　　（6）在您违反与我们或我们的其他用户签订的协议时，披露您的个人资料及违约事实，将您的违约信息写入黑名单并与必要的第三方共享数据，以供我们及第三方审核、追索之用。
            <br/>　　　　（7）其他必要的使用及披露您个人资料的情形。您已明确同意本条款不因您终止使用意远服务而失效。如因我们行使本条款项下权利使您遭受损失，我们对该等损失免责。
            <br/>　　　　（8）使用您的个人资料进行申请信用卡进度查询，收集您的个人资料与三方合作机构没有关系（银行、小贷机构等），由平台独立承担数据保密和使用的权利。当使用完查询申卡进度之后，平台将不再使用此条数据。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            为更好地为您提供服务，您同意并授权意远可与其合作的第三方进行联合研究，并可将通过本协议获得的您的信息投入到该等联合研究中。但意远与其合作的第三方在开展上述联合研究前，应要求其合作的第三方对在联合研究中所获取的您的信息予以保密
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            2.4　我们不会向与您无关的第三方恶意出售或免费提供您的个人资料，但下列情况除外：
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            2.5 （1）事先获得您的明确授权；
            <br/>　　　　（2）按照相关司法机构或政府主管部门的要求；
            <br/>　　　　（3）以维护我们合法权益之目的；
            <br/>　　　　（4）维护社会公众利益；
            <br/>　　　　（5）为了确保意远业务和系统的完整与操作。
            <br/>　　　　（6）符合其他合法要求。

        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            三、使用准则
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            您在使用意远服务过程中，必须遵循国家的相关法律法规，不通过意远发布、复制、上传、散播、分发、存储、创建或以其它方式公开含有以下内容的信息：
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            3.1 （1）反对宪法所确定的基本原则的；
            <br/>　　　　（2）危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；
            <br/>　　　　（3）损害国家荣誉和利益的；
            <br/>　　　　（4）煽动民族仇恨、民族歧视，破坏民族团结的；
            <br/>　　　　（5）破坏国家宗教政策，宣扬邪教和封建迷信的；
            <br/>　　　　（6）散布谣言，扰乱社会秩序，破坏社会稳定的；
            <br/>　　　　（7）散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的、欺诈性的或以其它令人反感的讯息、数据、信息、文本、音乐、声音、照片、图形、代码或其它材料；
            <br/>　　　　（8）侮辱或者诽谤他人，侵害他人合法权益的；
            <br/>　　　　（9）其他违反宪法和法律、行政法规或规章制度的；
            <br/>　　　　（10）可能侵犯他人的专利、商标、商业秘密、版权或其它知识产权或专有权利的内容；
            <br/>　　　　（11）假冒任何人或实体或以其它方式歪曲您与任何人或实体之关联性的内容；
            <br/>　　　　（12）未经请求而擅自提供的促销信息、政治活动、广告或意见征集；
            <br/>　　　　（13）任何第三方的私人信息，包括但不限于地址、电话号码、电子邮件地址、身份证号以及信用卡卡号；
            <br/>　　　　（14）病毒、不可靠数据或其它有害的、破坏性的或危害性的文件；
            <br/>　　　　（15）与内容所在的互动区域的话题不相关的内容；
            <br/>　　　　（16）依我们的自行判断，足以令人反感的内容，或者限制或妨碍他人使用或享受互动区域或意远的内容，或者可能使我们或我们关联方或其他用户遭致任何类型损害或责任的内容；
            <br/>　　　　（17）包含法律或行政法规禁止内容的其他内容。

        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            用户不得利用意远的服务从事下列危害互联网信息网络安全的活动：
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            3.2 （1）未经允许，进入计算机信息网络或者使用计算机信息网络资源；
            <br/>　　　　（2）未经允许，对计算机信息网络功能进行删除、修改或者增加；
            <br/>　　　　（3）未经允许，对进入计算机信息网络中存储、处理或者传输的数据和应用程序进行删除、修改或者增加；
            <br/>　　　　（4）故意制作、传播计算机病毒等破坏性程序；
            <br/>　　　　（5）其他危害计算机信息网络安全的行为。

        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            我们保留在任何时候为任何理由而不经通知地过滤、移除、筛查或编辑本网站上发布或存储的任何内容的权利，您须自行负责备份和替换在本网站发布或存储的任何内容，成本和费用自理。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            3.3 您须对自己在使用意远服务过程中的行为承担法律责任。若您为限制行为能力或无行为能力者，则您的法定监护人应承担相应的法律责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            3.4 如您的操作影响系统总体稳定性或完整性，我们将暂停或终止您的操作，直到相关问题得到解决。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            四、免责声明
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            意远是一个开放平台，用户将文章或照片等个人资料上传到互联网上，有可能会被其他组织或个人复制、转载、擅改或做其它非法用途，用户必须充分意识此类风险的存在。作为网络服务的提供者，我们对用户在任何论坛、个人主页或其它互动区域提供的任何陈述、声明或内容均不承担责任。您明确同意使用意远服务所存在的风险或产生的一切后果将完全由您自身承担，我们对上述风险或后果不承担任何责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            4.1您违反本注册协议、违反道德或法律的，侵犯他人权利（包括但不限于知识产权）的，我们不承担任何责任。同时，我们对任何第三方通过意远发送服务或包含在服务中的任何内容不承担责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            4.2 对您、其他用户或任何第三方发布、存储或上传的任何内容或由该等内容导致的任何损失或损害，我们不承担责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            4.3 对任何第三方通过意远可能对您造成的任何错误、中伤、诽谤、诬蔑、不作为、谬误、淫秽、色情或亵渎，我们不承担责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            4.4 对黑客行为、计算机病毒、或因您保管疏忽致使帐号、密码被他人非法使用、盗用、篡改的或丢失，或由于与本网站链接的其它网站所造成您个人资料的泄露，我们不承担责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            4.5如您发现任何非法使用用户帐号或安全漏洞的情况，请立即与我们联系。因任何非意远原因造成的网络服务中断或其他缺陷，我们不承担任何责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            4.6 我们不保证服务一定能满足您的要求；不保证服务不会中断，也不保证服务的及时性、安全性、准确性。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            4.7任何情况下，因使用意远而引起或与使用意远有关的而产生的由我们负担的责任总额，无论是基于合同、保证、侵权、产品责任、严格责任或其它理论，均不得超过您因访问或使用本网站而向意远支付的任何报酬（如果有的话）。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            4.8 意远提供免费的贷款推荐服务和推荐信用卡办卡服务，贷款、办信用卡过程中遇到的任何预先收费均为诈骗行为，请保持警惕避免损失。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            五、服务变更、中断或终止
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            如因升级的需要而需暂停网络服务、或调整服务内容，我们将尽可能在网站上进行通告。由于用户未能及时浏览通告而造成的损失，我们不承担任何责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            5.1 您明确同意，我们保留根据实际情况随时调整意远提供的服务内容、种类和形式，或自行决定授权第三方向您提供原本我们提供的服务。因业务调整给您或其他用户造成的损失，我们不承担任何责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            5.2同时，我们保留随时变更、中断或终止意远全部或部分服务的权利。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            发生下列任何一种情形，我们有权单方面中断或终止向您提供服务而无需通知您，且无需对您或第三方承担任何责任：
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            5.3（1）您提供的个人资料不真实；
            <br/>&nbsp;　　　（2）您违反本服务条款；
            <br/> 　　　（3）未经我们书面同意，将意远平台用于商业目的。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            您可随时通知我们终止向您提供服务或直接取消意远服务。自您终止或取消服务之日起，我们不再向您承担任何形式的责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            六、知识产权及其它权利
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            用户可以充分利用意远平台共享信息。您可以在意远发布从意远个人主页或其他网站复制的图片和信息等内容，但这些内容必须属于公共领域或者您拥有以上述使用方式使用该等内容的权利，且您有权对该等内容作出本条款下之授权、同意、认可或承诺。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            6.1 对您在意远发布或以其它方式传播的内容，您作如下声明和保证：
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            6.2（i）对于该等内容，您具有所有权或使用权；
            <br/>　　　（ii）该等内容是合法的、真实的、准确的、非误导性的；
            <br/>　　　（iii）使用和发布此等内容或以其它方式传播此等内容不违反本服务条款，也不侵犯任何人或实体的任何权利或造成对任何人或实体的伤害。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            未经相关内容权利人的事先书面同意，您不得擅自复制、传播在意远的该等内容，或将其用于任何商业目的，所有这些资料或资料的任何部分仅可作为个人或非商业用途而保存在某台计算机内。否则，我们及/或权利人将追究您的法律责任。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            6.3 您在意远发布或传播的自有内容或具有使用权的内容，您特此同意如下：
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            6.4（1）授予我们使用、复制、修改、改编、翻译、传播、发表此等内容，从此等内容创建派生作品，以及在全世界范围内通过任何媒介（现在已知的或今后发明的）公开展示和表演此等内容的权利；
            <br/>　　　 （2）授予意远及其关联方和再许可人一项权利，可依他们的选择而使用用户有关此等内容而提交的名称；
            <br/>　　　 （3）授予我们在第三方侵犯您在意远的权益、或您发布在意远的内容情况下，依法追究其责任的权利（但这并非我们的义务）；

        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            您在意远公开发布或传播的内容、图片等为非保密信息，我们没有义务将此等信息作为您的保密信息对待。在不限制前述规定的前提下，我们保留以适当的方式使用内容的权利，包括但不限于删除、编辑、更改、不予采纳或拒绝发布。我们无义务就您提交的内容而向您付款。一旦内容已在意远发布，我们也不保证向您提供对在意远发布内容进行编辑、删除或作其它修改的机会。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            6.5 如有权利人发现您在意远发表的内容侵犯其权利，并依相关法律、行政法规的规定向我们发出书面通知的，意远有权在不事先通知您的情况下自行移除相关内容，并依法保留相关数据。您同意不因该种移除行为向我们主张任何赔偿，如我们因此遭受任何损失，您应向赔偿我们的损失（包括但不限于赔偿各种费用及律师费）。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            6.6若您认为您发布第6.6条指向内容并未侵犯其他方的权利，您可以向我们以书面方式说明被移除内容不侵犯其他方权利的书面通知，该书面通知应包含如下内容：您详细的身份证明、住址、联系方式、您认为被移除内容不侵犯其他方权利的证明、被移除内容在意远上的位置以及书面通知内容的真实性声明。我们收到该书面通知后，有权决定是否恢复被移除内容。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            6.7 您特此同意，如果6.7条中的书面通知的陈述失实，您将承担由此造成的全部法律责任，如我们因此遭受任何损失，您应向赔偿我们的损失（包括但不限于赔偿各种费用及律师费）。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            七、推广宣传方面的约定：
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            7.1 任何注册会员推广人员不得在转介绍客户时向客户承诺一定可以下卡或承诺一定可以审批多少额度。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            7.2 不得以银行或意远合作方的名义进行任何的推广宣传，如需要以意远合作方的名义进行推广宣传，需提前报备，经书面同意方可进行。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            7.3 本次推广宣传仅限移动互联网、互联网线上推广宣传，在未经允许情况下不得进行任何市场推广宣传。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            7.4 完全知悉意远对网页物料享有完全的知识产权，保证未经同意，不得擅自更改合作银行制作的物料页面内容，亦不得把物料用于本约定之外的任何其他用途，不得有任何侵犯意远知识产权的行为。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            7.5 必须严格按照本协议约定的方式为合作银行宣传推广信用卡，其宣传渠道必须为推广人员自身朋友圈、微信好友、微信群、微博及移动端。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            7.6 不得恶意代替用户包装用户资料或指导用户提供虚假资料进行注册。 推广人员仅限推荐引流至平台，由用户自行申请办理。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            如有违反推广宣传方面的约定，意远将有权关闭账号同时保留追回以前有在意远平台获利的收入的权利。
        </div>

        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            八、特别约定
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            您使用本服务的行为若有任何违反国家法律法规或侵犯任何第三方的合法权益的情形时，我们有权直接删除该等违反规定之信息，并可以暂停或终止向您提供服务。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            8.1 若您利用意远服务从事任何违法或侵权行为，由您自行承担全部责任，因此给我们或任何第三方造成任何损失，您应负责全额赔偿，并使我们免受由此产生的任何损害。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            8.2 您同意我们通过重要页面的公告、通告、电子邮件以及常规信件的形式向您传送与意远服务有关的任何通知和通告。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            8.3 如您有任何有关与意远服务的个人信息保护相关投诉，请您与我们联系，我们将在接到投诉之日起15日内进行答复。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            8.4 本服务条款之效力、解释、执行均适用中华人民共和国法律。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            8.5 如就本协议内容或其执行发生任何争议，应尽量友好协商解决；协商不成时，任何一方均可向深圳市一收呗网络有限公司所在地的人民法院提起诉讼。
        </div>
        <div style="text-indent:2em;font-size:12px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            8.6 本服务条款中的标题仅为方便而设，不影响对于条款本身的解释。本服务条款最终解释权归一收呗网络有限公司所有。
        </div>
        <div style="width: 100%;height: 30px;"></div>
        <div style="text-indent:2em;font-size:16px;text-align:justify; text-justify:inter-ideograph;margin: 0 auto;width: 90%;color: #7B7B7B;line-height: 25px;margin-top: 5px;">
            请仔细阅读此协议，并完全同意协议内容，无异议。
        </div>
        <div style="width: 100%;height: 80px;"></div>

    </div>

    @include('agent.layout.floatbtn')

</body>

</html>