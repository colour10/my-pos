<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// 测试路由,测试网站基本功能
Route::group(['prefix' => '/test'], function () {
    Route::get('/index', 'Test\TestController@index');
    Route::get('/kindeditor', 'Test\TestController@kindeditor');
    Route::get('/md', 'Test\TestController@md');
    Route::get('/redis', 'Test\TestController@redis');
    Route::get('/ueditor', 'Test\TestController@ueditor');
    Route::get('/setsession', 'Test\TestController@setsession');
    Route::get('/getsession', 'Test\TestController@getsession');
    Route::get('/getca', 'Test\TestController@getca');
    Route::get('/authuser', 'Test\TestController@authuser');
    Route::get('/scout', 'Test\TestController@scout');
    Route::get('/passwd', 'Test\TestController@passwd');
    Route::get('/testsid', 'Test\TestController@testsid');
    Route::get('/getsid', 'Test\TestController@getsid');
    Route::get('/getagent', 'Test\TestController@getagent');
    Route::get('/update', 'Test\TestController@update');
    Route::get('/getone', 'Test\TestController@getone');
    Route::get('/permissions', 'Test\TestController@permissions');
    Route::get('/token', 'Test\TestController@token');
    Route::get('/logininfo', 'Test\TestController@logininfo');
    Route::get('/ifexists', 'Test\TestController@ifexists');
    Route::get('/{id}/getparent', 'Test\TestController@getparent');
    Route::get('/orm', 'Test\TestController@orm');
    Route::get('/error', 'Test\TestController@error');
    Route::get('/delete', 'Test\TestController@delete');
    Route::get('/card/{id}', 'Test\TestController@card');
    Route::get('/getstatus', 'Test\TestController@getstatus');
    Route::get('/getcashid', 'Test\TestController@getcashid');
    Route::get('/testapi', 'Test\TestController@testapi');
    Route::get('/freezes', 'Test\TestController@freezes');
    Route::get('/getopenid', 'Test\TestController@getOpenId');
    Route::get('/returnopenid', 'Test\TestController@returnOpenId');
    Route::get('/getsessionid', 'Test\TestController@getSessionId');
    Route::get('/sendmsg', 'Test\TestController@sendMsg');
    Route::get('/recurrence', 'Test\TestController@recurrence');
    Route::get('/getconfig', 'Test\TestController@getconfig');
    Route::get('/getip', 'Controller@getIP');
    Route::get('/redisincr', 'Test\TestController@redisincr');
    Route::get('/authpage', 'Test\TestController@authpage');
    Route::post('/is_permit_auth', 'Test\TestController@is_permit_auth');
    // 测试验证码
    Route::get('/logcode/captcha/{tmp}', 'Test\TestController@captcha');
    Route::get('/testcaptcha', 'Test\TestController@testcaptcha');
    Route::get('/logcode/getcaptcha', 'Test\TestController@getcaptcha');
    Route::post('/checkcaptcha', 'Test\TestController@checkcaptcha');
    // 记录log
    Route::get('/log', 'Test\TestController@log');
    // 发送邮件
    Route::get('/sendmail', 'Test\TestController@sendmail');
    // 自动导入
    Route::get('/insertagents', 'Test\TestController@insertagents');
    // 测试微信签名，是否ping通
    Route::get('/testwx', 'Test\TestController@testwx');
    // 文件自动添加版本号
    Route::get('/autoversion', 'Test\TestController@AutoVersion');
    // 测试文件加版本号
    Route::get('/testautoversion', 'Test\TestController@testautoversion');
    // 新增清除验证码缓存
    // 清除验证码
    Route::post('/removewxyzm', 'Test\TestController@removewxyzm')->name('testwxremovewxyzm');
    // 微信生成测试短信验证码
    Route::get('/createcode', 'Test\TestController@createcode')->name('testwxcreatecode');
    // 取出测试微信短信4位数验证码
    Route::get('/getwxcode', 'Test\TestController@getwxcode')->name('testwxgetwxcode');
    // 检查测试验证码是否正确
    Route::post('/checkregcode', 'Test\TestController@checkregcode')->name('testwxcheckregcode');
});



// 微信开发处理库
Route::any('/easywechat', 'WeChatController@serve');


// 正式接口
// 微信接口相关
Route::group(['prefix' => '/wechat'], function () {
    // 生成临时二维码
    Route::get('/createqrcode', 'WeChatController@createQrcode');
    // 获得二维码地址
    Route::get('/getqrcodeurl/{ticket}', 'WeChatController@getQrcodeUrl');
    // 生成二维码图片
    Route::get('/getqrcodecontent/{ticket}', 'WeChatController@getQrcodeContent');
    // 获取用户列表
    Route::get('/getusers', 'WeChatController@getUsers');
    // 获取单个用户信息（包含是否关注公众号）
    Route::post('/getuser', 'WeChatController@getUser');
    // 获取用户标签列表
    Route::get('/getusertags', 'WeChatController@getUserTags');
    // 长链接转短链接
    Route::get('/getshorten', 'WeChatController@getShorten');
    // 获取所有客服
    Route::get('/getservices', 'WeChatController@getServices');
    // 获取当前在线客服
    Route::get('/getonlineservices', 'WeChatController@getOnlineServices');
});


