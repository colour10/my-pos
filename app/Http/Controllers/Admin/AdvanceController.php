<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdvanceMethod;
use App\Models\Withdraw;
use Zhuzhichao\BankCardInfo\BankCard;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Agent\AgentauthController as AgentApi;

/**
 * 代付功能模块
 */
class AdvanceController extends Controller
{
    // 当前控制器/方法
    protected $controller_action;
    // 结算方式
    protected $methods;

    /**
     * 构造函数
     */
    public function __construct(AgentApi $agentapi)
    {
        // 控制器
        $this->controller_action = $this->getControllerAction();
        // 结算方式
        $this->methods = AdvanceMethod::select(['id', 'name'])->get();
        // agent
        $this->agentapi = $agentapi;
    }

    /**
     * 代付通道管理
     *
     * @return \Illuminate\Http\Response
     */
    public function method(Request $request)
    {
        // 逻辑，判断是否有搜索关键词
        $name = request('name');
        $methods = AdvanceMethod::select(['id', 'name', 'max', 'cost_rate', 'per_charge', 'status', 'created_at', 'updated_at'])->orderBy('created_at', 'asc')->where('name', 'like', '%' . $name . '%')->paginate(10);

        // 渲染
        $page_title = '代付通道管理';
        return view('admin.advance.method', compact('page_title', 'methods', 'name'));
    }

    /**
     * 代付通道创建
     *
     * @return \Illuminate\Http\Response
     */
    public function methodcreate()
    {
        // 渲染
        $page_title = '代付通道创建';
        return view('admin.advance.methodcreate', compact('page_title'));
    }

