<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Agent;
use App\Model\Cardbox;
use App\Model\ApplyCard;
use Illuminate\Support\Facades\DB;
use App\Model\Finance;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Agent\AgentauthController;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    // 当前控制器/方法
    protected $controller_action;
    protected $agent_auth;
    protected $request;

    // 构造函数
    public function __construct(Request $request)
    {
        // request注入
        $this->request = $request;

        // 控制器
        $this->controller_action = $this->getControllerAction();

        // agent_auth
        $this->agent_auth = new AgentauthController($this->request);
    }


    /**
     * 办卡银行模块-首页&&搜索
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cardboxindex(Request $request)
    {
        // DB类搜索逻辑
        $cardboxes = DB::table('cardboxes')
                    ->select(['id', 'merCardName', 'merCardImg', 'cardAmount', 'created_at', 'updated_at', 'littleFlag', 'status', 'sort', 'creditCardUrl', 'cardBankAmount', 'cardTopAmount', 'source'])
                    ->where(function ($query) use ($request) {
                        $merCardName = $request->get('merCardName');
                        if (!empty($merCardName)) {
                            $query->where('merCardName', 'like binary', '%'.$merCardName.'%');
                        }
                    })
                    ->where(function ($query) use ($request) {
                        $status = $request->get('status');
                        if (isset($status)) {
                            $query->where('status', $status);
                        }
                    })
                    ->orderBy('sort', 'desc')
                    ->paginate(10);

        // 渲染
        $page_title = '办卡银行';
        $controller_action = $this->controller_action;
        return view('admin.products.cardbox.index', compact('page_title', 'cardboxes', 'request', 'controller_action'));
    }


    /**
     * 办卡银行模块-创建
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cardboxcreate()
    {
        // 渲染
        $page_title = '办卡银行创建';
        return view('admin.products.cardbox.create', compact('page_title'));
    }



    /**
     * 办卡银行模块-新增逻辑
     * @param Request $request
     * @return array
     */
    public function cardboxstore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'merCardName' => 'required|string',
            'merCardImg' => 'required',
            // 'merCardJinduImg' => 'required',
            // 'merCardOrderImg' => 'required',
            'cardAmount' => 'required',
            'cardTopAmount' => 'required',
            'cardBankAmount' => 'required',
            'creditCardUrl' => 'required|string',
            'littleFlag' => 'required|string',
            'creditCardJinduUrl' => 'required|string',
            'source' => 'required|string',
        ]);

        // 逻辑
        $merCardName = request('merCardName');
        $cardContent = request('cardContent');
        $cardAmount = request('cardAmount');
        $cardTopAmount = request('cardTopAmount');
        $cardBankAmount = request('cardBankAmount');
        $creditCardUrl = request('creditCardUrl');
        $littleFlag = request('littleFlag');
        $creditCardJinduUrl = request('creditCardJinduUrl');
        $source = request('source');
        $rate = request('rate');
        $method = request('method');

        // 新增逻辑
        // 上传图片
        // 办卡封面
        if (!empty($request->file('merCardImg'))) {
            $path = $request->file('merCardImg')->storePublicly(date('Ymd'));
            $merCardImg = '/storage/'.$path;
        }

        // 广告封面
        if (!empty($request->file('advertiseImg'))) {
            $path = $request->file('advertiseImg')->storePublicly(date('Ymd'));
            $advertiseImg = '/storage/'.$path;
        }

        $orderpath = $request->file('merCardOrderImg')->storePublicly(date('Ymd'));
        $merCardOrderImg = '/storage/'.$orderpath;

        $cardbox = Cardbox::create(compact('merCardName', 'merCardImg', 'advertiseImg', 'cardAmount', 'creditCardUrl', 'littleFlag', 'creditCardJinduUrl', 'cardContent', 'cardBankAmount', 'cardTopAmount', 'source', 'rate', 'method', 'merCardOrderImg'));

        if ($cardbox) {
            // 排序字段
            $new_id = $cardbox->id;
            $sort = 10 * $new_id;
            $cardbox->update(compact('sort'));

            // 重写缓存
            Cache::forever('cardboxes', $this->agent_auth->getCardboxesList());
            
            $data = [
                'code' => '0',
                'msg' => '卡片添加成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '卡片添加失败',
            ];
        }
        return $data;
    }
    


    /**
     * 办卡银行模块-编辑页面
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cardboxedit($id)
    {
        // 逻辑
        $cardbox = Cardbox::find($id);
        // 渲染
        $page_title = '编辑办卡银行';
        return view('admin.products.cardbox.edit', compact('page_title', 'cardbox'));
    }



    /**
     * 办卡银行模块-更新逻辑
     * @param Request $request
     * @param $id
     * @return array
     */
    public function cardboxupdate(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'merCardName' => 'required|string',
            'cardAmount' => 'required',
            'cardTopAmount' => 'required',
            'cardBankAmount' => 'required',
            'creditCardUrl' => 'required|string',
            'littleFlag' => 'required|string',
            'creditCardJinduUrl' => 'required|string',
            'sort' => 'required|integer',
            'source' => 'required|string',
        ]);

        // 逻辑
        $cardbox = Cardbox::find($id);
        $merCardName = request('merCardName');
        $cardContent = request('cardContent');
        $cardAmount = request('cardAmount');
        $cardTopAmount = request('cardTopAmount');
        $cardBankAmount = request('cardBankAmount');
        $creditCardUrl = request('creditCardUrl');
        $littleFlag = request('littleFlag');
        $sort = request('sort');
        $creditCardJinduUrl = request('creditCardJinduUrl');
        $source = request('source');
        $rate = request('rate');
        $method = request('method');

        // 新增逻辑
        // 办卡封面
        if (!empty($request->file('merCardImg'))) {
            $path = $request->file('merCardImg')->storePublicly(date('Ymd'));
            $merCardImg = '/storage/'.$path;
        } else {
            $merCardImg = $cardbox->merCardImg;
        }

        // 广告封面
        if (!empty($request->file('advertiseImg'))) {
            $path = $request->file('advertiseImg')->storePublicly(date('Ymd'));
            $advertiseImg = '/storage/'.$path;
        } else {
            $advertiseImg = $cardbox->advertiseImg;
        }

        // 卡片订单封面
        if (!empty($request->file('merCardOrderImg'))) {
            $orderpath = $request->file('merCardOrderImg')->storePublicly(date('Ymd'));
            $merCardOrderImg = '/storage/'.$orderpath;
        } else {
            $merCardOrderImg = $cardbox->merCardOrderImg;
        }

        $result = $cardbox->update(compact('merCardName', 'merCardImg', 'advertiseImg', 'cardAmount', 'creditCardUrl', 'littleFlag', 'creditCardJinduUrl', 'cardContent', 'sort', 'cardBankAmount', 'cardTopAmount', 'source', 'rate', 'method', 'merCardOrderImg'));

        if ($result) {

            // 重写缓存
            Cache::forever('cardboxes', $this->agent_auth->getCardboxesList());

            $data = [
                'code' => '0',
                'msg' => '卡片修改成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '卡片修改失败',
            ];
        }
        return $data;
    }



    /**
     * 办卡银行模块-单个查看【接口】
     * @param $id
     * @return array
     */
    public function cardboxshow($id)
    {
        // 逻辑
        $cardbox = Cardbox::find($id);
        // 返回
        if ($cardbox) {
            $response = [
                'code' => '0',
                'data' => $cardbox,
            ];
        } else {
            $response = [
                'code' => '1',
                'data' => $cardbox,
            ];
        }
        return $response;
    }


    /**
     * 办卡银行模块-单个删除
     * @param $id
     * @return array
     */
    public function cardboxdestroy($id)
    {
        // 执行删除
        $result = Cardbox::destroy($id);
        if ($result) {

            // 重写缓存
            Cache::forever('cardboxes', $this->agent_auth->getCardboxesList());

            $response = [
                'code' => '0',
                'msg' => '删除成功',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg' => '删除失败',
            ];
        }
        // 最终返回
        return $response;
    }



    /**
     * 办卡银行模块-批量删除
     * @param Request $request
     * @return array
     */
    public function cardboxdestroys(Request $request)
    {
        // 验证
        $this->validate($request, [
            'ids' => 'required|array',
        ]);
        // 逻辑
        $ids = $request->get('ids');
        // 执行删除
        $result = Cardbox::destroy($ids);
        if ($result) {

            // 重写缓存
            Cache::forever('cardboxes', $this->agent_auth->getCardboxesList());

            $response = [
                'code' => '0',
                'msg' => '批量删除成功',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg' => '批量删除失败',
            ];
        }
        // 最终返回
        return $response;
    }


    /**
     * 办卡银行模块-批量禁用（接口）
     * @param Request $request
     * @return array\
     */
    public function cardboxdisables(Request $request)
    {
        // 验证
        $this->validate($request, [
            'ids' => 'required|array',
        ]);
        // 逻辑
        $ids = request('ids');
        $cardboxes = Cardbox::findMany($ids);
        $status = '0';
        foreach ($cardboxes as $cardbox) {
            $result = $cardbox->update(compact('status'));
        }
        // 返回
        if ($result) {

            // 重写缓存
            Cache::forever('cardboxes', $this->agent_auth->getCardboxesList());

            $response = [
                'code' => '0',
                'msg' => '批量禁用成功',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg' => '批量禁用失败',
            ];
        }
        return $response;
    }


    /**
     * 办卡银行模块-批量启用（接口）
     * @param Request $request
     * @return array
     */
    public function cardboxenables(Request $request)
    {
        // 验证
        $this->validate($request, [
            'ids' => 'required|array',
        ]);
        // 逻辑
        $ids = request('ids');
        $cardboxes = Cardbox::findMany($ids);
        $status = '1';
        foreach ($cardboxes as $cardbox) {
            $result = $cardbox->update(compact('status'));
        }
        // 返回
        if ($result) {

            // 重写缓存
            Cache::forever('cardboxes', $this->agent_auth->getCardboxesList());

            $response = [
                'code' => '0',
                'msg' => '批量启用成功',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg' => '批量启用失败',
            ];
        }
        return $response;
    }


    /**
     * 待审核信用卡申请记录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function applycardsindex(Request $request)
    {
        // DB类搜索逻辑
        $applycards = DB::table('apply_cards as ac')
                    ->select(['ac.id', 'ac.order_id', 'ac.order_id', 'ac.card_id', 'a.name', 'a.mobile', 'ac.status', 'ac.created_at', 'ac.updated_at', 'c.merCardName', 'c.source',  'ac.invite_money', 'ac.top_money', 'ac.invite_openid', 'ac.top_openid', 'a.id_number'])
                    ->leftJoin('agents as a', 'a.openid', '=', 'ac.user_openid')
                    ->leftJoin('cardboxes as c', 'c.id', '=', 'ac.card_id')
                    ->where(function ($query) use ($request) {
                        $method = $request->method;
                        $applyer = $request->applyer;
                        if (!empty($method)) {
                            if ($method == '1') {
                                $query->where('a.name', 'like', '%'.$applyer.'%');
                            } elseif ($method == '2') {
                                $query->where('a.mobile', 'like', '%'.$applyer.'%');
                            } elseif ($method == '3') {
                                $query->where('a.id_number', 'like', '%'.$applyer.'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($request) {
                        $merCardName = $request->merCardName;
                        if (!empty($merCardName)) {
                            $query->where('c.merCardName', $merCardName);
                        }
                    })
                    ->where('ac.status', '0')
                    ->orderBy('ac.created_at', 'desc')
                    ->paginate(10);

        // 数据加工
        foreach ($applycards as $k => $applycard) {
            // 邀请人和邀请人上级信息
            if ($applycard->invite_openid) {
                $invite_agent = Agent::where('openid', $applycard->invite_openid)->first();
                if ($invite_agent) {
                    $parent_name = $invite_agent->name ? $invite_agent->name : '';
                } else {
                    $parent_name = '';
                }
            } else {
                $parent_name = '';
            }
            if ($applycard->top_openid) {
                $top_agent = Agent::where('openid', $applycard->top_openid)->first();
                if ($top_agent) {
                    $top_name = $top_agent->name ? $top_agent->name : '';
                } else {
                    $top_name = '';
                }
            } else {
                $top_name = '';
            }
            $applycards[$k]->parent_name = $parent_name;
            $applycards[$k]->top_name = $top_name;
        }

        // 渲染
        $page_title = '待审核信用卡申请记录';
        $controller_action = $this->controller_action;
        $card_status = '0';
        return view('admin.products.applycards.index', compact('page_title', 'applycards', 'request', 'controller_action', 'card_status'));
    }



    /**
     * 已完毕信用卡申请记录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function applycardsfinished(Request $request)
    {
        // DB类搜索逻辑
        $applycards = DB::table('apply_cards as ac')
                    ->select(['ac.id', 'ac.order_id', 'ac.order_id', 'ac.card_id', 'a.name', 'a.mobile', 'ac.status', 'ac.created_at', 'ac.updated_at', 'c.merCardName', 'c.source', 'ac.invite_money', 'ac.top_money', 'ac.invite_openid', 'ac.top_openid', 'ac.invite_openid', 'ac.top_openid', 'a.id_number'])
                    ->leftJoin('agents as a', 'a.openid', '=', 'ac.user_openid')
                    ->leftJoin('cardboxes as c', 'c.id', '=', 'ac.card_id')
                    ->where(function ($query) use ($request) {
                        $method = $request->method;
                        $applyer = $request->applyer;
                        if (!empty($method)) {
                            if ($method == '1') {
                                $query->where('a.name', 'like', '%'.$applyer.'%');
                            } elseif ($method == '2') {
                                $query->where('a.mobile', 'like', '%'.$applyer.'%');
                            } elseif ($method == '3') {
                                $query->where('a.id_number', 'like', '%'.$applyer.'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($request) {
                        $merCardName = $request->merCardName;
                        if (!empty($merCardName)) {
                            $query->where('c.merCardName', 'like', '%'.$merCardName.'%');
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->status) {
                            $query->where('ac.status', $request->status);
                        } else {
                            $query->where('ac.status', '>', '0');
                        }
                    })
                    ->orderBy('ac.created_at', 'desc')
                    ->paginate(10);

        // 数据加工
        foreach ($applycards as $k => $applycard) {
            // 邀请人和邀请人上级信息
            if ($applycard->invite_openid) {
                $parent_name = Agent::where('openid', $applycard->invite_openid)->first()->name;
            } else {
                $parent_name = '';
            }
            if ($applycard->top_openid) {
                $top_name = Agent::where('openid', $applycard->top_openid)->first()->name;
            } else {
                $top_name = '';
            }
            $applycards[$k]->parent_name = $parent_name;
            $applycards[$k]->top_name = $top_name;
        }

        // 渲染
        $page_title = '已完毕信用卡申请记录';
        $controller_action = $this->controller_action;
        $card_status = '1';
        return view('admin.products.applycards.index', compact('page_title', 'applycards', 'request', 'controller_action', 'card_status'));
    }



    /**
     * 卡片状态查看
     * @param $id
     * @return array
     */
    public function applycardsshow($id)
    {
        // 渲染
        $page_title = '卡片状态查看';
        // DB类搜索逻辑
        $applycard = DB::table('apply_cards as ac')
                    ->select(['ac.id', 'ac.order_id', 'ac.order_id', 'ac.card_id', 'a.name', 'a.mobile', 'ac.status', 'ac.created_at', 'ac.updated_at', 'c.merCardName', 'c.source', 'ac.invite_money', 'ac.top_money', 'a.id_number'])
                    ->leftJoin('agents as a', 'a.openid', '=', 'ac.user_openid')
                    ->leftJoin('cardboxes as c', 'c.id', '=', 'ac.card_id')
                    ->where('ac.id', $id)
                    ->first();

        // 格式化数据
        switch ($applycard->status) {
            case '0':
                $applycard->status_name = '审核中';
                break;
            case '1':
                $applycard->status_name = '通过';
                break;
            case '2':
                $applycard->status_name = '未通过';
                break;
            case '3':
                $applycard->status_name = '无记录';
                break;
        }
        // 组装数据
        $response = [
            'code' => '0',
            'data' => $applycard,
        ];
        // 返回
        return $response;
    }


    /**
     * 卡片状态修改
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function applycardsedit($id)
    {
        // 渲染
        $page_title = '卡片状态修改';
        $applycard = ApplyCard::findOrFail($id);
        return view('admin.products.applycards.edit', compact('applycard', 'page_title'));
    }

    /**
     * 卡片状态修改逻辑
     */
    public function applycardsupdate(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'status' => 'required',
        ]);

        // 逻辑
        $status = $request->status;
        $applycard = Applycard::findOrFail($id);
        if ($applycard->update(compact('status'))) {
            $response = [
                'code' => '0',
                'msg' => '卡片审核成功',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg' => '卡片审核失败',
            ];
        }
        return $response;
    }


    /**
     * 卡片批量审核通过
     * @param Request $request
     * @return array
     */
    public function applycardsenables(Request $request)
    {
        // 验证
        $this->validate($request, [
            'ids' => 'required|array',
        ]);
        // 逻辑
        $ids = request('ids');
        $applycards = ApplyCard::findMany($ids);

        // 采用事务处理机制
        DB::beginTransaction();
        try {
            // 批量
            foreach ($applycards as $applycard) {
                // 通过
                $status = '1';
                if (!$applycard->update(compact('status'))) {
                    throw new \Exception('编号为'.$applycard->id.'的信用卡申请通过操作失败了，请知悉...');
                }
                // 新增一条分润记录，系统自动分配
                // 合伙人id
                // 如果推荐人为空，那么就无需进行下面的分润操作
                if (!empty($applycard->invite_openid)) {
                    $agent = Agent::where('openid', $applycard->invite_openid)->first();
                    if (!$agent) {
                        throw new \Exception('微信openid为：'.$applycard->invite_openid.'的推荐人还不是合伙人，不能为其分润，请知悉...');
                    }
                    // 参数组装
                    $agent_id = $agent->id;
                    $type = '1';
                    $amount = $applycard->invite_money;

                    // 推荐人名字
                    $agent_name = !$agent->name ? '空' : $agent->name;
                    // 申请人
                    $apply_agent = $applycard->agent;
                    // 申请人名字
                    $card_user_name = !$apply_agent->name ? '空' : $apply_agent->name;
                    // 申请人手机号
                    $card_user_phone = !$apply_agent->mobile ? '空' : $apply_agent->mobile;
                    // 申请卡名字
                    $card_name = $applycard->cardbox->merCardName;
                    // 推荐人佣金
                    $invite_money = !$applycard->invite_money ? '0.00' : $applycard->invite_money;
                    // 推荐人上级佣金
                    $top_money = !$applycard->top_money ? '0.00' : $applycard->top_money;
                    // 订单号
                    $order_id = $applycard->order_id;

                    // $description = '成功推荐了 '.$applycard->user_name.' '.$applycard->user_phone.' 办理了 '.$applycard->cardbox->merCardName.' 信用卡';

                    // 前面的1代表来自一级合伙人的佣金
                    $description = '1----'.$agent_name.'----'.$card_user_name.'----'.$card_user_phone.'----'.$card_name.'----'.$invite_money.'----'.$order_id;

                    $account_type = '1';
                    // 如果分润>0，那么就新增一条分润记录
                    if ($amount > 0) {
                        if (!Finance::create(compact('agent_id', 'type', 'amount', 'description', 'account_type'))) {
                            throw new \Exception('添加新分润记录失败，当前操作失败的合伙人ID为：'.$agent_id);
                        }
                    }

                    // 推荐人上级分润
                    // 判断有没有上级
                    // 如果没有上级，就不用给上级发分润，也就没有下面的操作了
                    // 判断不能为空和null字符串
                    if (!empty($agent->parentopenid) && ($agent->parentopenid != 'null') && ($agent->parentopenid != 'NULL')) {
                        $parent_agent = Agent::where('openid', $agent->parentopenid)->first();
                        if (!$parent_agent) {
                            throw new \Exception('合伙人编号为：'.$agent_id.'的上级合伙人在数据库不存在！');
                        } else {
                            // 如果分润>0，那么就新增一条分润记录
                            if ($applycard->top_money > 0) {
                                // 如果存在上级合伙人，那么就新增一条上级合伙人分润记录
                                if (!Finance::create([
                                    'agent_id' => $parent_agent->id,
                                    'type' => '1',
                                    'amount' => $applycard->top_money,
                                    // 'description' => '您的下线'.$agent->name.'成功推荐了 '.$applycard->user_name.' '.$applycard->user_phone.' 办理了 '.$applycard->cardbox->merCardName.' 信用卡',

                                    // 前面的2代表来自二级合伙人的佣金
                                    'description' => '2----'.$agent_name.'----'.$card_user_name.'----'.$card_user_phone.'----'.$card_name.'----'.$top_money.'----'.$order_id,

                                    'creater' => null,
                                    'account_type' => '1',
                                ])) {
                                    throw new \Exception('添加合伙人上级新分润记录失败，当前操作失败的合伙人ID为：'.$parent_agent->id);
                                }
                            }
                        }
                    }
                }
            }

            // 提交
            DB::commit();

            // 成功返回
            $response = [
                'code' => '0',
                'msg' => '批量审核通过成功',
            ];
            return $response;
        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            // 返回
            $response = [
                'code' => '1',
                'msg' => $e->getMessage(),
            ];
            return $response;
        }
    }


    /**
     * 卡片批量审核不通过
     * @param Request $request
     * @return array
     */
    public function applycardsdisables(Request $request)
    {
        // 验证
        $this->validate($request, [
            'ids' => 'required|array',
        ]);
        // 逻辑
        $ids = request('ids');
        $applycards = ApplyCard::findMany($ids);
        $status = '2';

        // 采用事务处理机制
        DB::beginTransaction();
        try {
            // 批量
            foreach ($applycards as $applycard) {
                if (!$applycard->update(compact('status'))) {
                    throw new \Exception('编号为'.$applycard->id.'的信用卡申请不通过操作失败，请知悉...');
                }
            }

            // 提交
            DB::commit();

            // 成功返回
            $response = [
                'code' => '0',
                'msg' => '批量审核不通过成功',
            ];
            return $response;
        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            // 返回
            $response = [
                'code' => '1',
                'msg' => $e->getMessage(),
            ];
            return $response;
        }
    }



    /**
     * 卡片批量审核为无记录
     * @param Request $request
     * @return array
     */
    public function applycardsnorecords(Request $request)
    {
        // 验证
        $this->validate($request, [
            'ids' => 'required|array',
        ]);
        // 逻辑
        $ids = request('ids');
        $applycards = ApplyCard::findMany($ids);
        $status = '3';

        // 采用事务处理机制
        DB::beginTransaction();
        try {
            // 批量
            foreach ($applycards as $applycard) {
                if (!$applycard->update(compact('status'))) {
                    throw new \Exception('编号为'.$applycard->id.'的信用卡申请为无记录操作失败，请知悉...');
                }
            }

            // 提交
            DB::commit();

            // 成功返回
            $response = [
                'code' => '0',
                'msg' => '批量审核为无记录操作成功',
            ];
            return $response;
        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            // 返回
            $response = [
                'code' => '1',
                'msg' => $e->getMessage(),
            ];
            return $response;
        }
    }




    /**
     * 申卡记录删除
     */
    public function destroy($id)
    {
        //
    }

    /**
     * 卡片单条审核通过 [首卡]
     * @param $id
     * @return array
     */
    public function applycardsreviewfirstsuccessed($id)
    {
        // 逻辑
        $applycard = ApplyCard::findOrFail($id);
        // 采用事务处理机制
        DB::beginTransaction();
        try {
            // 审核通过
            $status = '1';
            if (!$applycard->update(compact('status'))) {
                throw new \Exception('审核失败');
            }

            // 新增一条分润记录，系统自动分配
            // 合伙人id
            // 如果推荐人为空，那么就无需进行下面的分润操作
            // 如果为首卡，就执行下面的代码
            if (!empty($applycard->invite_openid)) {
                $agent = Agent::where('openid', $applycard->invite_openid)->first();
                if (!$agent) {
                    throw new \Exception('微信openid为：'.$applycard->invite_openid.'的推荐人还不是合伙人，不能为其分润，请知悉...');
                }
                // 推荐人参数组装
                $agent_id = $agent->id;
                $type = '1';
                $amount = $applycard->invite_money;

                // 推荐人名字
                $agent_name = !$agent->name ? '空' : $agent->name;
                // 申请人
                $apply_agent = $applycard->agent;
                // 申请人名字
                $card_user_name = !$apply_agent->name ? '空' : $apply_agent->name;
                // 申请人手机号
                $card_user_phone = !$apply_agent->mobile ? '空' : $apply_agent->mobile;
                // 申请卡名字
                $card_name = $applycard->cardbox->merCardName;
                // 推荐人佣金
                $invite_money = !$applycard->invite_money ? '0.00' : $applycard->invite_money;
                // 推荐人上级佣金
                $top_money = !$applycard->top_money ? '0.00' : $applycard->top_money;
                // 订单号
                $order_id = $applycard->order_id;

                // 前面的1代表一级合伙人
                $description = '1----'.$agent_name.'----'.$card_user_name.'----'.$card_user_phone.'----'.$card_name.'----'.$invite_money.'----'.$order_id;

                // 系统虚拟账户，暂且为0
                // 推荐人分润
                $creater = null;
                $account_type = '1';

                // 如果分润>0，那么就新增一条分润记录
                if ($amount > 0) {
                    if (!Finance::create(compact('agent_id', 'type', 'amount', 'description', 'account_type'))) {
                        throw new \Exception('添加新分润记录失败，当前操作失败的合伙人ID为：'.$agent_id);
                    }
                }

                // 推荐人上级分润
                // 判断有没有上级
                // 如果没有上级，就不用给上级发分润，也就没有下面的操作了
                // 不能为null或NULL字符串
                if (!empty($agent->parentopenid) && ($agent->parentopenid != 'null') && ($agent->parentopenid != 'NULL')) {
                    $parent_agent = Agent::where('openid', $agent->parentopenid)->first();
                    if (!$parent_agent) {
                        throw new \Exception('合伙人编号为：'.$agent_id.'的上级合伙人在数据库不存在！');
                    } else {
                        // 如果分润>0，那么就新增一条分润记录
                        if ($applycard->top_money > 0) {
                            // 如果存在上级合伙人，那么就新增一条上级合伙人分润记录
                            if (!Finance::create([
                                'agent_id' => $parent_agent->id,
                                'type' => '1',
                                'amount' => $applycard->top_money,
                                // 'description' => '您的下线'.$agent->name.'成功推荐了 '.$applycard->user_name.' '.$applycard->user_phone.' 办理了 '.$applycard->cardbox->merCardName.' 信用卡',

                                // 前面的2代表来自二级合伙人的佣金
                                'description' => '2----'.$agent_name.'----'.$card_user_name.'----'.$card_user_phone.'----'.$card_name.'----'.$top_money.'----'.$order_id,

                                'creater' => null,
                                'account_type' => '1',
                            ])) {
                                throw new \Exception('添加合伙人上级新分润记录失败，当前操作失败的合伙人ID为：'.$parent_agent->id);
                            }
                        }
                    }
                }
            }

            // 提交
            DB::commit();

            // 成功返回
            $data = [
                'code' => '0',
                'msg' => '审核通过',
            ];
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
     * 卡片单条审核通过 [非首卡]
     * @param $id 卡片申请记录id
     * @return array
     */
    public function applycardsreviewsuccessed($id)
    {
        // 逻辑
        $applycard = ApplyCard::findOrFail($id);
        // 采用事务处理机制
        DB::beginTransaction();
        try {
            // 审核通过
            $status = '1';
            if (!$applycard->update(compact('status'))) {
                throw new \Exception('审核失败');
            }

            // 非首卡，不用分润，直接提交即可
            // 提交
            DB::commit();

            // 成功返回
            $data = [
                'code' => '0',
                'msg' => '审核通过',
            ];
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
     * 卡片单条审核不通过
     * @param $id
     * @return array
     */
    public function applycardsreviewfailed($id)
    {
        // 逻辑
        $applycard = ApplyCard::findOrFail($id);
        // 两条记录采用事务处理机制
        DB::beginTransaction();
        try {
            // 审核通过
            $status = '2';
            if (!$applycard->update(compact('status'))) {
                throw new \Exception('审核不通过操作失败');
            }

            // 提交
            DB::commit();

            // 成功返回
            $data = [
                'code' => '0',
                'msg' => '审核不通过',
            ];
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
     * 卡片单条审核-无记录
     * @param $id
     * @return array
     */
    public function applycardsreviewnorecord($id)
    {
        // 逻辑
        $applycard = ApplyCard::findOrFail($id);
        // 两条记录采用事务处理机制
        DB::beginTransaction();
        try {
            // 审核通过
            $status = '3';
            if (!$applycard->update(compact('status'))) {
                throw new \Exception('审核无记录操作失败');
            }

            // 提交
            DB::commit();

            // 成功返回
            $data = [
                'code' => '0',
                'msg' => '审核为无记录',
            ];
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
     * 取出cardboxes缓存 【测试】
     */
    public function getCardboxesCache()
    {
        // // 逻辑
        Cache::forget('cardboxes');
        // $cardboxes = Cardbox::select(['id', 'merCardName', 'merCardImg', 'merCardJinduImg', 'littleFlag', 'creditCardUrl', 'creditCardJinduUrl', 'cardAmount', 'rate', 'method', 'merCardOrderImg'])->where('status', '1')->orderBy('sort', 'desc')->get()->toArray();
        // // 写入session   
        // Session::put('cardboxes', $cardboxes);
        // // 返回
        // return Session::get('cardboxes');

        // echo '<pre>';
        // print_r(Session::get('cardboxes'));
        // echo '</pre>';
    }



    /**
     * Excel文件导出功能-未审核
     * @param Request $request
     */
    public function unauditedexport(Request $request)
    {
        // 逻辑
        // DB类搜索逻辑
        $applycards = DB::table('apply_cards as ac')
            ->select(['ac.id', 'ac.order_id', 'ac.order_id', 'ac.card_id', 'a.name', 'a.mobile', 'ac.status', 'ac.created_at', 'ac.updated_at', 'c.merCardName', 'c.source',  'ac.invite_money', 'ac.top_money', 'ac.invite_openid', 'ac.top_openid', 'a.id_number'])
            ->leftJoin('agents as a', 'a.openid', '=', 'ac.user_openid')
            ->leftJoin('cardboxes as c', 'c.id', '=', 'ac.card_id')
            ->where(function ($query) use ($request) {
                $method = $request->method;
                $applyer = $request->applyer;
                if (!empty($method)) {
                    if ($method == '1') {
                        $query->where('a.name', 'like', '%'.$applyer.'%');
                    } elseif ($method == '2') {
                        $query->where('a.mobile', 'like', '%'.$applyer.'%');
                    } elseif ($method == '3') {
                        $query->where('a.id_number', 'like', '%'.$applyer.'%');
                    }
                }
            })
            ->where(function ($query) use ($request) {
                $merCardName = $request->merCardName;
                if (!empty($merCardName)) {
                    $query->where('c.merCardName', $merCardName);
                }
            })           
            ->where('ac.status', '0')
            ->orderBy('ac.created_at', 'desc')
            ->get();

        // 定义一个excel对象
        $cellData = [];

        // 数据处理逻辑
        foreach ($applycards as $k => $applycard) {
            // 邀请人和邀请人上级信息
            if ($applycard->invite_openid) {
                $parent_Agent = Agent::where('openid', $applycard->invite_openid)->first();
                if ($parent_Agent) {
                    $parent_name = $parent_Agent->name;
                } else {
                    $parent_name = '';
                }
            } else {
                $parent_name = '';
            }
            if ($applycard->top_openid) {
                $top_agent = Agent::where('openid', $applycard->top_openid)->first();
                if ($top_agent) {
                    $top_name = $top_agent->name;
                } else {
                    $top_name = '';
                }
            } else {
                $top_name = '';
            }

            // 申请状态
            if ($applycard->status == '0') {
                $applycard->status_name = '审核中';
            } elseif ($applycard->status == '1') {
                $applycard->status_name = '通过';
            } elseif ($applycard->status == '2') {
                $applycard->status_name = '未通过';
            } elseif ($applycard->status == '3') {
                $applycard->status_name = '无记录';
            }

            $applycards[$k]->parent_name = $parent_name;
            $applycards[$k]->top_name = $top_name;

            // excel对象赋值
            $cellData[] = [$applycard->order_id, $applycard->merCardName, $applycard->source, $applycard->name, $applycard->mobile, $applycard->id_number."\t", $applycard->status_name, $applycard->created_at, $applycard->parent_name, $applycard->top_name, $applycard->invite_money, $applycard->top_money];
        }

        // cellData头部插入标题
        array_unshift($cellData, ['订单号','卡片名称','渠道来源','申请人', '申请人手机号', '申请人身份证','申请状态','申请时间','邀请人姓名','邀请人上级姓名','预计邀请人返佣','预计邀请人上级返佣']);

        // excel导出逻辑
        Excel::create('卡片申请列表', function($excel) use ($cellData) {
            $excel->sheet('卡片申请列表', function($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }


    /**
     * Excel文件导出功能-已经审核
     * @param Request $request
     */
    public function finishedexport(Request $request)
    {
        // 逻辑
        // DB类搜索逻辑
        $applycards = DB::table('apply_cards as ac')
            ->select(['ac.id', 'ac.order_id', 'ac.order_id', 'ac.card_id', 'a.name', 'a.mobile', 'ac.status', 'ac.created_at', 'ac.updated_at', 'c.merCardName', 'c.source',  'ac.invite_money', 'ac.top_money', 'ac.invite_openid', 'ac.top_openid', 'a.id_number'])
            ->leftJoin('agents as a', 'a.openid', '=', 'ac.user_openid')
            ->leftJoin('cardboxes as c', 'c.id', '=', 'ac.card_id')
            ->where(function ($query) use ($request) {
                $method = $request->method;
                $applyer = $request->applyer;
                if (!empty($method)) {
                    if ($method == '1') {
                        $query->where('a.name', 'like', '%'.$applyer.'%');
                    } elseif ($method == '2') {
                        $query->where('a.mobile', 'like', '%'.$applyer.'%');
                    } elseif ($method == '3') {
                        $query->where('a.id_number', 'like', '%'.$applyer.'%');
                    }
                }
            })
            ->where(function ($query) use ($request) {
                $merCardName = $request->merCardName;
                if (!empty($merCardName)) {
                    $query->where('c.merCardName', $merCardName);
                }
            })           
            ->where('ac.status', '>', '0')
            ->orderBy('ac.created_at', 'desc')
            ->get();

        // 定义一个excel对象
        $cellData = [];

        // 数据处理逻辑
        foreach ($applycards as $k => $applycard) {
            // 邀请人和邀请人上级信息
            if ($applycard->invite_openid) {
                $parent_Agent = Agent::where('openid', $applycard->invite_openid)->first();
                if ($parent_Agent) {
                    $parent_name = $parent_Agent->name;
                } else {
                    $parent_name = '';
                }
            } else {
                $parent_name = '';
            }
            if ($applycard->top_openid) {
                $top_agent = Agent::where('openid', $applycard->top_openid)->first();
                if ($top_agent) {
                    $top_name = $top_agent->name;
                } else {
                    $top_name = '';
                }
            } else {
                $top_name = '';
            }

            // 申请状态
            if ($applycard->status == '0') {
                $applycard->status_name = '审核中';
            } elseif ($applycard->status == '1') {
                $applycard->status_name = '通过';
            } elseif ($applycard->status == '2') {
                $applycard->status_name = '未通过';
            } elseif ($applycard->status == '3') {
                $applycard->status_name = '无记录';
            }

            $applycards[$k]->parent_name = $parent_name;
            $applycards[$k]->top_name = $top_name;

            // excel对象赋值
            $cellData[] = [$applycard->order_id, $applycard->merCardName, $applycard->source, $applycard->name, $applycard->mobile, $applycard->id_number."\t", $applycard->status_name, $applycard->created_at, $applycard->parent_name, $applycard->top_name, $applycard->invite_money, $applycard->top_money];       
        }

        // cellData头部插入标题
        array_unshift($cellData, ['订单号','卡片名称','渠道来源','申请人', '申请人手机号', '申请人身份证','申请状态','申请时间','邀请人姓名','邀请人上级姓名','预计邀请人返佣','预计邀请人上级返佣']);

        // excel导出逻辑
        Excel::create('卡片申请列表', function($excel) use ($cellData) {
            $excel->sheet('卡片申请列表', function($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
}