// 微信，必须拿到授权用户资料
Route::group(['middleware' => ['web', 'wechat.oauth']], function () {

    // 判断当前留言是否被管理员回复了
    Route::get('/wechat/{id}/checkisanswer', 'Agent\AgentauthController@wxcheckisanswer')->name('wechat.wxcheckisanswer');

    // 消息转发
    // 消息转发页面，在微信端，使用get传输，openid需要使用授权方式获取
    Route::get('/wechat/{id}', 'Agent\AgentauthController@wxask')->name('wechat.wxask');
    
    // 获取当前提问用户的消息列表
    Route::post('/wechat/list', 'Agent\AgentauthController@wechatmsgs')->name('wechat.wechatmsgs');

    // 消息转发逻辑，使用post传输，openid需要使用授权方式获取
    Route::post('/wechat/{id}', 'Agent\AgentauthController@wxanswer')->name('wechat.wxanswer');

});

// 正式路由
// 后台登录
// 添加默认登录
Route::get('/', 'Admin\PublicController@login')->name('defaultlogin');

// 标准登录页面
Route::get('/admin/login', 'Admin\PublicController@login')->name('login');

// 后台登录
Route::post('/admin/logindo', 'Admin\PublicController@logindo')->name('logindo');

// 后台退出
Route::get('/admin/logout', 'Admin\PublicController@logout')->name('logout');

// 微信登录验证码
Route::get('/wx/captcha/{tmp}', 'Agent\AgentauthController@captcha')->name('wxcaptcha');

// 微信取出验证码
Route::get('/wx/getcaptcha', 'Agent\AgentauthController@getcaptcha')->name('wxgetcaptcha');

// PC登录验证码
Route::get('/pc/captcha/{tmp}', 'Admin\PublicController@captcha')->name('pccaptcha');

// PC取出验证码
Route::get('/pc/getcaptcha', 'Admin\PublicController@getcaptcha')->name('pcgetcaptcha');

// 取出调整经办短信验证码
Route::get('/getfinancecode', 'Admin\FinanceController@getfinancecode')->name('getfinancecode');

// 微信访问不用任何权限
Route::group(['prefix' => '/agent/wx'], function () {

    // 微信生成短信验证码
    Route::get('/createcode', 'Agent\AgentauthController@createcode')->name('wxcreatecode');

    // 取出微信短信4位数验证码
    Route::get('/getwxcode', 'Agent\AgentauthController@getwxcode')->name('wxgetwxcode');

    // 微信发送短信接口
    Route::post('/sendmsg', 'Agent\AgentauthController@sendMsg')->name('wxsendmsg');

    // 显示所有合伙人的结果，接口，PC端和微信端都可以调用
    Route::get('/v1/getagents', 'Agent\AgentauthController@getagents')->name('wxgetagents');

    // 显示格式化合伙人后的结果，接口，PC端和微信端都可以调用
    Route::get('/v1/showtreeagents', 'Agent\AgentauthController@showtreeagents')->name('wxshowtreeagents');

    // 显示单个格式化合伙人后的结果，接口，PC端和微信端都可以调用
    Route::post('/v1/showsingleagenttree', 'Agent\AgentauthController@showSingleAgentTree')->name('wxshowsingleagenttree');

    // 显示信用卡推广的下线-团队列表
    Route::post('/v1/getmyteam', 'Agent\AgentauthController@wxgetmyteam')->name('wxgetmyteam');

    // 显示信用卡推广的下线-申请卡的总人数
    Route::post('/v1/getteamsumagent', 'Agent\AgentauthController@wxgetteamsumagent')->name('wxgetteamsumagent');

    // 记录前端日志
    Route::post('/savelog', 'Agent\AgentauthController@wxsavelog')->name('wxsavelog');

    // 获取银行列表 【缓存】 【测试】
    Route::get('/getcardboxeslist', 'Agent\AgentauthController@getCardboxesList')->name('wxgetcardboxeslist');

    // 获取银行列表 【缓存】 【测试】
    Route::get('/getcardboxes', 'Agent\AgentauthController@getCardboxesCache')->name('wxgetcardboxescache'); 

    // 生成合伙人查询缓存
    Route::get('/createagentcache/{openid}', 'Agent\AgentauthController@createAgentCache')->name('wxcreateagentcache');
});

// 合伙人登录逻辑（PC端）
Route::group(['middleware' => ['agent.auth'], 'prefix' => '/agent'], function () {

    // 首页，禁止访问，因为微信端作为入口了
    // Route::get('/', 'Agent\AgentauthController@index')->name('AgentauthIndex');

    // 判断是否登录（pc）
    Route::get('/islogin', 'Agent\AgentauthController@islogin')->name('AgentauthIslogin');

    // 添加银行卡
    Route::get('/addcard', 'Agent\AgentauthController@addcard')->name('addcard');

    // // 检查用户输入的和数据库录入的姓名是否一致
    // Route::post('/checkuser', 'Agent\AgentauthController@checkuser')->name('AgentauthCheckuser');

    // 添加银行卡逻辑
    Route::post('/addcardstore', 'Agent\AgentauthController@addcardstore')->name('addcardstore');

    // // 微信提现【必须在微信端操作】
    // Route::post('/cash', 'Agent\AgentauthController@cash')->name('AgentauthCash');

    // 银行卡列表逻辑
    Route::get('/cards', 'Agent\AgentauthController@cards')->name('AgentauthCards');

    // 单张银行卡接口，单页使用
    Route::get('/cards/{id}', 'Agent\AgentauthController@cardinfo')->name('AgentauthCardinfo');

    // 修改银行卡逻辑 [PC]
    Route::put('/cards/{id}', 'Agent\AgentauthController@updatecard')->name('Agentauthupdatecard');

    // // 提现记录列表【必须登录】
    // Route::post('/withdraws', 'Agent\AgentauthController@withdraws')->name('AgentauthWithdraws');
    
    // // 当前余额【必须登录】
    // Route::get('/available', 'Agent\AgentauthController@available')->name('AgentauthAvailable');

    // 退出，禁止退出
    // Route::get('/logout', 'Agent\AgentauthController@logout')->name('AgentauthLogout');

});





