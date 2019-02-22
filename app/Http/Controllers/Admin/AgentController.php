<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Agent;
use App\Model\Bank;
use App\Model\Card;
use App\Model\Role;
use App\Model\AgentAccount;
use Illuminate\Support\Facades\DB;
use Zhuzhichao\BankCardInfo\BankCard;
use App\Http\Controllers\Agent\AgentauthController as AgentApi;
use Maatwebsite\Excel\Facades\Excel;

class AgentController extends Controller
{
    // 当前控制器/方法
    protected $controller_action;
    protected $agentapi;

    /**
     * 构造函数
     */
    public function __construct(AgentApi $agentapi)
    {
        // 控制器
        $this->controller_action = $this->getControllerAction();

        // agent
        $this->agentapi = $agentapi;
    }

    /**
     * 合伙人列表
     */
    public function index(Request $request)
    {
        // 渲染
        $page_title = '合伙人列表';
        $controller_action = $this->controller_action;
        $agents = Agent::select(['id', 'sname', 'sid', 'name', 'created_at', 'updated_at', 'mobile', 'status', 'id_number', 'openid', 'parentopenid', 'method'])->orderBy('created_at', 'desc')->paginate(10);

        // 总的合伙人层级表
        $format_agents = $this->agentapi->showtreeagents();

        // 因为模板要用ajax读取，所以这里对数据进行一下处理
        foreach ($agents as $k => $agent) {
            // 取出当前用户默认的结算卡片，也就是第一录入的卡片
            $card = $agent->card;
            // 卡片信息
            // $cardinfo = BankCard::info($card['card_number']);
            // 如果有卡片，那么就取出
            if ($card) {
                // 卡号
                $agents[$k]->card_number = $card['card_number'];
                // 默认卡片的开户行
                $agents[$k]->bank_name = $card->bank->name;
                // 支行
                $agents[$k]->branch = $card['branch'];
            } else {
                // 否则就留空
                // 卡号
                $agents[$k]->card_number = '用户未录入';
                // 默认卡片的开户行
                $agents[$k]->bank_name = '用户未录入';
                // 支行
                $agents[$k]->branch = '用户未录入';
            }

            // 身份证号
            $agents[$k]->id_number = empty($agent->id_number) ? "用户未录入" : $agent->id_number;

            // 注册途径
            switch ($agent->method) {
                case '1':
                    $agents[$k]->method_name = '后台添加';
                    break;
                case '2':
                    $agents[$k]->method_name = '办卡自动添加';
                    break;
                case '3':
                    $agents[$k]->method_name = '微信主动注册';
                    break;
                case '4':
                    $agents[$k]->method_name = '实名认证注册';
                    break;
                case '5':
                    $agents[$k]->method_name = '首页授权添加';
                    break;
                default:
                    $agents[$k]->method_name = '后台添加';
            }

            // 审核状态
            switch ($agent->status) {
                case '0':
                    $agents[$k]->status_name = '未审核';
                    break;
                case '1':
                    $agents[$k]->status_name = '审核通过';
                    break;
                case '2':
                    $agents[$k]->status_name = '审核未通过';
                    break;
                default:
                    $agents[$k]->status_name = '未审核';
            }
            // 上级代理人
            // 如果不小心存入了null，那么就为空
            if ($agents[$k]->parentopenid == 'null' || $agents[$k]->parentopenid == 'NULL' || !$agents[$k]->parentopenid) {
                $agents[$k]->parentopenid = null;
            }

            if ($agents[$k]->parentopenid) {
                // 如果是null，那么就为空
                $parent_agent = Agent::where('openid', $agents[$k]->parentopenid)->first();
                // 如果上级合伙人不存在，说明被禁用了，那么上级合伙人无效
                if ($parent_agent) {
                    if ($parent_agent->name) {
                        $agents[$k]->parentopenid_name = $parent_agent->mobile . '（' . $parent_agent->name . '）';
                    } else {
                        $agents[$k]->parentopenid_name = $parent_agent->mobile;
                    }
                } else {
                    $agents[$k]->parentopenid_name = '无';
                }
            } else {
                $agents[$k]->parentopenid_name = '无';
            }

            // 如果openid为NULL，那么就赋值为空
            if ($agent->openid == 'null' || $agent->openid == 'NULL' || !$agent->openid) {
                $agent->openid = '';
            }

            // 查找每一个用户下面的下级代理
            foreach ($format_agents as $format_agent) {
                if ($format_agent['openid'] == $agent->openid) {
                    $agents[$k]->level = $format_agent['level'];
                }
            }
            $agents[$k]->second_level = $this->agentapi->formatagents($format_agents, $agent->openid, $agents[$k]->level + 1);
        }

        // 渲染
        return view('admin.agent.index', compact('page_title', 'agents', 'controller_action', 'request'));
    }

