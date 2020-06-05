<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\Freeze;
use App\Models\Agent;
use App\Models\Account;
use App\Models\AgentAccount;
use App\Models\Manager;
use App\Models\AdvanceMethod;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Cache;
use Session;
// 启用日志
use Log;
// 微信处理
use EasyWeChat\Factory;
use App\Http\Controllers\WeChatController;

class FinanceController extends Controller
{
    // 初始化公众号
    protected $app;

    // 构造函数
    // 依赖注入
    public function __construct(WeChatController $wechat)
    {
        // 全局配置
        $config = config('wechat.official_account.default');

        // 使用配置来初始化一个公众号应用实例
        $this->app = Factory::officialAccount($config);
    }

    /**
     * 调账经办
     *
     * @return \Illuminate\Http\Response
     */
    public function transactor(Request $request)
    {
        // 逻辑，判断是否有搜索关键词
        $keyword = request('keyword');

        // 查出合伙人模型
        $agents = Agent::select(['id', 'sid', 'name', 'mobile'])->orderBy('created_at', 'asc')->where('mobile', $keyword)->orwhere('name', $keyword)->get();

        // 账户类型
        $accounts = Account::select(['id', 'name'])->orderBy('created_at', 'asc')->get();

        // 渲染
        $page_title = '调账经办';
        return view('admin.finance.transactor', compact('page_title', 'agents', 'accounts', 'keyword'));
    }