// 微信访问页面路由,必须通过微信访问
Route::group(['middleware' => ['wx.auth'], 'prefix' => '/agent/wx'], function () {

    // 微信登录相关逻辑
    // 检查注册验证码是否正确
    Route::post('/checkregcode', 'Agent\AgentauthController@checkregcode')->name('wxcheckregcode');

    // 检查登录验证码是否正确
    Route::post('/checklogincode', 'Agent\AgentauthController@checklogincode')->name('wxchecklogincode');

    // 获取openid
    Route::get('/getopenid', 'Agent\AgentauthController@getOpenId')->name('wxgetOpenId');

    // 微信拉取用户信息-发起授权页
    Route::get('/getuserinfo', 'Agent\AgentauthController@getUserInfo')->name('wxgetuserinfo');

    // 微信获取AccessToken
    Route::get('/getaccesstoken', 'Agent\AgentauthController@getAccessToken')->name('wxgetaccesstoken');

    // 我-未登录之前，根据openid，查询
    Route::get('/{openid}/mybyopenid', 'Agent\AgentauthController@wxmybyopenid')->name('wxmybyopenid');

    // 我-未登录之前，根据openid，查询，post接口
    Route::post('/wxpostbyopenid', 'Agent\AgentauthController@wxpostbyopenid')->name('wxpostbyopenid');

    // 删除银行卡逻辑
    Route::delete('/{id}', 'Agent\AgentauthController@wxcarddelete')->name('wxcarddelete');

    // 判断手机号是否被注册
    Route::post('/isreg', 'Agent\AgentauthController@wxisreg')->name('wxisreg');

    // 新增清除验证码缓存
    // 清除验证码
    Route::post('/removewxyzm', 'Agent\AgentauthController@removewxyzm')->name('wxremovewxyzm');  
});