    /**
     * 合伙人开户
     */
    public function create()
    {
        // 渲染
        $page_title = '合伙人开户';
        return view('admin.agent.create', [
            'page_title' => $page_title,
        ]);
    }

    /**
     * 合伙人开户逻辑
     */
    public function store(Request $request)
    {
        // 验证
        $this->validate($request, [
            'sname' => 'required|string',
            'name' => 'required|string',
            // 'id_number' => 'required|unique:agents,id_number',
            'mobile' => 'required|unique:agents,mobile|regex:/^1[345678][0-9]{9}$/',
            // 'card_number' => 'required|unique:cards,card_number',
            'password' => 'required',
        ]);

        // 逻辑
        $sname = request('sname');
        $name = request('name');
        $id_number = request('id_number');
        $mobile = request('mobile');
        $branch = request('branch');
        $card_number = request('card_number');
        $password = bcrypt(request('password'));
        $branch = empty(request('branch')) ? 0 : request('branch');
        $method = request('method');

        // 新记录值
        // 因为要记录两次，所以这里启用事务处理
        DB::beginTransaction();
        try {

            // 如果卡号填写了
            // 判断是否无效卡号
            if (!empty($card_number)) {
                $cardinfo = $this->checkcard($card_number);
                if ($cardinfo['validated'] == false) {
                    throw new \Exception('银行卡号无效，请重新输入！');
                }

                // 判断卡信息开户行是否存在
                $bank_id = Bank::firstOrCreate(['name' => $cardinfo['bankName']])->id;
            }

            // 新纪录值
            $agent = Agent::create(compact('sname', 'name', 'id_number', 'mobile', 'password', 'method'));

            // 如果没有写入成功，那么就报错
            if (!$agent) {
                throw new \Exception('合伙人添加失败');
            }

            // $agent_id
            $agent_id = $agent->id;

            // 写入sid值
            $result = \DB::table('agents')->select(\DB::raw("concat('M', right(concat('00000' , id), 5)) as sid"))->where('id', $agent_id)->get();
            $sid = $result[0]->sid;

            // 如果没有sid值，那么就报错
            if (!$sid) {
                throw new \Exception('生成合伙人ID失败');
            }
            if (!$agent->update(compact('sid'))) {
                throw new \Exception('写入合伙人ID失败');
            }

            // 把银行卡添加进cards表
            // 后台添加的为默认卡
            if (!empty($card_number)) {
                $isdefault = 1;
                if (!Card::create(compact('agent_id', 'isdefault', 'bank_id', 'branch', 'card_number'))) {
                    throw new \Exception('写入用户卡号表失败');
                }
            }

            // 如果agentaccount表没有这个用户，那么就新增
            if (!AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                'frozen_money' => '0.00',
                'available_money' => '0.00',
                'sum_money' => '0.00',
            ])) {
                throw new \Exception('写入用户资产表失败');
            }

            // 如果存在openid，则增加缓存
            if ($agent->openid) {
                $this->agentapi->createAgentCache($agent->openid);
            }

            // 提交
            DB::commit();