    /**
     * 调账经办创建逻辑
     * @param Request $request
     * @return array
     */
    public function transactorstore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'type' => 'required|integer',
            'account_type' => 'required|integer',
            'amount' => 'required|numeric',
            'description' => 'required|string',
        ]);

        // 逻辑
        $agent_id = request('agent_id');
        $agent = Agent::find($agent_id);
        $type = request('type');
        $amount = request('amount');
        $description = request('description');
        $creater = \Session::get('admin')['admin_id'];
        $account_type = request('account_type');

        // 如果选择了调出，但是可用余额小于调出，那么就给出提示
        if ($type == '2') {
            // 涉及到资金，重新从数据库查询一遍比较稳妥
            if ($agent->agentaccount->available_money < $amount) {
                $data = [
                    'code' => '1',
                    'msg' => '调账金额不能超过账户可用余额，请知悉~',
                ];
                return $data;
            }
        }

        // amount必须大于0
        if ($amount <= 0) {
            $data = [
                'code' => '1',
                'msg' => '调账金额必须大于等于0',
            ];
            return $data;
        }

        // 新记录值
        // 因为要操作finances表和agentaccount表，所以启用事务处理
        DB::beginTransaction();
        try {
            // 新纪录值
            $insert_id = Finance::create(compact('agent_id', 'type', 'amount', 'description', 'creater', 'account_type'))->id;

            // 如果没有写入成功，那么就报错
            if (!$insert_id) {
                throw new \Exception('调账经办添加失败');
            }

            // 合伙人-账户表对于agent_id = $agent_id,如果不存在，就新增
            AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                'frozen_money' => '0.00',
                'available_money' => '0.00',
                'sum_money' => '0.00',
            ]);

            // 提交
            DB::commit();

            // json
            $data = [
                'code' => '0',
                'msg' => '调账经办新纪录添加成功',
            ];

            // 返回
            return $data;

        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            $data = [
                'code' => '1',
                'msg' => $e->getMessage(),
            ];
            return $data;
        }
    }


    /**
     * 分润复核-列表
     *
     * @return \Illuminate\Http\Response
     */
    public function benefitcheck(Request $request)
    {
        // DB类搜索逻辑
        $finances = DB::table('finances as f')
                    ->select(['f.id', 'f.excel_id', 'a.sid', 'a.name', 'a.mobile', 'a.status as as', 'f.amount', 'f.type', 'f.creater', 'f.status as fs', 'f.created_at', 'f.description', 'at.name as at_name'])
                    ->join('agents as a', 'f.agent_id', '=', 'a.id')
                    ->join('accounts as at', 'at.id', '=', 'f.account_type')
                    ->where(function($query) use($request) {
                        $mobile = $request->input('mobile');
                        if (!empty($mobile)) {
                            $query->where('a.mobile', $mobile);
                        }
                    })
                    ->where(function($query) use($request) {
                        $account_type = $request->get('account_type');
                        if (!empty($account_type)) {
                            $query->where('f.account_type', $account_type);
                        }
                    })
                    ->where(function($query) use($request) {
                        $type = $request->get('type');
                        if (!empty($type)) {
                            $query->where('f.type', $type);
                        }
                    })
                    ->where(function($query) use($request) {
                        $start_time = $request->input('start_time');
                        if (!empty($start_time)) {
                            $query->where('f.created_at', '>=', $start_time);
                        }
                    })
                    ->where(function($query) use($request) {
                        $end_time = $request->input('end_time');
                        if (!empty($end_time)) {
                            $query->where('f.created_at', '<=', $end_time);
                        }
                    })
                    ->where('f.status', '0')
                    ->when('f.excel_id == 0', function($query) {
                        $query->orderBy('f.id', 'desc');
                    })
                    ->when('f.excel_id <> 0', function($query) {
                        $query->orderBy('f.excel_id', 'asc');
                    })
                    ->paginate(10);

        // 数据整理
        foreach ($finances as $k => $finance) {
            if ($finance->type == 1) {
                $finances[$k]->type_name = '调入';
            } else {
                $finances[$k]->type_name = '调出';
            }
            // 如果creater是0，那么就默认为系统虚拟管理员
            if (empty($finance->creater)) {
                $finances[$k]->creater_name = '系统虚拟管理员';
            } else {
                $finances[$k]->creater_name = Manager::find($finance->creater)->name;
            }
        }

        // 汇总逻辑
        $total_lists = DB::table('finances as f')
                    ->select(DB::raw("sum(f.amount) as total_sum"))
                    ->join('agents as a', 'f.agent_id', '=', 'a.id')
                    ->join('accounts as at', 'at.id', '=', 'f.account_type')
                    ->where(function($query) use($request) {
                        $mobile = $request->input('mobile');
                        if (!empty($mobile)) {
                            $query->where('a.mobile', $mobile);
                        }
                    })
                    ->where(function($query) use($request) {
                        $account_type = $request->get('account_type');
                        if (!empty($account_type)) {
                            $query->where('f.account_type', $account_type);
                        }
                    })
                    ->where(function($query) use($request) {
                        $type = $request->get('type');
                        if (!empty($type)) {
                            $query->where('f.type', $type);
                        }
                    })
                    ->where(function($query) use($request) {
                        $start_time = $request->input('start_time');
                        if (!empty($start_time)) {
                            $query->where('f.created_at', '>=', $start_time);
                        }
                    })
                    ->where(function($query) use($request) {
                        $end_time = $request->input('end_time');
                        if (!empty($end_time)) {
                            $query->where('f.created_at', '<=', $end_time);
                        }
                    })
                    ->where('f.status', '0')
                    ->get();

        // 总金额赋值
        $sum = empty($total_lists[0]->total_sum) ? 0 : $total_lists[0]->total_sum;

        // 渲染
        $page_title = '分润复核查询结果';

        return view('admin.finance.transactorcheck', compact('page_title', 'finances', 'request', 'sum'));

    }


    /**
     * 分润复核审核通过(单条记录审核)
     */
    public function benefitchecksuccessed($id)
    {
        // 逻辑
        $model = Finance::find($id);
        $agentaccount = AgentAccount::where('agent_id', $model->agent_id)->first();

        // 如果状态不为0，说明已经审核完毕，无需审核
        if ($model->status != 0) {
            $data = [
                'code' => '1',
                'msg' => '编号为：'.$id.'的记录已经审核完毕，无需再次审核',
                // 使用短信模板id，2003
                'sendid' => '2003',
                'sendmsg' => "$id",
            ];
            return $data;
        }

        // 审核通过
        // 因为要操作finances表和agentaccount表，所以启用事务处理
        DB::beginTransaction();
        try {
            // 取出合伙人模型
            $agent = Agent::find($model->agent_id);
            if (!$agent) {
                throw new \Exception('合伙人不存在！');
            }

            // 取出openid
            $openid = $agent->openid;

            // 卡信息，推送使用2,4,5号数组元素
            $description = $model->description;
            // 切割，如果含有----字符
            if (strpos($description, '----') !== false) {
                $description = explode('----', $description);
                // 推荐人名字
                $agent_name = !$description[1] ? '***' : $description[1];
                // 申请人名字
                // $card_user_name = !$description[2] ? '空' : $description[2];
                // 申请人名字隐藏处理
                $card_user_name = $this->substr_cutname($description[2]);
                // 申请人手机号
                $card_user_phone = !$description[3] ? '空' : $description[3];
                // 申请卡名字
                $card_name = !$description[4] ? '空' : $description[4];
                // 卡佣金
                $money = !$description[5] ? '0.00' : $description[5];
                // 订单号
                $order_id = $description[6];
            }

            // 写入操作时间
            $created_at = date('Y-m-d H:i:s');

            // 添加操作人
            $modelresult = $model->update([
                'status' => '1',
                'operater' => \Session::get('admin')['admin_id'],
                'operated_at' => date('Y-m-d H:i:s', time()),
            ]);

            // 通过之后，要针对这个合伙人的可用资金进行修改
            // 如果是转入
            if ($model->type == 1) {
                $accountresult = $agentaccount->update([
                    'available_money' => $agentaccount->available_money + $model->amount,
                    'sum_money' => $agentaccount->sum_money + $model->amount,
                ]);
            } else {
                // 如果是转出
                $accountresult = $agentaccount->update([
                    'available_money' => $agentaccount->available_money - $model->amount,
                    'sum_money' => $agentaccount->sum_money - $model->amount,
                ]);
            }
            if ($modelresult && $accountresult) {

                // 给微信号推送消息
                // 分成两种情况，一是办卡审核通过，二是分润审核通过
                // 办卡审核是creater字段为空；以这点区分，分润那边是excel导入的，是有创建人的
                // 如果有创建人，是普通分润到账
                if ($model->creater) {
                    $firstValue = '您有一笔新的分润到账';
                } else {
                    $firstValue = '您的客户'.$card_user_name.'办了一张'.$card_name.'信用卡，审核通过，您获得银行的推广奖励'.$money.'元！已到账，请查收！';
                }
                // 如果存在openid，就推送微信模板，否则就不推送
                if (!empty($openid)) {
                    // 如果是办卡佣金，那么就开始推送推荐成交通知模板
                    if (!$model->creater) {
                        $this->app->template_message->send([
                            'touser' => $openid,
                            'template_id' => 'CUB5p4U2M8HgKpT6WjJfIJMsD_iAP8EQls3lN6JVbqI',
                            // 这里推送当前用户的推广链接
                            'url' => 'http://hhr.yiopay.com/agent/wx?wxshare=wxshare&appuuid=wx88d48c474331a7f5&parentopenId='.$openid,
                            'data' => [
                                'first' => [
                                    'value' => $firstValue,
                                    'color' => '#173177',
                                ],
                                "keyword1" => [
                                    "value" => $money.'元',
                                    "color" => "#FF0000",
                                ],
                                "keyword2" => [
                                    "value" => '办卡佣金',
                                    "color" => "#173177",
                                ],
                                "keyword3" => [
                                    "value" => $order_id,
                                    "color" => "#173177",
                                ],
                                "keyword4" => [
                                    "value" => $created_at,
                                    "color" => "#173177",
                                ],
                                "remark" => [
                                    "value"=>"再接再厉，继续努力哦！".PHP_EOL.'点击查看详情',
                                    "color"=>"#173177",
                                ],
                            ],
                        ]);
                    }
                }

                // 提交
                DB::commit();

                $data = [
                    'code' => '0',
                    'msg' => '编号为：'.$id.'的记录复核通过',
                    // 使用短信模板id，2004
                    'sendid' => '2004',
                    'sendmsg' => "$id",
                ];
                return $data;
            }
        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            $data = [
                'code' => '0',
                'msg' => $e->getMessage(),
            ];
            return $data;
        }

    }



    /**
     * 分润复核审核通过(多条记录审核)
     */
    public function benefitcheckssuccessed(Request $request)
    {
        // 验证
        $this->validate(request(), [
            'ids' => 'required|array',
        ]);

        // 逻辑
        $ids = $request->get('ids');
        // 所有分润复核记录集
        $finances = Finance::findMany($ids);
        // 选中记录数
        $count = $finances->count();

        // 因为涉及表单众多，所以采用事务机制
        DB::beginTransaction();
        try {
            // 循环操作
            foreach ($finances as $k => $finance) {
                // 如果状态不为0，说明已经审核完毕，无需审核
                if ($finance->status != 0) {
                    // 临时变量
                    $temp_id = $finance->id;
                    $data = [
                        'code' => '1',
                        'msg' => '编号为'.$temp_id.'的记录已经审核完毕，无需再次审核',
                        // 使用短信模板id，2003
                        'sendid' => '2003',
                        'sendmsg' => "$temp_id",
                    ];
                    return $data;
                }

                // 找出agentaccount表对应的记录
                $agentaccount = AgentAccount::where('agent_id', $finance->agent_id)->first();

                // 取出合伙人模型
                $agent = Agent::find($finance->agent_id);
                if (!$agent) {
                    throw new \Exception('合伙人不存在！');
                }

                // 取出openid
                $openid = $agent->openid;
                // 卡信息，推送使用2,4,5号数组元素
                $description = $finance->description;
                // 切割，如果含有----字符
                if (strpos($description, '----') !== false) {
                    $description = explode('----', $finance->description);
                    // 推荐人名字
                    $agent_name = !$description[1] ? '***' : $description[1];
                    // 申请人名字
                    // $card_user_name = !$description[2] ? '空' : $description[2];
                    // 申请人名字隐藏处理
                    $card_user_name = $this->substr_cutname($description[2]);
                    // 申请人手机号
                    $card_user_phone = !$description[3] ? '空' : $description[3];
                    // 申请卡名字
                    $card_name = !$description[4] ? '空' : $description[4];
                    // 卡佣金
                    $money = !$description[5] ? '0.00' : $description[5];
                    // 订单号
                    $order_id = $description[6];
                }

                // 写入操作时间
                $created_at = date('Y-m-d H:i:s');

                // 执行审核通过逻辑
                $updated_at = date('Y-m-d H:i:s', time());
                $finance->update([
                    'status' => '1',
                    'operater' => \Session::get('admin')['admin_id'],
                    'operated_at' => $updated_at,
                ]);
                // 通过之后，要针对这个合伙人的可用资金进行修改
                // 如果是转入
                if ($finance->type == 1) {
                    $agentaccount->update([
                        'available_money' => $agentaccount->available_money + $finance->amount,
                        'sum_money' => $agentaccount->sum_money + $finance->amount,
                    ]);
                } else {
                    // 如果是转出
                    $agentaccount->update([
                        'available_money' => $agentaccount->available_money - $finance->amount,
                        'sum_money' => $agentaccount->sum_money - $finance->amount,
                    ]);
                }


                // 给微信号推送消息
                // 分成两种情况，一是办卡审核通过，二是分润审核通过
                // 办卡审核是creater字段为空；以这点区分，分润那边是excel导入的，是有创建人的
                // 如果有创建人，是普通分润到账
                if ($finance->creater) {
                    $firstValue = '您有一笔新的分润到账';
                } else {
                    $firstValue = '您的客户'.$card_user_name.'办了一张'.$card_name.'信用卡，审核通过，您获得银行的推广奖励'.$money.'元！已到账，请查收！';
                }
                // 如果存在openid，就推送微信模板，否则就不推送
                if (!empty($openid)) {
                    // 开始推送推荐成交通知模板
                    if (!$finance->creater) {
                        // // 测试openid
                        // if ($openid == 'ol0Z1uIjM2v6lFxU2d8gm7l5tEm8') {
                        //     $openid = 'ol0Z1uJ8dkjU__z66lukgiZsNZl0';
                        // }
                        $this->app->template_message->send([
                            'touser' => $openid,
                            'template_id' => 'CUB5p4U2M8HgKpT6WjJfIJMsD_iAP8EQls3lN6JVbqI',
                            // 这里推送当前用户的推广链接
                            'url' => 'http://hhr.yiopay.com/agent/wx?wxshare=wxshare&appuuid=wx88d48c474331a7f5&parentopenId='.$openid,
                            'data' => [
                                'first' => [
                                    'value' => $firstValue,
                                    'color' => '#173177',
                                ],
                                "keyword1" => [
                                    "value" => $money.'元',
                                    "color" => "#FF0000",
                                ],
                                "keyword2" => [
                                    "value" => '办卡佣金',
                                    "color" => "#173177",
                                ],
                                "keyword3" => [
                                    "value" => $order_id,
                                    "color" => "#173177",
                                ],
                                "keyword4" => [
                                    "value" => $created_at,
                                    "color" => "#173177",
                                ],
                                "remark" => [
                                    "value"=>"再接再厉，继续努力哦！".PHP_EOL.'点击查看详情',
                                    "color"=>"#173177",
                                ],
                            ],
                        ]);
                    }
                }
            }

            // 提交
            DB::commit();

            // json集合
            $data = [
                'code' => '0',
                'msg' => '您好，批量分润复核已完毕，请知悉。',
                // 完整发送，内容留空
                // 使用短信模板id，2006
                'sendid' => '2006',
                'sendmsg' => "$count",
            ];
            return $data;

        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            // json集合
            $data = [
                'code' => '1',
                'msg' => $e->getMessage(),
            ];
            return $data;
        }
    }



    /**
     * 分润复核审核不通过(单条记录审核)
     */
    public function benefitcheckfailed($id)
    {
        // 逻辑
        $model = Finance::find($id);
        $agentaccount = AgentAccount::find($model->agent_id);

        // 如果状态不为0，说明已经审核完毕，无需审核
        if ($model->status != 0) {
            $data = [
                'code' => '1',
                'msg' => '编号为'.$id.'的记录已经审核完毕，无需再次审核',
                // 使用短信模板id，2003
                'sendid' => '2003',
                'sendmsg' => "$id",
            ];
            return $data;
        }

        // 审核不通过
        // 直接删除这条记录即可，而且审核失败合伙人原来的账户没有任何影响
        $updated_at = date('Y-m-d H:i:s', time());
        $modelresult = $model->update([
            'status' => '2',
            'operater' => \Session::get('admin')['admin_id'],
            'operated_at' => $updated_at,
        ]);

        if ($modelresult) {
            $data = [
                'code' => '0',
                'msg' => '编号为'.$id.'的记录审核未通过',
                // 使用短信模板id，2007
                'sendid' => '2007',
                'sendmsg' => "$id",
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '编号为'.$id.'的记录审核未通过操作失败',
                // 使用短信模板id，2008
                'sendid' => '2008',
                'sendmsg' => "$id",
            ];
        }
        // 返回
        return $data;
    }


    /**
     * 分润复核审核不通过(多条记录审核)
     */
    public function benefitchecksfailed(Request $request)
    {
        // 验证
        $this->validate($request, [
            'ids' => 'required|array',
        ]);

        // 逻辑
        $ids = $request->get('ids');
        // 所有分润复核记录集
        $finances = Finance::findMany($ids);
        // 选中记录数
        $count = $finances->count();

        // 因为涉及表单众多，所以采用事务机制
        DB::beginTransaction();
        try {

            foreach ($finances as $k => $finance) {
                // 如果状态不为0，说明已经审核完毕，无需审核
                if ($finance->status != 0) {
                    // 临时变量
                    $temp_id = $finance->id;
                    $data = [
                        'code' => '1',
                        'msg' => '编号为'.$temp_id.'的记录已经审核完毕，无需再次审核',
                        // 使用短信模板id，2003
                        'sendid' => '2003',
                        'sendmsg' => "$temp_id",
                    ];
                    return $data;
                }
                // 审核不通过
                // 直接删除这条记录即可，而且审核失败合伙人原来的账户没有任何影响
                $updated_at = date('Y-m-d H:i:s', time());
                $finance->update([
                    'status' => '2',
                    'operater' => \Session::get('admin')['admin_id'],
                    'operated_at' => $updated_at,
                ]);

            }

            // 提交
            DB::commit();
            // json
            $data = [
                'code' => '0',
                'msg' => '您好，分润复核审核失败操作已完毕，请知悉。',
                // 使用短信模板id，2009
                'sendid' => '2009',
                'sendmsg' => "$count",
            ];
            return $data;

        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            // json集合
            $data = [
                'code' => '1',
                'msg' => $e->getMessage(),
            ];
            return $data;
        }
    }


    /**
     * 调账查询
     */
    public function transactquery(Request $request)
    {
        // DB类搜索逻辑
        $finances = DB::table('finances as f')
                    ->select(['f.id', 'a.sid', 'a.name', 'a.mobile', 'a.status as as', 'f.amount', 'f.type', 'f.creater', 'f.status as fs', 'f.created_at', 'f.description', 'at.name as at_name', 'f.operater', 'f.operated_at', 'f.status'])
                    ->join('agents as a', 'f.agent_id', '=', 'a.id')
                    ->join('accounts as at', 'at.id', '=', 'f.account_type')
                    ->where(function($query) use($request) {
                        $name = $request->input('name');
                        if (!empty($name)) {
                            $query->where('a.name', $name);
                        }
                    })
                    ->where(function($query) use($request) {
                        $mobile = $request->input('mobile');
                        if (!empty($mobile)) {
                            $query->where('a.mobile', $mobile);
                        }
                    })
                    ->where(function($query) use($request) {
                        $account_type = $request->get('account_type');
                        if (!empty($account_type)) {
                            $query->where('f.account_type', $account_type);
                        }
                    })
                    ->where(function($query) use($request) {
                        $type = $request->get('type');
                        if (!empty($type)) {
                            $query->where('f.type', $type);
                        }
                    })
                    ->where(function($query) use($request) {
                        $start_time = $request->input('start_time');
                        if (!empty($start_time)) {
                            $query->where('f.created_at', '>=', $start_time);
                        }
                    })
                    ->where(function($query) use($request) {
                        $end_time = $request->input('end_time');
                        if (!empty($end_time)) {
                            $query->where('f.created_at', '<=', $end_time);
                        }
                    })
                    ->where(function($query) use($request) {
                        $status = $request->get('status');
                        if ($status != '') {
                            $query->where('f.status', '=', $status);
                        }
                    })
                    ->orderBy('f.created_at', 'desc')
                    ->paginate(10);

        // 数据整理
        foreach ($finances as $k => $finance) {
            // 调账类型
            if ($finance->type == 1) {
                $finances[$k]->type_name = '调入';
            } else {
                $finances[$k]->type_name = '调出';
            }

            // 审核状态
            if ($finance->fs == 1) {
                $finances[$k]->status_name = '审核通过';
            } elseif ($finance->fs == 2) {
                $finances[$k]->status_name = '未通过';
            } else {
                $finances[$k]->status_name = '待审核';
            }

            // 经办人
            if (Manager::find($finance->creater)) {
                $finances[$k]->creater_name = Manager::find($finance->creater)->name;
            } else {
                $finances[$k]->creater_name = '';
            }

            // 审核人
            if (Manager::find($finance->operater)) {
                $finances[$k]->operater_name = Manager::find($finance->operater)->name;
            } else {
                $finances[$k]->operater_name = '';
            }

        }

        // 汇总逻辑
        $total_lists = DB::table('finances as f')
                        ->select(DB::raw("sum(f.amount) as total_sum"))
                        ->join('agents as a', 'f.agent_id', '=', 'a.id')
                        ->join('accounts as at', 'at.id', '=', 'f.account_type')
                        ->where(function($query) use($request) {
                            $name = $request->input('name');
                            if (!empty($name)) {
                                $query->where('a.name', $name);
                            }
                        })
                        ->where(function($query) use($request) {
                            $mobile = $request->input('mobile');
                            if (!empty($mobile)) {
                                $query->where('a.mobile', $mobile);
                            }
                        })
                        ->where(function($query) use($request) {
                            $account_type = $request->get('account_type');
                            if (!empty($account_type)) {
                                $query->where('f.account_type', $account_type);
                            }
                        })
                        ->where(function($query) use($request) {
                            $type = $request->get('type');
                            if (!empty($type)) {
                                $query->where('f.type', $type);
                            }
                        })
                        ->where(function($query) use($request) {
                            $start_time = $request->input('start_time');
                            if (!empty($start_time)) {
                                $query->where('f.created_at', '>=', $start_time);
                            }
                        })
                        ->where(function($query) use($request) {
                            $end_time = $request->input('end_time');
                            if (!empty($end_time)) {
                                $query->where('f.created_at', '<=', $end_time);
                            }
                        })
                        ->where(function($query) use($request) {
                            $status = $request->get('status');
                            if ($status != '') {
                                $query->where('f.status', '=', $status);
                            }
                        })
                        ->get();

        // 总金额赋值
        $sum = empty($total_lists[0]->total_sum) ? 0 : $total_lists[0]->total_sum;

        // 渲染
        $page_title = '调账查询';
        return view('admin.finance.transactorquery', compact('page_title', 'finances', 'request', 'sum'));

    }


    /**
     * 资金冻结
     */
    public function freeze()
    {
        // 逻辑，判断是否有搜索关键词
        $mobile = request('mobile');
        // 查出合伙人模型
        $agent = Agent::select(['id', 'sid', 'name', 'mobile'])->orderBy('created_at', 'asc')->where('mobile', $mobile)->first();
        // 账户类型
        $accounts = Account::select(['id', 'name'])->orderBy('created_at', 'desc')->get();

        // 渲染
        $page_title = '资金冻结';
        return view('admin.finance.freeze', compact('page_title', 'agent', 'accounts', 'mobile'));
    }


    /**
     * 冻结添加逻辑
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function freezestore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'agent_id' => 'required|integer',
            'account_type' => 'required|integer',
            'amount' => 'required|numeric',
            'description' => 'required|string',
        ]);

        // 逻辑
        $agent_id = request('agent_id');
        $agent = Agent::find($agent_id);
        $amount = request('amount');
        $description = request('description');
        $operater = \Session::get('admin')['admin_id'];
        $account_type = request('account_type');

        // 如果可用余额小于冻结金额，那么就给出提示
        if ($agent->agentaccount->available_money < $amount) {
            $data = [
                'code' => '1',
                'msg' => '冻结金额不能超过账户可用余额，请知悉~',
            ];
            return $data;
        }

        // amount必须大于0
        if ($amount < 0) {
            $data = [
                'code' => '1',
                'msg' => '冻结金额必须大于等于0',
            ];
            return $data;
        }

        // 新记录值
        // 因为要操作finances表和agentaccount表，所以启用事务处理
        DB::beginTransaction();
        try {

            // 合伙人资产表
            $agentaccount = AgentAccount::where('agent_id', $agent_id)->first();
            if (!$agentaccount) {
                throw new \Exception('合伙人资产表不存在，操作失败！');
            }
            $frozen_money = $agentaccount->frozen_money;

            // 如果冻结设置为0，那么就取消冻结，并删除冻结中的记录
            if ($amount == 0) {
                // // 如果冻结表中没有冻结记录，那么就无需进行任何操作
                // if (Freeze::where('agent_id', $agent_id)->get()->count() == 0) {
                //     throw new \Exception('当前用户不存在冻结记录，无需取消冻结！');
                // }
                // if (!Freeze::where('agent_id', $agent_id)->delete()) {
                //     throw new \Exception('删除冻结记录失败');
                // }
                // 把所有的冻结记录status改为0
                if (!Freeze::where('agent_id', $agent_id)->update([
                    'status' => '0',
                ])) {
                    throw new \Exception('清除冻结记录失败');
                }

                // 然后添加取消冻结记录
                $status = '0';
                if (!Freeze::create(compact('agent_id', 'amount', 'description', 'operater', 'account_type', 'status'))) {
                    throw new \Exception('冻结记录添加失败');
                }

                // 资产表修改
                if (!$agentaccount->update([
                    'frozen_money' => '0.00',
                    'available_money' => $agentaccount->available_money + $frozen_money,
                ])) {
                    throw new \Exception('更新用户资产表失败');
                }
            } else {
                // 新纪录值
                if (!Freeze::create(compact('agent_id', 'amount', 'description', 'operater', 'account_type'))) {
                    throw new \Exception('冻结记录添加失败');
                }

                // 资产表修改
                if (!$agentaccount->update([
                    'frozen_money' => $agentaccount->frozen_money + $amount,
                    'available_money' => $agentaccount->available_money - $amount,
                ])) {
                    throw new \Exception('更新用户资产表失败');
                }

            }

            // 提交
            DB::commit();

            // json
            if ($amount == 0) {
                $data = [
                    'code' => '0',
                    'msg' => '删除冻结成功，可用余额已恢复最大值',
                ];
            } else {
                $data = [
                    'code' => '0',
                    'msg' => '账户资金冻结成功',
                ];
            }

            // 返回
            return $data;

        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            // 返回
            $data = [
                'code' => '1',
                'msg' => $e->getMessage(),
            ];
            return $data;
        }
    }

    /**
     * 调账经办(批量)
     *
     * @return \Illuminate\Http\Response
     */
    public function transactors(Request $request)
    {
        // 渲染
        $page_title = '调账经办(批量)';
        return view('admin.finance.transactors', compact('page_title'));
    }


    /**
     * 调账经办(批量)-逻辑
     * @param Request $request
     */
    public function transactorsstore(Request $request)
    {
        // 判断是否有文件上传
        if (!empty($request->file('file'))) {
            $path = $request->file('file')->storePublicly(date('Ymd'));
            $fileurl = 'storage/'.$path;
            $basename = pathinfo($fileurl)['basename'];

            // 内部使用$fileurl变量，因为如果出错，要进行删除
			Excel::load($fileurl, function($reader) use($fileurl) {

				//获取excel的第几张表
                $reader = $reader->getSheet(0);

				//获取表中的数据
                $records = $reader->toArray();

                // 去掉第一列
                array_shift($records);

                // 删除数组集合里面的空数组
                // 我们规定，如果手机号为空，就判断当前记录为空，然后予以删除
                foreach ($records as $k => $record) {
                    if (empty($record[1])) {
                        unset($records[$k]);
                    }
                }

                // // 取出数据
                // echo '<pre>';
                // print_r($records);
                // echo '</pre>';

                // exit();

                // 采用事务机制进行处理
                DB::beginTransaction();
                try {
                    // 循环操作
                    foreach ($records as $k => $record) {

                        // 数组语义化处理
                        $excel_id = $record[0];
                        $mobile = $record[1];
                        $amount = $record[2];
                        $account_type = $record[3];
                        $type = $record[4];
                        $description = $record[5];
                        // 取出当前登录用户
                        $creater = Session::get('admin')['admin_id'];

                        // 在这其中，$mobile，$amount，$account_type，$type，$description，$creater都是不能为空的，否则就给出错误提示
                        if (empty($mobile) || empty($amount) || empty($account_type) || empty($type) || empty($description)) {
                            throw new \Exception('合伙人手机号、金额、账户类型、调账类型、备注必须填写，缺一不可，请修改excel文件后再重新上传！');
                        }

                        // 首先查找当前条目的合伙人模型
                        $agent = Agent::where('mobile', $mobile)->first();

                        // 判断合伙人
                        if (empty($agent)) {
                            throw new \Exception('手机号码为'.$mobile.'的合伙人不存在！');
                        }

                        // 取出agent_id
                        $agent_id = $agent->id;

                        // 如果存在agent模型，那么就开始增加调账记录
                        // 新纪录值
                        $insert_id = Finance::create(compact('agent_id', 'type', 'amount', 'description', 'creater', 'account_type', 'excel_id'))->id;

                        // 如果没有写入成功，那么就报错
                        if (!$insert_id) {
                            throw new \Exception('调账经办添加失败');
                        }

                        // 合伙人-账户表对于agent_id = $agent_id,如果不存在，就新增
                        AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                            'frozen_money' => '0.00',
                            'available_money' => '0.00',
                            'sum_money' => '0.00',
                        ]);
                    }

                    // 提交
                    DB::commit();

                    // 成功提示
                    $data = [
                        'code' => '0',
                        'msg' => '数据导入成功',
                    ];
                    echo json_encode($data);

                } catch (\Exception $e) {
                    // 回滚
                    DB::rollback();
                    $data = [
                        'code' => '1',
                        'msg' => $e->getMessage(),
                    ];
                    echo json_encode($data);
                    // 删除出错文件
                    @unlink($fileurl);
                }

            });
        } else {
            $data = [
                'code' => '1',
                'msg' => '请上传文件',
            ];
            echo json_encode($data);
        }
    }

    /**
     * 账户信息-模板
     */
    public function show()
    {

        // 测试账户
        // echo '<pre>';
        // print_r($this->getaccount());
        // exit();

        // 渲染
        $page_title = '账户信息';
        return view('admin.finance.show', compact('page_title'));
    }

    /**
     * 代付通道账户信息-接口
     */
    public function getfinanceaccount()
    {
        // 获得实例化模型
        $tools = $this->getInterface();

        // 取出相关数据
        $method = AdvanceMethod::where('status', '1')->first();
        $withdraw = Withdraw::first();

        // 参数
        $merchant_id = $method->merchant_id;
        $username = $method->username;
        $password = $method->password;
        $cash_id = $withdraw->cash_id;
        $reqtime = date('YmdHis');
        $acctno = $method->acctno;

        // 源数组(合伙人提现到自己的银行卡，金额是$account)
        $params = array(
            'INFO' => array(
                // 账户查询代码，必填，固定为300000
                'TRX_CODE' => '300000',
                // 版本，必填
                'VERSION' => '03',
                // 数据格式，必填，2代表XML
                'DATA_TYPE' => '2',
                // 处理级别，必填，0-9 0优先级最低
                'LEVEL' => '6',
                // 商户ID
                'MERCHANT_ID' => "$merchant_id",
                // 用户名，必填
                'USER_NAME' => "$username", // 正式账户用户名
                // 用户密码，必填
                'USER_PASS' => "$password",    // 正式账户密码
                // 交易流水号，必填，建议格式：商户号+时间+固定位数顺序流水号
                'REQ_SN' => "$cash_id",
                // 请求交易时间戳,YYYYMMDDHHMMSS
                'REQTIME' => "$reqtime",
            ),
            'ACQUERYREQ' => array(
                // 账户号
                'ACCTNO' => "$acctno",
            ),
        );

        // 发起请求
        $result = $tools->send($params);

        // 返回结果
        return $result;

        // 如果正常返回，结果如下：
        // Array
        // (
        //     [AIPG] => Array
        //         (
        //             [INFO] => Array
        //                 (
        //                     [TRX_CODE] => 300000
        //                     [VERSION] => 03
        //                     [DATA_TYPE] => 2
        //                     [REQ_SN] => DF201808291610552350
        //                     [RET_CODE] => 0000
        //                     [ERR_MSG] => 查询成功
        //                     [REPTIME] => 20180830143951
        //                 )

        //             [ACQUERYREP] => Array
        //                 (
        //                     [ACNODE] => Array
        //                         (
        //                             [ACCTNO] => 200110000008201000
        //                             [ACCTNAME] => 天津意远投资咨询有限公司
        //                             [BALANCE] => 83587
        //                             [USABLEBAL] => 0
        //                             [BALBY] => 2
        //                             [DEPOSIT] => 1
        //                             [WITHDRAW] => 1
        //                             [TRANSFERIN] => 1
        //                             [TRANSFEROUT] => 1
        //                             [PAYABLE] => 1
        //                             [DEFCLR] => 0
        //                         )

        //                 )

        //         )

        // )


        // 查询错误，返回如下，例子
        // ACQUERYREP部分为空，没有任何数据
        // Array
        // (
        //     [AIPG] => Array
        //         (
        //             [INFO] => Array
        //                 (
        //                     [TRX_CODE] => 300000
        //                     [VERSION] => 03
        //                     [DATA_TYPE] => 2
        //                     [REQ_SN] => DF201808291610552350
        //                     [RET_CODE] => 0000
        //                     [ERR_MSG] => 查询成功
        //                     [REPTIME] => 20180830144328
        //                 )

        //             [ACQUERYREP] =>
        //         )

        // )

    }


    /**
     * 合伙人汇总账户信息-接口
     * 只有审核之后的才计入累计
     */
    public function getagentsaccount()
    {
        // 保存累加
        $sum = 0;
        // 账户列表
        $agentaccounts = AgentAccount::select(['available_money'])->get();
        foreach ($agentaccounts as $agentaccount) {
            $sum += $agentaccount->available_money;
        }
        // 返回
        return $sum;
    }


    /**
     * 分润制单
     */
    public function benefitbill()
    {
        // 列表
        $page_title = '分润制单';
        return view('admin.finance.benefitbill', compact('page_title'));
    }

    /**
     * 通联支付接口-业务逻辑实例化
     */
    public function getInterface()
    {
        // 引入通联接口
        // 引入三个类文件
        $base_url = base_path('public');
        // 支付通道
        $method = AdvanceMethod::where('status', '1')->first();
        // 如果是通联线上环境，那么就读取PhpTools.class.php，负责就读取PhpToolsTest.class.php
        if ($method->id == 1) {
            require_once $base_url . "/backend/allinpayInter/libs/PhpTools.class.php";
        } elseif ($method->id == 2) {
            require_once $base_url . "/backend/allinpayInter/libs/PhpToolsTest.class.php";
        }
        require_once $base_url . "/backend/allinpayInter/libs/ArrayXml.class.php";
        require_once $base_url . "/backend/allinpayInter/libs/cURL.class.php";
        return \PhpTools::getInstance();
    }

    /**
     * 发送短信-接口(和FinanceController中的方法重叠，待整合)
     */
    public function sendMsg(Request $request)
    {
        // 测试url
        // $url = 'http://api.id98.cn/api/sms?appkey=d10a8e06284cf889deaf93ffb5d9c60a&phone=13800000000&templateid=1000&param=623584';
        // 电话是当前登录管理员的ID，也就是\Session::get('admin')['admin_mobile']
        $tel = Session::get('admin')['admin_mobile'];
        // 接收数据
        // 如果手机号存在，说明用户已经登录
        if (!empty($tel)) {
            $sendid = $request->get('sendid');
            $sendmsg = $request->get('sendmsg');
            $url = 'http://api.id98.cn/api/sms?appkey='.self::MSG_APPKEY.'&phone='.$tel.'&templateid='.$sendid.'&param='.$sendmsg;
            // 取出结果
            $result = $this->http_curl($url);
            // 为了可控，需要获取当前短信是否发送成功的结果，把这个结果保存在日志文件中
            $msg = '';
            $msg .= '<pre>'."\n";
            $msg .= '短信计划发送时间：'.date('Y-m-d H:i:s')."\n";
            $msg .= '短信发送url地址：'.$url."\n\n";
            $msg .= '短信发送结果：'."\n";
            $arr = print_r($result, true);
            $msg .= "$arr";
            $msg .= "\n\n";
            // 写入日志
            Log::info($msg);
            // 记录后返回给前端
            return $result;
        } else {
            $response = [
                'errcode' => '1',
                'errmsg' => '手机号码不存在',
            ];
            return $response;
        }

    }

    /**
     * Excel文件导出功能
     */
    public function export(Request $request)
    {
        // 逻辑
        // DB类搜索逻辑
        $finances = DB::table('finances as f')
            ->select(['f.id', 'a.sid', 'a.name', 'a.mobile', 'a.status as as', 'f.amount', 'f.type', 'f.creater', 'f.status as fs', 'f.created_at', 'f.description', 'at.name as at_name', 'f.operater', 'f.operated_at', 'f.status'])
            ->join('agents as a', 'f.agent_id', '=', 'a.id')
            ->join('accounts as at', 'at.id', '=', 'f.account_type')
            ->where(function($query) use($request) {
                $name = $request->input('name');
                if (!empty($name)) {
                    $query->where('a.name', $name);
                }
            })
            ->where(function($query) use($request) {
                $mobile = $request->input('mobile');
                if (!empty($mobile)) {
                    $query->where('a.mobile', $mobile);
                }
            })
            ->where(function($query) use($request) {
                $account_type = $request->get('account_type');
                if (!empty($account_type)) {
                    $query->where('f.account_type', $account_type);
                }
            })
            ->where(function($query) use($request) {
                $type = $request->get('type');
                if (!empty($type)) {
                    $query->where('f.type', $type);
                }
            })
            ->where(function($query) use($request) {
                $start_time = $request->input('start_time');
                if (!empty($start_time)) {
                    $query->where('f.created_at', '>=', $start_time);
                }
            })
            ->where(function($query) use($request) {
                $end_time = $request->input('end_time');
                if (!empty($end_time)) {
                    $query->where('f.created_at', '<=', $end_time);
                }
            })
            ->where(function($query) use($request) {
                $status = $request->get('status');
                if ($status != '') {
                    $query->where('f.status', '=', $status);
                }
            })
            ->orderBy('f.created_at', 'desc')
            ->get();

        // 定义一个excel对象
         $cellData = [];

        // 数据处理逻辑
        foreach ($finances as $k => $finance) {

            // 调账类型
            if ($finance->type == 1) {
                $finances[$k]->type_name = '调入';
            } else {
                $finances[$k]->type_name = '调出';
            }

            // 审核类型
            if ($finance->fs == 1) {
                $finances[$k]->status_name = '审核通过';
            } elseif ($finance->fs == 2) {
                $finances[$k]->status_name = '未通过';
            } else {
                $finances[$k]->status_name = '待审核';
            }

            // 经办人
            if (Manager::find($finance->creater)) {
                $finances[$k]->creater_name = Manager::find($finance->creater)->name;
            } else {
                $finances[$k]->creater_name = '';
            }

            // 审核人
            if (Manager::find($finance->operater)) {
                $finances[$k]->operater_name = Manager::find($finance->operater)->name;
            } else {
                $finances[$k]->operater_name = '';
            }

            // excel对象赋值
            $cellData[] = [$finance->id, $finance->sid, $finance->name, $finance->at_name, $finances[$k]->type_name, $finance->amount, $finance->creater_name, $finance->created_at, $finance->operater_name, $finance->operated_at, $finances[$k]->status_name, $finance->description];

        }

        // cellData头部插入标题
        array_unshift($cellData, ['序号','合伙人编号','合伙人姓名','账户类型', '调账类型', '调账金额','经办人','经办日期','审核人','审核日期','调账状态','调账原因']);

        // excel导出逻辑
        Excel::create('调账列表', function($excel) use ($cellData) {
            $excel->sheet('调账列表', function($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

    /**
     * 发送4位数字验证码
     */
    public function createcode()
    {
        // 生成的验证码保存在cache里，默认5分钟有效期
        Cache::put('yzm', mt_rand(1000, 9999), 5);
        return Cache::get('yzm');
    }

    /**
     * 取出4位验证码
     */
    public function getfinancecode()
    {
        // 逻辑
        return Cache::get('yzm');
    }

    /**
     * 批量经办模板下载
     */
    public function download()
    {
        return response()->download(realpath(base_path('public')).'/backend/file/transactor.xls', "批量经办模板".'.xls');
    }

}