// 微信，必须拿到授权用户资料
Route::group(['middleware' => ['web', 'wechat.oauth'], 'prefix' => '/agent/wx'], function () {

    // 获得用户授权资料  [接口]
    Route::get('/getauthuser', 'Agent\AgentauthController@getauthuser')->name('wxgetauthuser');

    // 取出授权的合伙人模型 [接口]
    Route::post('/checkbyopenid', 'Agent\AgentauthController@wxcheckbyopenid')->name('wxcheckbyopenid');    

    // 首页 [模板]
    Route::get('/', 'Agent\AgentauthController@wxindex')->name('wxindex');

    // 当前合伙人的下线 [模板]
    Route::get('/myteam', 'Agent\AgentauthController@myteam')->name('wxmyteam');

    // 当前合伙人下线人数 [接口]
    Route::post('/myteamapi', 'Agent\AgentauthController@wxmyteamapi')->name('wxmyteamapi');

    // 账户详情 [模板]
    Route::get('/mysum', 'Agent\AgentauthController@wxmysum')->name('wxmysum');

    // 我的默认首页 [模板]
    Route::get('/mine', 'Agent\AgentauthController@wxmine')->name('wxmine');

    // 我-账户信息 [接口]
    Route::post('/my', 'Agent\AgentauthController@wxmy')->name('wxmy');

    // 取出微信公众号所有设置参数
    Route::post('/config', 'Agent\AgentauthController@getWxConfig')->name('wxgetwxconfig');

    // 微信jssdk [接口]
    Route::post('/getsignpackage', 'Agent\AgentauthController@getSignPackage')->name('wxgetsignpackage');

    // 邀请 [模板]
    Route::get('/invitation', 'Agent\AgentauthController@wxinvitation')->name('wxinvitation');    

    // 分享 [模板]
    Route::get('/share', 'Agent\AgentauthController@wxshare')->name('wxshare');

    // 申请办卡 [模板]
    Route::get('/applycard/{bankid}', 'Agent\AgentauthController@wxapplycard')->name('wxapplycard');

    // 进度查询
    Route::get('/progress', 'Agent\AgentauthController@wxprogress')->name('wxprogress');

    // 我的订单
    Route::get('/order', 'Agent\AgentauthController@wxorder')->name('wxorder');
    
    // 审核中订单列表 [接口]
    Route::post('/revieworders', 'Agent\AgentauthController@wxrevieworders')->name('wxrevieworders');

    // 已完成订单列表 [接口]
    Route::post('/finishorders', 'Agent\AgentauthController@wxfinishorders')->name('wxfinishorders');

    // 新增实名认证逻辑
    // 信用卡介绍
    Route::get('/cardinfo/{bankid}', 'Agent\AgentauthController@wxcardinfo')->name('wxcardinfo');

    // 实名认证类 [模板]
    Route::get('/identityforreal', 'Agent\AgentauthController@wxidentityforreal')->name('wxidentityforreal');
    
    // 实名认证类【逻辑】
    Route::post('/identityforrealstore', 'Agent\AgentauthController@wxidentityforrealstore')->name('wxidentityforrealstore');

    // 判断当前手机能否被使用 [接口]
    Route::post('/checkmobilevalid', 'Agent\AgentauthController@wxcheckmobilevalid')->name('wxcheckmobilevalid');

    // 判断当前身份证能否被使用 [接口]
    Route::post('/checkidnumbervalid', 'Agent\AgentauthController@wxcheckidnumbervalid')->name('wxcheckidnumbervalid');

    // 意远平台服务协议 [模板]
    Route::get('/agreement', 'Agent\AgentauthController@wxagreement')->name('wxagreement');

    // 修改合伙人手机号逻辑 [接口]
    Route::post('/modifymobile', 'Agent\AgentauthController@wxmodifymobile')->name('wxmodifymobile');

    // 添加银行卡 [模板]
    Route::get('/wxaddcard', 'Agent\AgentauthController@wxaddcard')->name('wxaddcard');

    // 添加银行卡逻辑 [接口]
    Route::post('/wxaddcardstore', 'Agent\AgentauthController@wxaddcardstore')->name('wxaddcardstore');

    // 编辑银行卡 [模板]
    Route::get('/{id}/wxeditcard', 'Agent\AgentauthController@wxeditcard')->name('wxeditcard');

    // 编辑银行卡逻辑 [接口]
    Route::put('/{id}', 'Agent\AgentauthController@wxupdatecard')->name('wxupdatecard');

    // 我的银行卡 [模板]
    Route::get('/rankcard', 'Agent\AgentauthController@wxrankcard')->name('wxrankcard');

    // 检查用户输入的和数据库录入的姓名是否一致 [接口]
    Route::post('/checkuser', 'Agent\AgentauthController@checkuser')->name('AgentauthCheckuser');

    // 提现明细 [模板]
    Route::get('/withdraw', 'Agent\AgentauthController@wxwithdraw')->name('wxwithdraw');

    // 申请提现页面 [模板]
    Route::get('/drawcash', 'Agent\AgentauthController@wxdrawcash')->name('wxdrawcash');

    // 重置提现密码
    Route::get('/{id}/resetpwd', 'Agent\AgentauthController@wxresetpwd')->name('wxresetpwd');

    // 重置提现密码逻辑
    Route::put('/{id}/updateresetpwd', 'Agent\AgentauthController@wxupdateresetpwd')->name('wxupdateresetpwd');

    // 分润调账明细 [模板]
    Route::get('/incentivedetail', 'Agent\AgentauthController@wxincentivedetail')->name('wxincentivedetail');

    // 我的信息 [模板]
    Route::get('/message', 'Agent\AgentauthController@wxmessage')->name('wxmessage');

    // 设置提现密码 [模板]
    Route::get('/{id}/setpwd', 'Agent\AgentauthController@wxsetpwd')->name('wxsetpwd');

    // 设置提现密码逻辑 [接口]
    Route::put('/{id}/setpwd', 'Agent\AgentauthController@wxstorepwd')->name('wxstorepwd');

    // 修改提现密码 [模板]
    Route::get('/{id}/modifypwd', 'Agent\AgentauthController@wxmodifypwd')->name('wxmodifypwd');

    // 修改提现密码逻辑 [接口]
    Route::put('/{id}/updatepwd', 'Agent\AgentauthController@wxupdatepwd')->name('wxupdatepwd');

    // 我的银行卡(接口)
    Route::post('/cards', 'Agent\AgentauthController@wxcards')->name('wxcards');

    // 我的银行卡-当前唯一卡(接口)
    Route::post('/firstcard', 'Agent\AgentauthController@wxfirstcard')->name('wxfirstcard');

    // 我的客服
    Route::get('/customerService', 'Agent\AgentauthController@wxcustomerService')->name('wxcustomerService');

    // 设置
    Route::get('/setting', 'Agent\AgentauthController@wxsetting')->name('wxsetting');

    // 提现记录列表【必须登录】
    Route::post('/withdraws', 'Agent\AgentauthController@withdraws')->name('AgentauthWithdraws');
    
    // 当前余额【必须登录】
    Route::get('/available', 'Agent\AgentauthController@available')->name('AgentauthAvailable');

    // 微信提现【必须在微信端操作】
    Route::post('/cash', 'Agent\AgentauthController@cash')->name('AgentauthCash');

    // 提现记录
    Route::get('/list', 'Agent\AgentauthController@wxlist')->name('wxlist');

    // 推荐银行列表【接口】
    Route::post('/cardboxes', 'Agent\AgentauthController@cardboxes')->name('wxcardboxes');

    // 当前用户的激励金明细
    Route::post('/getincent', 'Agent\AgentauthController@getincent')->name('wxgetincent');

    // 办卡须知
    Route::get('/strategy', 'Agent\AgentauthController@wxstrategy')->name('wxstrategy');

    // 用户是否已经申请了该信用卡
    Route::post('/iscardapply', 'Agent\AgentauthController@wxiscardapply')->name('wxiscardapply');

    // 申请办卡-逻辑
    Route::post('/applycardstore', 'Agent\AgentauthController@wxapplycardstore')->name('wxapplycardstore');

    // 银行卡四要素认证
    Route::post('/checkbankcard', 'Agent\AgentauthController@checkbankcard')->name('wxcheckbankcard');

    // 是否实名认证
    Route::post('/wxisreal', 'Agent\AgentauthController@wxisreal')->name('wxisreal');

    // 实名认证
    Route::post('/authentication', 'Agent\AgentauthController@wxauthentication')->name('wxauthentication');

});