    /**
     * 代付通道创建逻辑
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function methodstore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|string|unique:advance_methods,name',
            'cost_rate' => 'required|numeric',
            'acctno' => 'required|string',
            // 'contract_rate' => 'required|numeric',
            'per_charge' => 'required|numeric',
            'max' => 'required|numeric',
            'username' => 'required|string',
            'password' => 'required|string',
            'merchant_id' => 'required|integer',
            'business_code' => 'required',
            'status' => 'required|integer',
        ]);

        // 逻辑
        $name = request('name');
        $max = request('max');
        $acctno = request('acctno');
        $cost_rate = request('cost_rate');
        $username = request('username');
        $password = request('password');
        $merchant_id = request('merchant_id');
        $business_code = request('business_code');
        // $contract_rate = request('contract_rate');
        $per_charge = request('per_charge');
        $status = request('status');
        $newid = AdvanceMethod::create(compact('name', 'cost_rate', 'per_charge', 'status', 'max', 'username', 'password', 'merchant_id', 'business_code', 'acctno'));

        // 判断
        if ($newid) {
            // 代付通道cache过期
            $this->agentapi->deleteAdvanceMethodCache();
            $data = [
                'code' => '0',
                'msg' => '代付通道添加成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '代付通道添加失败',
            ];
        }
        return $data;
    }

    /**
     * 代付通道显示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function methodshow($id)
    {
        //
    }

    /**
     * 代付通道修改
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function methodedit($id)
    {
        // 渲染
        $page_title = '代付通道修改';
        $model = AdvanceMethod::select(['id', 'name', 'max', 'cost_rate', 'per_charge', 'status', 'created_at', 'updated_at', 'username', 'password', 'merchant_id', 'business_code', 'acctno'])->find($id);

        if ($model) {
            return view('admin.advance.methodedit', compact('page_title', 'model'));
        } else {
            return view('errors.404');
        }
    }

    /**
     * 代付通道修改逻辑
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function methodupdate(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|string|unique:advance_methods,name,' . $id,
            'cost_rate' => 'required|numeric',
            // 'contract_rate' => 'required|numeric',
            'per_charge' => 'required|numeric',
            'max' => 'required|numeric',
            'username' => 'required|string',
            'password' => 'required|string',
            'merchant_id' => 'required|integer',
            'business_code' => 'required',
            'status' => 'required|integer',
            'acctno' => 'required|string',
        ]);

        // 逻辑
        $name = request('name');
        $max = request('max');
        $cost_rate = request('cost_rate');
        // $contract_rate = request('contract_rate');
        $per_charge = request('per_charge');
        $status = request('status');
        $username = request('username');
        $password = request('password');
        $merchant_id = request('merchant_id');
        $business_code = request('business_code');
        $acctno = request('acctno');

        $result = AdvanceMethod::find($id)->update(compact('name', 'cost_rate', 'per_charge', 'status', 'max', 'username', 'password', 'merchant_id', 'business_code', 'acctno'));

        // 判断
        if ($result) {
            // 代付通道cache过期
            $this->agentapi->deleteAdvanceMethodCache();
            $data = [
                'code' => '0',
                'msg' => '代付通道修改成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '代付通道修改失败',
            ];
        }
        return $data;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function methoddestroy($id)
    {
        $result = AdvanceMethod::destroy($id);
        if ($result == '1') {
            // 代付通道cache过期
            $this->agentapi->deleteAdvanceMethodCache();
            $data = [
                'code' => '0',
                'msg' => '删除成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '删除失败',
            ];
        }
        // 返回
        return $data;
    }

    /**
     * 提现记录
     */
    public function list(Request $request)
    {
        // 逻辑
        // DB类搜索逻辑
        $lists = DB::table('withdraws as w')
            ->select(['w.id', 'w.cash_id', 'w.agent_id', 'w.method_id', 'w.sum', 'w.charge', 'w.account', 'w.status', 'w.card_id', 'w.updated_at', 'a.mobile', 'a.sid', 'c.card_number as cardNumber', 'a.name as agentName', 'w.method_id', 'am.name as methodName', 'b.name as bankName', 'w.err_msg', 'w.err_code'])
            ->join('agents as a', 'w.agent_id', '=', 'a.id')
            ->join('advance_methods as am', 'w.method_id', '=', 'am.id')
            ->join('cards as c', 'w.card_id', '=', 'c.id')
            ->join('banks as b', 'c.bank_id', '=', 'b.id')
            ->orderBy('w.updated_at', 'desc')
            ->paginate(10);

        // 因为模板要用ajax读取，所以这里对数据进行一下处理
        foreach ($lists as $k => $list) {
            switch ($list->status) {
                case '0':
                    $lists[$k]->statusName = '结算中';
                    break;
                case '1':
                    $lists[$k]->statusName = '结算成功';
                    break;
                case '2':
                    $lists[$k]->statusName = '结算失败';
                    break;
                default:
                    $lists[$k]->status_name = '结算成功';
            }

            // // 卡类型，都是储蓄卡，不调用接口了，速度太慢
            // $cardinfo = BankCard::info($list->cardNumber);
            // // 判定是不是非法卡
            // if (empty($cardinfo['cardTypeName'])) {
            //     $lists[$k]->cardType = '非法卡';
            // } else {
            //     $lists[$k]->cardType = $cardinfo['cardTypeName'];
            // }

            $lists[$k]->cardType = '储蓄卡';
        }

        // 汇总逻辑
        $total_lists = DB::table('withdraws as w')
            ->select(DB::raw("sum(w.sum) as total_sum"))
            ->join('agents as a', 'w.agent_id', '=', 'a.id')
            ->join('advance_methods as am', 'w.method_id', '=', 'am.id')
            ->join('cards as c', 'w.card_id', '=', 'c.id')
            ->join('banks as b', 'c.bank_id', '=', 'b.id')
            ->get();

        // 总金额赋值
        $sum = empty($total_lists[0]->total_sum) ? 0 : $total_lists[0]->total_sum;

        // 渲染
        $page_title = '提现记录';
        $controller_action = $this->controller_action;
        $methods = $this->methods;
        return view('admin.advance.list', compact('page_title', 'lists', 'controller_action', 'request', 'methods', 'sum'));
    }