            // 成功返回
            $data = [
                'code' => '0',
                'msg' => '合伙人添加成功',
            ];
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
     * 合伙人单个显示
     */
    public function show($id)
    {
        // 逻辑
        $agent = Agent::find($id);

        // 数据加工
        if ($agent->openid == 'null' || $agent->openid == 'NULL' || !$agent->openid) {
            $agent->openid = '';
        }
        // 如果不小心赋值为null，那么就为空
        // 这个赋值为null，和上面的空值区分开来
        if ($agent->parentopenid == 'null' || $agent->parentopenid == 'NULL') {
            $agent->parentopenid = null;
        }

        // 渲染
        $page_title = '合伙人详情';
        return view('admin.agent.show', compact('agent', 'page_title'));
    }

    /**
     * 合伙人编辑
     */
    public function edit($id)
    {
        // 取出数据
        $agent = Agent::find($id);

        // 渲染
        $page_title = '合伙人编辑';
        // 判断有没有填写卡号
        $card = $agent->card;
        if (!empty($card)) {
            $cardinfo = BankCard::info($agent->card->card_number);
        } else {
            $cardinfo = null;
        }
        return view('admin.agent.edit', [
            'page_title' => $page_title,
            'agent' => $agent,
            'cardinfo' => $cardinfo,
        ]);
    }