// 微信，必须拿到授权用户资料，TestController [测试类]
Route::group(['middleware' => ['web', 'wechat.oauth'], 'prefix' => '/test/wx'], function () {

    // 获得用户授权资料
    Route::get('/getauthuser', 'Test\TestController@getauthuser')->name('testwxgetauthuser');

    // 测试controller接口类 [测试]
    // 微信合伙人首页 [测试]
    Route::get('/', 'Test\TestController@wxindex')->name('testwxindex');

    // 当前合伙人的下线
    Route::get('/myteam', 'Test\TestController@wxmyteam')->name('testwxmyteam');

    // 显示信用卡推广的下线-团队列表
    Route::post('/getmyteam', 'Test\TestController@wxgetmyteam')->name('testwxgetmyteam');
    
    // 微信推送消息
    Route::post('/sendwxmsg', 'Test\TestController@sendwxmsg')->name('testwxsendwxmsg');

    // 申请办卡
    Route::get('/applycard/{bankid}', 'Test\TestController@wxapplycard')->name('testwxapplycard');

    // 申请办卡-逻辑
    Route::post('/applycardstore', 'Test\TestController@wxapplycardstore')->name('testwxapplycardstore');

    // 当前用户的激励金明细
    Route::post('/getincent', 'Test\TestController@wxgetincent')->name('testwxgetincent');

    // 分润调账明细
    Route::get('/incentivedetail', 'Test\TestController@wxincentivedetail')->name('testwxincentivedetail');

    // 账户详情
    Route::get('/mysum', 'Test\TestController@wxmysum')->name('testwxmysum');

    // 我的默认首页
    Route::get('/mine', 'Test\TestController@wxmine')->name('testwxmine');

    // 设置
    Route::get('/setting', 'Test\TestController@wxsetting')->name('testwxsetting');

    // 我的客服
    Route::get('/customerService', 'Test\TestController@wxcustomerService')->name('testwxcustomerService');

    // 邀请
    Route::get('/invitation', 'Test\TestController@wxinvitation')->name('testwxinvitation');

    // 我的银行卡(模板)
    Route::get('/rankcard', 'Test\TestController@wxrankcard')->name('testwxrankcard');

    // 我的信息
    Route::get('/message', 'Test\TestController@wxmessage')->name('testwxmessage');

    // 我的订单
    Route::get('/order', 'Test\TestController@wxorder')->name('testwxorder');

    // 进度查询
    Route::get('/progress', 'Test\TestController@wxprogress')->name('testwxprogress');

    // 登录页面
    Route::get('/login', 'Test\TestController@wxlogin')->name('testwxlogin');

    // 是否实名认证
    Route::post('/wxisreal', 'Test\TestController@wxisreal')->name('testwxisreal');

    // 判断当前授权用户是否为合伙人
    Route::post('/checkbyopenid', 'Test\TestController@wxcheckbyopenid')->name('testwxcheckbyopenid');

    // 信用卡介绍
    Route::get('/cardinfo/{bankid}', 'Test\TestController@wxcardinfo')->name('testwxcardinfo');

    // 实名认证类
    Route::get('/identityforreal', 'Test\TestController@wxidentityforreal')->name('testwxidentityforreal');
    
    // 实名认证类【逻辑】
    Route::post('/identityforrealstore', 'Test\TestController@wxidentityforrealstore')->name('testwxidentityforrealstore');

    // 判断当前手机能否被使用
    Route::post('/checkmobilevalid', 'Test\TestController@wxcheckmobilevalid')->name('testwxcheckmobilevalid');

    // 判断当前身份证能否被使用
    Route::post('/checkidnumbervalid', 'Test\TestController@wxcheckidnumbervalid')->name('testwxcheckidnumbervalid');

    // 意远平台服务协议
    Route::get('/agreement', 'Test\TestController@wxagreement')->name('testwxagreement');

    // 修改合伙人手机号逻辑
    Route::post('/modifymobile', 'Test\TestController@wxmodifymobile')->name('testwxmodifymobile');

    // 添加银行卡
    Route::get('/wxaddcard', 'Test\TestController@wxaddcard')->name('testwxaddcard');

    // 银行卡四要素认证
    Route::post('/checkbankcard', 'Test\TestController@checkbankcard')->name('testwxcheckbankcard');

    // 添加银行卡逻辑
    Route::post('/wxaddcardstore', 'Test\TestController@wxaddcardstore')->name('testwxaddcardstore');

});







// Admin后台模板所有功能
Route::group(['middleware' => ['manager.auth', 'permission.control'], 'prefix' => '/admin'], function () {

    // 合伙人搜索
    Route::get('/agents/search', 'Admin\AgentController@search')->name('agents.search');

    // 合伙人审核通过
    Route::post('/agents/review/{id}/successed', 'Admin\AgentController@reviewsuccessed')->name('agents.review.successed');

    // 合伙人审核不通过
    Route::post('/agents/review/{id}/failed', 'Admin\AgentController@reviewfailed')->name('agents.review.failed');

    // 检查合伙人输入的卡号
    Route::get('/agents/checkcard/{card}', 'Admin\AgentController@checkcard')->name('agents.checkcard');

    // 合伙人复核审核通过逻辑(多条)
    Route::post('/agents/multisuccessed', 'Admin\AgentController@multisuccessed')->name('agents.multi.successed');

    // 合伙人复核审核不通过逻辑(多条)
    Route::post('/agents/multifailed', 'Admin\AgentController@multifailed')->name('agents.multi.failed');

    // 合伙人管理
    Route::resource('agents', 'Admin\AgentController');

});