    // 搜索
    public function search(Request $request)
    {
        // DB类搜索逻辑
        $lists = DB::table('withdraws as w')
            ->select(['w.id', 'w.cash_id', 'w.agent_id', 'w.method_id', 'w.sum', 'w.charge', 'w.account', 'w.status', 'w.card_id', 'w.updated_at', 'a.mobile', 'a.sid', 'c.card_number as cardNumber', 'a.name as agentName', 'w.method_id', 'am.name as methodName', 'b.name as bankName', 'w.err_msg', 'w.err_code'])
            ->join('agents as a', 'w.agent_id', '=', 'a.id')
            ->join('advance_methods as am', 'w.method_id', '=', 'am.id')
            ->join('cards as c', 'w.card_id', '=', 'c.id')
            ->join('banks as b', 'c.bank_id', '=', 'b.id')
            ->where(function ($query) use ($request) {
                $name = $request->get('name');
                if (!empty($name)) {
                    $query->where('a.name', $name);
                }
            })
            ->where(function ($query) use ($request) {
                $method_id = $request->get('method_id');
                if (!empty($method_id)) {
                    $query->where('w.method_id', $method_id);
                }
            })
            ->where(function ($query) use ($request) {
                $mobile = $request->get('mobile');
                if (!empty($mobile)) {
                    $query->where('a.mobile', $mobile);
                }
            })
            ->where(function ($query) use ($request) {
                $sid = $request->get('sid');
                if (!empty($sid)) {
                    $query->where('a.sid', $sid);
                }
            })
            ->where(function ($query) use ($request) {
                $start_time = $request->get('start_time');
                if (!empty($start_time)) {
                    $query->where('w.created_at', '>=', $start_time);
                }
            })
            ->where(function ($query) use ($request) {
                $end_time = $request->get('end_time');
                if (!empty($end_time)) {
                    $query->where('w.created_at', '<=', $end_time);
                }
            })
            ->where(function ($query) use ($request) {
                $status = $request->get('status');
                if (isset($status)) {
                    $query->where('w.status', $status);
                }
            })
            ->orderBy('w.updated_at', 'desc')
            ->paginate(10);

        // 因为模板要用ajax读取，所以这里对数据进行一下处理
        foreach ($lists as $k => $list) {
            switch ($list->status) {
                case '0':
                    $lists[$k]->statusName = '结算中';
                    break;
                case '1':
                    $lists[$k]->statusName = '结算成功';
                    break;
                case '2':
                    $lists[$k]->statusName = '结算失败';
                    break;
                default:
                    $lists[$k]->status_name = '结算成功';
            }

            // // 卡类型，用接口读取太慢，废弃
            // $cardinfo = BankCard::info($list->cardNumber);

            // // 判定是不是非法卡
            // if (empty($cardinfo['cardTypeName'])) {
            //     $lists[$k]->cardType = '非法卡';
            // } else {
            //     $lists[$k]->cardType = $cardinfo['cardTypeName'];
            // }

            // 卡类型，都是储蓄卡，不调用接口了，速度太慢
            $lists[$k]->cardType = '储蓄卡';
        }

        // 汇总逻辑
        $total_lists = DB::table('withdraws as w')
            ->select(DB::raw("sum(w.sum) as total_sum"))
            ->join('agents as a', 'w.agent_id', '=', 'a.id')
            ->join('advance_methods as am', 'w.method_id', '=', 'am.id')
            ->join('cards as c', 'w.card_id', '=', 'c.id')
            ->join('banks as b', 'c.bank_id', '=', 'b.id')
            ->where(function ($query) use ($request) {
                $name = $request->get('name');
                if (!empty($name)) {
                    $query->where('a.name', 'like binary', '%' . $name . '%');
                }
            })
            ->where(function ($query) use ($request) {
                $method_id = $request->get('method_id');
                if (!empty($method_id)) {
                    $query->where('w.method_id', $method_id);
                }
            })
            ->where(function ($query) use ($request) {
                $mobile = $request->get('mobile');
                if (!empty($mobile)) {
                    $query->where('a.mobile', $mobile);
                }
            })
            ->where(function ($query) use ($request) {
                $sid = $request->get('sid');
                if (!empty($sid)) {
                    $query->where('a.sid', $sid);
                }
            })
            ->where(function ($query) use ($request) {
                $start_time = $request->get('start_time');
                if (!empty($start_time)) {
                    $query->where('w.created_at', '>=', $start_time);
                }
            })
            ->where(function ($query) use ($request) {
                $end_time = $request->get('end_time');
                if (!empty($end_time)) {
                    $query->where('w.created_at', '<=', $end_time);
                }
            })
            ->where(function ($query) use ($request) {
                $status = $request->get('status');
                if (isset($status)) {
                    $query->where('w.status', $status);
                }
            })
            ->get();

        // 总金额赋值
        $sum = empty($total_lists[0]->total_sum) ? 0 : $total_lists[0]->total_sum;

        // 渲染
        $page_title = '搜索结果';
        $controller_action = $this->controller_action;
        $methods = $this->methods;
        return view('admin.advance.list', compact('page_title', 'lists', 'request', 'controller_action', 'methods', 'sum'));
    }