    /**
     * 合伙人保存逻辑
     */
    public function update(Request $request, $id)
    {
        // 取出卡号所属的id
        $agent = Agent::find($id);
        // 卡号相关
        $card = $agent->card;
        // 验证
        $this->validate($request, [
            'sname' => 'required|string',
            'name' => 'required|string',
            // 'id_number' => 'required|unique:agents,id_number,'.$id,
            'mobile' => 'required|unique:agents,mobile,' . $id . '|regex:/^1[345678][0-9]{9}$/',
            // 'card_number' => 'required|unique:cards,card_number,'.$card->id,
        ]);

        // 逻辑
        // 取出原来的密码
        $p = $agent->password;
        // 新值
        $sname = request('sname');
        $name = request('name');
        $id_number = request('id_number');
        $mobile = request('mobile');
        $branch = request('branch');
        $card_number = request('card_number');
        $password = empty(request('password')) ? $p : bcrypt(request('password'));
        $branch = empty(request('branch')) ? '未填写' : request('branch');

        // 开启事务机制
        DB::beginTransaction();
        try {

            if (!empty($card_number)) {
                // 判断是否无效卡号
                $cardinfo = $this->checkcard($card_number);
                if ($cardinfo['validated'] == false) {
                    throw new \Exception('银行卡号无效，请重新输入！');
                }
                // 判断卡信息开户行是否存在
                $bank_id = Bank::firstOrCreate(['name' => $cardinfo['bankName']])->id;
            }

            // agent表
            if (!$agent->update(compact('sname', 'name', 'id_number', 'mobile', 'password'))) {
                throw new \Exception('更新agent表出错，请检查!');
            }

            // card表
            if ($card) {
                if (!$card->update(compact('branch', 'card_number', 'bank_id'))) {
                    throw new \Exception('更新card表出错，请检查!');
                }
            }

            // 如果存在openid，则增加缓存
            if ($agent->openid) {
                $this->agentapi->createAgentCache($agent->openid);
            }

            // 如果都无错，则提交
            DB::commit();

            // 返回
            $data = [
                'code' => '0',
                'msg' => '合伙人修改成功',
            ];
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
     * 合伙人删除
     */
    public function destroy($id)
    {
        //
    }

    /**
     * 合伙人审核通过
     */
    public function reviewsuccessed($id)
    {
        // 逻辑
        $agent = Agent::findOrFail($id);
        $result = $agent->update([
            'status' => '1',
        ]);

        if ($result == '1') {
            $data = [
                'code' => '0',
                'msg' => '审核通过',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => 'ID编号为' . $id . '的合伙人审核通过操作失败',
            ];
        }

        // 重新生成缓存
        if ($agent->openid) {
            $this->agentapi->createAgentCache($agent->openid);
        }

        // 返回
        return $data;

    }

    /**
     * 合伙人审核不通过
     */
    public function reviewfailed($id)
    {
        // 逻辑
        $agent = Agent::findOrFail($id);
        $result = $agent->update([
            'status' => '2',
        ]);
        if ($result == '1') {
            $data = [
                'code' => '0',
                'msg' => '审核不通过',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => 'ID编号为' . $id . '的合伙人审核不通过操作失败',
            ];
        }

        // 重新生成缓存
        if ($agent->openid) {
            $this->agentapi->createAgentCache($agent->openid);
        }

        // 返回
        return $data;
    }

    // 搜索
    public function search(Request $request)
    {
        // DB类搜索逻辑
        $agents = DB::table('agents as a')
            ->select(['a.id', 'a.sname', 'a.sid', 'a.name', 'a.method', 'a.created_at', 'a.updated_at', 'a.mobile', 'a.status', 'c.branch', 'c.card_number', 'a.id_number', 'c.bank_id', 'b.name as bank_name', 'a.openid', 'a.parentopenid', 'aa.available_money'])
            ->leftJoin('cards as c', 'c.agent_id', '=', 'a.id')
            ->leftJoin('banks as b', 'b.id', '=', 'c.bank_id')
            ->leftJoin('agent_accounts as aa', 'aa.agent_id', '=', 'a.id')
            ->where(function ($query) use ($request) {
                $name = $request->input('name');
                if (!empty($name)) {
                    $query->where('a.name', 'like binary', '%' . $name . '%');
                }
            })
            ->where(function ($query) use ($request) {
                $amount = $request->input('amount');
                if (!empty($amount)) {
                    // 等于1是取出余额大于0的数据
                    if ($amount == '1') {
                        $query->where('aa.available_money', '>', '0');
                    } else {
                        $query->where('aa.available_money', '0');
                    }
                }
            })
            ->where(function ($query) use ($request) {
                $mobile = $request->input('mobile');
                if (!empty($mobile)) {
                    $query->where('a.mobile', $mobile);
                }
            })
            ->where(function ($query) use ($request) {
                $sid = $request->input('sid');
                if (!empty($sid)) {
                    $query->where('a.sid', $sid);
                }
            })
            ->where(function ($query) use ($request) {
                $start_time = $request->input('start_time');
                if (!empty($start_time)) {
                    $query->where('a.created_at', '>=', $start_time);
                }
            })
            ->where(function ($query) use ($request) {
                $end_time = $request->input('end_time');
                if (!empty($end_time)) {
                    $query->where('a.created_at', '<=', $end_time);
                }
            })
            ->where(function ($query) use ($request) {
                $status = $request->get('status');
                if (isset($status)) {
                    $query->where('a.status', $status);
                }
            })
            ->orderBy('a.created_at', 'desc')
            ->paginate(10);


        // 总的合伙人层级表
        $format_agents = $this->agentapi->showtreeagents();

        // 因为模板要用ajax读取，所以这里对数据进行一下处理
        foreach ($agents as $k => $agent) {
            // 如果有卡片，那么就取出
            if (!$agent->card_number) {
                // 卡号
                $agents[$k]->card_number = '用户未录入';
                // 默认卡片的开户行
                $agents[$k]->bank_name = '用户未录入';
                // 支行
                $agents[$k]->branch = '用户未录入';
            }

            // 身份证号
            $agents[$k]->id_number = empty($agent->id_number) ? "用户未录入" : $agent->id_number;

            // 审核状态
            switch ($agent->status) {
                case '0':
                    $agents[$k]->status_name = '未审核';
                    break;
                case '1':
                    $agents[$k]->status_name = '审核通过';
                    break;
                case '2':
                    $agents[$k]->status_name = '审核未通过';
                    break;
                default:
                    $agents[$k]->status_name = '未审核';
            }

            // 注册途径
            switch ($agent->method) {
                case '1':
                    $agents[$k]->method_name = '后台添加';
                    break;
                case '2':
                    $agents[$k]->method_name = '办卡自动添加';
                    break;
                case '3':
                    $agents[$k]->method_name = '微信主动注册';
                    break;
                case '4':
                    $agents[$k]->method_name = '实名认证注册';
                    break;
                case '5':
                    $agents[$k]->method_name = '首页授权添加';
                    break;
                default:
                    $agents[$k]->method_name = '后台添加';
            }

            // DB查询不支持orm关联,重新写逻辑
            $agents[$k]->agentaccount = Agent::find($agent->id)->agentaccount;

            // 上级代理人
            // 如果不小心存入了null，那么就为空
            if ($agents[$k]->parentopenid == 'null' || $agents[$k]->parentopenid == 'NULL' || !$agents[$k]->parentopenid) {
                $agents[$k]->parentopenid = null;
            }

            if ($agents[$k]->parentopenid) {
                // 如果是null，那么就为空
                $parent_agent = Agent::where('openid', $agents[$k]->parentopenid)->first();
                // 如果上级合伙人不存在，说明被禁用了，那么上级合伙人无效
                if ($parent_agent) {
                    if ($parent_agent->name) {
                        $agents[$k]->parentopenid_name = $parent_agent->mobile . '（' . $parent_agent->name . '）';
                    } else {
                        $agents[$k]->parentopenid_name = $parent_agent->mobile;
                    }
                } else {
                    $agents[$k]->parentopenid_name = '无';
                }
            } else {
                $agents[$k]->parentopenid_name = '无';
            }

            // 如果openid为NULL，那么就赋值为空
            if ($agent->openid == 'null' || $agent->openid == 'NULL' || !$agent->openid) {
                $agent->openid = '';
            }

            // 查找每一个用户下面的下级代理
            foreach ($format_agents as $format_agent) {
                if ($format_agent['openid'] == $agent->openid) {
                    $agents[$k]->level = $format_agent['level'];
                }
            }
            $agents[$k]->second_level = $this->agentapi->formatagents($format_agents, $agent->openid, $agents[$k]->level + 1);
        }

        // 渲染
        $page_title = '搜索结果';
        $controller_action = $this->controller_action;

        return view('admin.agent.index', compact('page_title', 'agents', 'request', 'controller_action'));
    }


    /**
     * 判断卡号
     * @param $card 卡号
     * @return array
     */
    public function checkcard($card)
    {
        return BankCard::info($card);
    }


    /**
     * 合伙人复核审核通过(多条记录审核)
     */
    public function multisuccessed(Request $request)
    {
        // 验证
        $this->validate(request(), [
            'ids' => 'required|array',
        ]);

        // 逻辑
        $ids = $request->get('ids');
        // 所有合伙人记录集
        $agents = Agent::findMany($ids);

        // 因为涉及表单众多，所以采用事务机制
        DB::beginTransaction();
        try {
            // 循环操作
            foreach ($agents as $k => $agent) {
                // 如果状态不为0，说明已经审核完毕，无需审核
                if ($agent->status != '0') {
                    $data = [
                        'code' => '1',
                        'msg' => '序号为' . $agent->id . '的记录已经审核完毕，无需再次审核',
                    ];
                    return $data;
                }
                // 执行审核通过逻辑
                $agent->update([
                    'status' => '1',
                ]);
            }
            // 提交
            DB::commit();

            // json集合
            $data = [
                'code' => '0',
                'msg' => '批量审核通过',
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
     * 合伙人复核审核不通过(多条记录审核)
     */
    public function multifailed(Request $request)
    {
        // 验证
        $this->validate($request, [
            'ids' => 'required|array',
        ]);

        // 逻辑
        $ids = $request->get('ids');
        // 所有合伙人
        $agents = Agent::findMany($ids);

        // 因为涉及表单众多，所以采用事务机制
        DB::beginTransaction();
        try {

            foreach ($agents as $k => $agent) {
                // 如果状态不为0，说明已经审核完毕，无需审核
                if ($agent->status != '0') {
                    $data = [
                        'code' => '1',
                        'msg' => '序号为' . $agent->id . '的记录已经审核完毕，无需再次审核',
                    ];
                    return $data;
                }
                // 批量审核失败
                $agent->update([
                    'status' => '2',
                ]);
            }

            // 提交
            DB::commit();
            // json
            $data = [
                'code' => '0',
                'msg' => '批量审核不通过',
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
     * Excel文件导出功能
     */
    public function export(Request $request)
    {
        // 逻辑
        // DB类搜索逻辑
        $lists = DB::table('agents as a')
            ->select(['a.id', 'a.sname', 'a.sid', 'a.name', 'a.method', 'a.created_at', 'a.updated_at', 'a.mobile', 'a.status', 'c.branch', 'c.card_number', 'a.id_number', 'c.bank_id', 'b.name as bank_name', 'a.openid', 'a.parentopenid', 'aa.available_money'])
            ->leftJoin('cards as c', 'c.agent_id', '=', 'a.id')
            ->leftJoin('banks as b', 'b.id', '=', 'c.bank_id')
            ->leftJoin('agent_accounts as aa', 'aa.agent_id', '=', 'a.id')
            ->where(function ($query) use ($request) {
                $name = $request->input('name');
                if (!empty($name)) {
                    $query->where('a.name', 'like binary', '%' . $name . '%');
                }
            })
            ->where(function ($query) use ($request) {
                $amount = $request->input('amount');
                if (!empty($amount)) {
                    // 等于1是取出余额大于0的数据
                    if ($amount == '1') {
                        $query->where('aa.available_money', '>', '0');
                    } else {
                        $query->where('aa.available_money', '0');
                    }
                }
            })
            ->where(function ($query) use ($request) {
                $mobile = $request->input('mobile');
                if (!empty($mobile)) {
                    $query->where('a.mobile', $mobile);
                }
            })
            ->where(function ($query) use ($request) {
                $sid = $request->input('sid');
                if (!empty($sid)) {
                    $query->where('a.sid', $sid);
                }
            })
            ->where(function ($query) use ($request) {
                $start_time = $request->input('start_time');
                if (!empty($start_time)) {
                    $query->where('a.created_at', '>=', $start_time);
                }
            })
            ->where(function ($query) use ($request) {
                $end_time = $request->input('end_time');
                if (!empty($end_time)) {
                    $query->where('a.created_at', '<=', $end_time);
                }
            })
            ->where(function ($query) use ($request) {
                $status = $request->get('status');
                if (isset($status)) {
                    $query->where('a.status', $status);
                }
            })
            ->orderBy('a.created_at', 'desc')
            ->get();

        // 定义一个excel对象
        $cellData = [];

        // 数据处理逻辑
        foreach ($lists as $k => $list) {

            // 审核状态
            $status_name = '';
            if ($list->status == '0') {
                $status_name = '未审核';
            } elseif ($list->status == '1') {
                $status_name = '审核通过';
            } elseif ($list->status == '2') {
                $status_name = '审核未通过';
            } else {
                $status_name = '未知状态';
            }
            $lists[$k]->status = $status_name;

            // 注册途径
            $method = '';
            if ($list->method == '1') {
                $method = '管理员后台开户';
            } elseif ($list->method == '2') {
                $method = '办卡自动添加';
            } elseif ($list->method == '3') {
                $method = '微信主动注册';
            } elseif ($list->method == '4') {
                $method = '实名认证注册';
            } elseif ($list->method == '5') {
                $method = '首页授权添加';
            } else {
                $method = '其他途径';
            }
            $lists[$k]->method = $method;

            // excel对象赋值
            $cellData[] = [$list->sid, $list->sname, $list->name, $list->mobile, $method, $list->created_at, $status_name, $list->available_money];

        }

        // cellData头部插入标题
        array_unshift($cellData, ['ID', '简称', '姓名', '手机号', '注册途径', '注册时间', '审核状态', '分润余额']);

        // excel导出逻辑
        Excel::create('合伙人导出记录', function ($excel) use ($cellData) {
            $excel->sheet('合伙人导出记录', function ($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }


}