// 分润管理
Route::group(['middleware' => ['manager.auth', 'permission.control'], 'prefix' => '/admin/benefit'], function () {

    // 分润明细查询
    Route::get('/info', 'Admin\BenefitController@info')->name('BenefitInfo');

    // 分润提现记录
    Route::get('/withdraw', 'Admin\BenefitController@withdraw')->name('BenefitWithdraw');

    // 分润提现记录
    Route::get('/withdrawsearch', 'Admin\BenefitController@withdrawsearch')->name('BenefitWithdrawsearch');

    // 分润余额查询
    Route::get('/balance', 'Admin\BenefitController@balance')->name('BenefitBalance');

    // 数据导出excel
    Route::get('/export', 'Admin\BenefitController@export')->name('BenefiteExport');

});

// 财务处理
Route::group(['middleware' => ['manager.auth', 'permission.control'], 'prefix' => '/admin/finance'], function () {

    // 账户信息
    Route::get('/show', 'Admin\FinanceController@show')->name('FinanceShow');

    // 资金冻结
    Route::get('/freeze', 'Admin\FinanceController@freeze')->name('FinanceFreeze');

    // 资金冻结新增逻辑
    Route::post('/freeze', 'Admin\FinanceController@freezestore')->name('FinanceFreezestore');

    // 分润制单
    Route::get('/benefitbill', 'Admin\FinanceController@benefitbill')->name('FinanceBenefitbill');

    // 调账经办
    Route::get('/transactor', 'Admin\FinanceController@transactor')->name('FinanceTransactor');

    // 调账经办(批量)
    Route::get('/transactors', 'Admin\FinanceController@transactors')->name('FinanceTransactors');

    // 调账经办(批量)-逻辑
    Route::post('/transactors', 'Admin\FinanceController@transactorsstore')->name('FinanceTransactorsstore');

    // 调账经办新增逻辑
    Route::post('/transactor', 'Admin\FinanceController@transactorstore')->name('FinanceTransactorstore');

    // 调账查询
    Route::get('/transactquery', 'Admin\FinanceController@transactquery')->name('FinanceTransactquery');

    // 分润复核
    Route::get('/benefitcheck', 'Admin\FinanceController@benefitcheck')->name('FinanceBenefitcheck');

    // 分润复核审核通过逻辑(单条)
    Route::post('/benefitcheck/{id}/successed', 'Admin\FinanceController@benefitchecksuccessed')->name('FinanceBenefitchecksuccessed');

    // 分润复核审核不通过逻辑(单条)
    Route::post('/benefitcheck/{id}/failed', 'Admin\FinanceController@benefitcheckfailed')->name('FinanceBenefitcheckfailed');

    // 分润复核审核通过逻辑(多条)
    Route::post('/benefitchecks/successed', 'Admin\FinanceController@benefitcheckssuccessed')->name('FinanceBenefitcheckssuccessed');

    // 分润复核审核不通过逻辑(多条)
    Route::post('/benefitchecks/failed', 'Admin\FinanceController@benefitchecksfailed')->name('FinanceBenefitchecksfailed');

    // 批量经办模板下载
    // Route::get('/download',function() {
    //     return response()->download(realpath(base_path('public')).'/backend/file/transactor.xls', "批量经办模板".'.xls');
    // })->name('FinanceDownload');
    Route::get('/download', 'Admin\FinanceController@download')->name('FinanceDownload');

    // 合伙人汇总账户总金额-接口
    Route::get('/getagentsaccount', 'Admin\FinanceController@getagentsaccount')->name('FinanceGetAgentsAccount');

    // 通联备付金账户余额-接口
    Route::get('/getfinanceaccount', 'Admin\FinanceController@getfinanceaccount')->name('FinanceGetFinanceAccount');

    // 发送短信接口
    Route::post('/sendmsg', 'Admin\FinanceController@sendMsg')->name('FinanceSendMsg');

    // 调账记录数据导出excel
    Route::get('/export', 'Admin\FinanceController@export')->name('FinanceExport');

    // 生成验证码接口
    Route::get('/createcode', 'Admin\FinanceController@createcode')->name('FinanceCreatecode');

});

// 代付管理
Route::group(['middleware' => ['manager.auth', 'permission.control'], 'prefix' => '/admin/advance'], function () {

    // 代付通道管理
    Route::get('/method', 'Admin\AdvanceController@method')->name('AdvanceMethod');

    // 代付通道添加
    Route::get('/method/create', 'Admin\AdvanceController@methodcreate')->name('AdvanceMethodcreate');

    // 代付通道添加逻辑
    Route::post('/method/store', 'Admin\AdvanceController@methodstore')->name('AdvanceMethodstore');

    // 代付通道修改
    Route::get('/method/{id}/edit', 'Admin\AdvanceController@methodedit')->name('AdvanceMethodedit');

    // 代付通道修改逻辑
    Route::put('/method/{id}/update', 'Admin\AdvanceController@methodupdate')->name('AdvanceMethodupdate');

    // 代付通道删除
    Route::delete('/method/{id}', 'Admin\AdvanceController@methoddestroy')->name('AdvanceMethoddestroy');

    // 代付记录
    Route::get('/list', 'Admin\AdvanceController@list')->name('AdvanceList');

    // 代付记录搜索
    Route::get('/search', 'Admin\AdvanceController@search')->name('AdvanceSearch');

    // 代付账户充值
    Route::get('/recharge', 'Admin\AdvanceController@recharge')->name('AdvanceRecharge');

    // 数据导出excel
    Route::get('/export', 'Admin\AdvanceController@export')->name('AdvanceExport');

});