    /**
     * 代付账户充值
     */
    public function recharge()
    {
        // 列表
        $page_title = '代付账户充值';
        return view('admin.advance.recharge', compact('page_title'));
    }


    /**
     * Excel文件导出功能
     */
    public function export(Request $request)
    {
        // 逻辑
        $lists = DB::table('withdraws as w')
            ->select(['w.id', 'w.cash_id', 'w.agent_id', 'w.method_id', 'w.sum', 'w.charge', 'w.account', 'w.status', 'w.card_id', 'w.updated_at', 'a.mobile', 'a.sid', 'c.card_number as cardNumber', 'a.name as agentName', 'w.method_id', 'am.name as methodName', 'b.name as bankName', 'w.err_msg', 'w.err_code'])
            ->join('agents as a', 'w.agent_id', '=', 'a.id')
            ->join('advance_methods as am', 'w.method_id', '=', 'am.id')
            ->join('cards as c', 'w.card_id', '=', 'c.id')
            ->join('banks as b', 'c.bank_id', '=', 'b.id')
            ->where(function ($query) use ($request) {
                $name = $request->get('name');
                if (!empty($name)) {
                    $query->where('a.name', 'like binary', '%' . $name . '%');
                }
            })
            ->where(function ($query) use ($request) {
                $method_id = $request->get('method_id');
                if (!empty($method_id)) {
                    $query->where('w.method_id', $method_id);
                }
            })
            ->where(function ($query) use ($request) {
                $mobile = $request->get('mobile');
                if (!empty($mobile)) {
                    $query->where('a.mobile', $mobile);
                }
            })
            ->where(function ($query) use ($request) {
                $sid = $request->get('sid');
                if (!empty($sid)) {
                    $query->where('a.sid', $sid);
                }
            })
            ->where(function ($query) use ($request) {
                $start_time = $request->get('start_time');
                if (!empty($start_time)) {
                    $query->where('w.created_at', '>=', $start_time);
                }
            })
            ->where(function ($query) use ($request) {
                $end_time = $request->get('end_time');
                if (!empty($end_time)) {
                    $query->where('w.created_at', '<=', $end_time);
                }
            })
            ->where(function ($query) use ($request) {
                $status = $request->get('status');
                if (isset($status)) {
                    $query->where('w.status', $status);
                }
            })
            ->orderBy('w.updated_at', 'desc')
            ->get();

        // 定义一个excel对象
        $cellData = [];

        // 数据处理逻辑
        foreach ($lists as $k => $list) {

            // 状态
            $status = '';
            if ($list->status === 0) {
                $status = '结算中';
            } elseif ($list->status === 1) {
                $status = '结算成功';
            } elseif ($list->status === 2) {
                $status = '结算失败';
            }
            $lists[$k]->status = $status;

            // excel对象赋值
            $cellData[] = [$list->cash_id, $list->sid, $list->agentName, $list->mobile, $list->sum, $list->charge, $list->account, $list->updated_at, $status, $list->bankName, "\t" . $list->cardNumber, '储蓄卡', $list->err_code, $list->err_msg];

        }

        // cellData头部插入标题
        array_unshift($cellData, ['结算订单号', '合伙人ID', '合伙人姓名', '手机号', '结算金额', '手续费', '到账金额', '结算时间', '结算状态', '结算银行', '结算卡号', '卡类型', '错误代码', '错误详情']);

        // excel导出逻辑
        Excel::create('分润提现记录', function ($excel) use ($cellData) {
            $excel->sheet('分润提现记录', function ($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

}