// 系统设置
Route::group(['middleware' => ['manager.auth', 'permission.control'], 'prefix' => '/admin/system'], function () {

    // 后台首页
    Route::get('/index', 'Admin\SystemController@index')->name('index');


    // 员工模块-管理员
    // 管理员列表
    Route::get('/manager', 'Admin\SystemController@managerindex')->name('ManagerIndex'); 

    Route::get('/manager/search', 'Admin\SystemController@managersearch')->name('ManagerSearch');

    // 管理员添加页面
    Route::get('/manager/create', 'Admin\SystemController@managercreate')->name('ManagerCreate'); 

    // 管理员添加逻辑
    Route::post('/manager/store', 'Admin\SystemController@managerstore')->name('ManagerStore'); 

    // 管理员修改
    Route::get('/manager/{id}/edit', 'Admin\SystemController@manageredit')->name('ManagerEdit'); 

    // 管理员修改逻辑
    Route::put('/manager/{id}/update', 'Admin\SystemController@managerupdate')->name('ManagerUpdate'); 

    // 管理员显示
    Route::get('/manager/{id}', 'Admin\SystemController@managershow')->name('ManagerShow'); 

    // 管理员删除
    Route::delete('/manager/{id}', 'Admin\SystemController@managerdestroy')->name('ManagerDestroy'); 

    // 管理员分配角色页面
    Route::get('/manager/{id}/role', 'Admin\SystemController@managerrole')->name('ManagerRole');

    // 管理员赋予角色
    Route::put('/manager/{id}/assignrole', 'Admin\SystemController@managerassignRole')->name('ManagerAssignrole');



    // 银行开户行管理
    // 银行开户行列表
    Route::get('/bank', 'Admin\SystemController@bankindex')->name('BankIndex');

    // 银行开户行添加页面
    Route::get('/bank/create', 'Admin\SystemController@bankcreate')->name('BankCreate');

    // 银行开户行添加逻辑
    Route::post('/bank/store', 'Admin\SystemController@bankstore')->name('BankStore');

    // 银行开户行修改
    Route::get('/bank/{id}/edit', 'Admin\SystemController@bankedit')->name('BankEdit');

    // 银行开户行修改逻辑
    Route::put('/bank/{id}/update', 'Admin\SystemController@bankupdate')->name('BankUpdate');

    // 银行开户行显示
    Route::get('/bank/{id}', 'Admin\SystemController@bankshow')->name('BankShow');

    // 银行开户行删除
    Route::delete('/bank/{id}', 'Admin\SystemController@bankdestroy')->name('BankDestroy');

    // 银行开户行搜索
    // Route::get('/bank', 'Admin\SystemController@bankindex')->name('bankindex'); 




    // 角色管理模块
    // 添加角色
    Route::get('/role/create', 'Admin\SystemController@rolecreate')->name('RoleCreate');
    // 添加角色逻辑
    Route::post('/role/store', 'Admin\SystemController@rolestore')->name('RoleStore');
    // 角色管理
    Route::get('/role', 'Admin\SystemController@roleindex')->name('RoleIndex');
    // 角色授权页面
    Route::get('/role/{id}/permission', 'Admin\SystemController@rolepermission')->name('RolePermission'); 
    // 角色授权页面逻辑
    Route::post('/role/{id}/assignpermission', 'Admin\SystemController@roleassignpermission')->name('RoleAssignpermission');
    // 角色编辑
    Route::get('/role/{id}/edit', 'Admin\SystemController@roleedit')->name('RoleEdit');
    // 角色编辑逻辑
    Route::put('/role/{id}/update', 'Admin\SystemController@roleupdate')->name('RoleUpdate');
    // 角色删除
    Route::delete('/role/{id}', 'Admin\SystemController@roledestroy')->name('RoleDestroy');


    // 权限管理
    // 添加权限
    Route::get('/permission/create', 'Admin\SystemController@permissioncreate')->name('PermissionCreate');
    // 添加权限逻辑
    Route::post('/permission/store', 'Admin\SystemController@permissionstore')->name('PermissionStore');
    // 权限管理
    Route::get('/permission', 'Admin\SystemController@permissionindex')->name('PermissionIndex');
    // 权限修改
    Route::get('/permission/{id}/edit', 'Admin\SystemController@permissionedit')->name('PermissionEdit'); 
    // 权限修改逻辑
    Route::put('/permission/{id}/update', 'Admin\SystemController@permissionupdate')->name('PermissionUpdate');
    // 权限删除
    Route::delete('/permission/{id}', 'Admin\SystemController@permissiondestroy')->name('PermissionDestroy');



    // 本人信息维护
    Route::get('/personal', 'Admin\SystemController@personalindex')->name('PersonalIndex');
    // 本人信息维护修改逻辑
    Route::put('/personal/{id}/update', 'Admin\SystemController@personalupdate')->name('PersonalUpdate');

    // 系统设置页面
    Route::get('/setup', 'Admin\SystemController@setupindex')->name('SetupIndex');
    // 系统设置逻辑
    Route::get('/setupupdate', 'Admin\SystemController@setupupdate')->name('SetupUpdate');

    


    // 公告列表
    Route::get('/notice', 'Admin\SystemController@noticeindex')->name('NoticeIndex'); 

    // 公告添加页面
    Route::get('/notice/create', 'Admin\SystemController@create')->name('NoticeCreate'); 

    // 公告添加逻辑
    Route::post('/notice/store', 'Admin\SystemController@store')->name('NoticeStore'); 

    // 公告修改
    Route::get('/notice/{id}/edit', 'Admin\SystemController@edit')->name('NoticeEdit'); 

    // 公告修改逻辑
    Route::put('/notice/{id}/update', 'Admin\SystemController@update')->name('NoticeUpdate'); 
    // 公告显示
    Route::get('/notice/{id}', 'Admin\SystemController@show')->name('NoticeShow'); 

    // 公告删除
    Route::delete('/notice/{id}', 'Admin\SystemController@destroy')->name('NoticeDestroy'); 



    // 账户类型管理
    // 账户类型列表
    Route::get('/account', 'Admin\SystemController@accountindex')->name('AccountIndex');

    // 账户类型添加页面
    Route::get('/account/create', 'Admin\SystemController@accountcreate')->name('AccountCreate');

    // 账户类型添加逻辑
    Route::post('/account/store', 'Admin\SystemController@accountstore')->name('Accountstore');

    // 账户类型修改
    Route::get('/account/{id}/edit', 'Admin\SystemController@accountedit')->name('AccountEdit');

    // 账户类型修改逻辑
    Route::put('/account/{id}/update', 'Admin\SystemController@accountupdate')->name('AccountUpdate');

    // 账户类型显示
    Route::get('/account/{id}', 'Admin\SystemController@accountshow')->name('AccountShow');

    // 账户类型删除
    Route::delete('/account/{id}', 'Admin\SystemController@accountdestroy')->name('AccountDestroy');

});





// 产品管理
Route::group(['middleware' => ['manager.auth', 'permission.control'], 'prefix' => '/admin/products'], function () {

    // 登记卡种（所有）
    // 登记卡种列表查看
    Route::get('/cardbox', 'Admin\ProductController@cardboxindex')->name('cardbox.index');

    // 登记卡种搜索
    Route::get('/cardbox/search', 'Admin\ProductController@cardboxsearch')->name('cardbox.search');

    // 登记卡种创建
    Route::get('/cardbox/create', 'Admin\ProductController@cardboxcreate')->name('cardbox.create');

    // 登记卡种创建保存
    Route::post('/cardbox', 'Admin\ProductController@cardboxstore')->name('cardbox.store');

    // 登记卡种编辑
    Route::get('/cardbox/{id}/edit', 'Admin\ProductController@cardboxedit')->name('cardbox.edit');

    // 登记卡种更新逻辑
    Route::put('/cardbox/{id}', 'Admin\ProductController@cardboxupdate')->name('cardbox.update');

    // 登记卡种单个查看
    Route::get('/cardbox/{id}', 'Admin\ProductController@cardboxshow')->name('cardbox.show');

    // 登记卡种单个删除
    Route::delete('/cardbox/{id}', 'Admin\ProductController@cardboxdestroy')->name('cardbox.destroy');

    // 登记卡种批量删除【接口】
    Route::post('/cardbox/destroys', 'Admin\ProductController@cardboxdestroys')->name('cardbox.destroys');

    // 登记卡种批量启用【接口】
    Route::post('/cardbox/enables', 'Admin\ProductController@cardboxenables')->name('cardbox.enables');

    // 登记卡种批量禁用【接口】
    Route::post('/cardbox/disables', 'Admin\ProductController@cardboxdisables')->name('cardbox.disables');

    // 待审核申请记录
    Route::get('/applycards', 'Admin\ProductController@applycardsindex')->name('applycards.index');

    // 已审核申请记录
    Route::get('/applycards/finished', 'Admin\ProductController@applycardsfinished')->name('applycards.finished');

    // 申请卡片查看
    Route::get('/applycards/{id}', 'Admin\ProductController@applycardsshow')->name('applycards.show');

    // 申请卡片修改
    Route::get('/applycards/{id}/edit', 'Admin\ProductController@applycardsedit')->name('applycards.edit');

    // 申请卡片单条审核通过 [首卡-付佣金]
    Route::post('/applycards/review/{id}/firstsuccessed', 'Admin\ProductController@applycardsreviewfirstsuccessed')->name('applycards.review.firstsuccessed');

    // 申请卡片单条审核通过 [非首卡-不付佣金]
    Route::post('/applycards/review/{id}/successed', 'Admin\ProductController@applycardsreviewsuccessed')->name('applycards.review.successed');

    // 申请卡片单条审核不通过
    Route::post('/applycards/review/{id}/failed', 'Admin\ProductController@applycardsreviewfailed')->name('applycards.review.failed');

    // 申请卡片单条无记录
    Route::post('/applycards/review/{id}/norecord', 'Admin\ProductController@applycardsreviewnorecord')->name('applycards.review.norecord');

    // 申请卡片批量通过【接口】
    Route::post('/applycards/enables', 'Admin\ProductController@applycardsenables')->name('applycards.enables');

    // 申请卡片批量不通过【接口】
    Route::post('/applycards/disables', 'Admin\ProductController@applycardsdisables')->name('applycards.disables');

    // 申请卡片批量无记录【接口】
    Route::post('/applycards/norecords', 'Admin\ProductController@applycardsnorecords')->name('applycards.norecords');

    // 申请卡片修改逻辑
    Route::put('/applycards/{id}', 'Admin\ProductController@applycardsupdate')->name('applycards.update');

    // 获取银行列表 【缓存】 【测试】
    Route::get('/cardboxes', 'Admin\ProductController@getCardboxesCache')->name('getcardboxescache');

    // Excel导出
    // Excel导出-未审核
    Route::get('/applycards/unaudited/export', 'Admin\ProductController@unauditedexport')->name('applycards.unauditedexport');

    // Excel导出-已审核
    Route::get('/applycards/finished/export', 'Admin\ProductController@finishedexport')->name('applycards.finishedexport');
});