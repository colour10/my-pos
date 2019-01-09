<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Zhuzhichao\BankCardInfo\BankCard;
use App\Model\AdvanceMethod;
use Maatwebsite\Excel\Facades\Excel;

class BenefitController extends Controller
{

    // 当前控制器/方法
    protected $controller_action;

    // 结算方式
    protected $methods;

    /**
     * 构造函数
     */
    public function __construct()
    {
        // 控制器
        $this->controller_action = $this->getControllerAction();
        // 结算方式
        $this->methods = AdvanceMethod::select(['id', 'name'])->get();
    }

    /**
     * 分润明细查询
     */
    public function info()
    {
        // 列表
        $page_title = '分润明细查询';
        return view('admin.benefit.info', compact('page_title'));
    }

    /**
     * 分润提现记录
     */
    public function withdraw(Request $request)
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

            // // 卡类型
            // $cardinfo = BankCard::info($list->cardNumber);
            // // 判定是不是非法卡
            // if (empty($cardinfo['cardTypeName'])) {
            //     $lists[$k]->cardType = '非法卡';
            // } else {
            //     $lists[$k]->cardType = $cardinfo['cardTypeName'];
            // }

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
        $page_title = '分润提现记录';
        $controller_action = $this->controller_action;
        $methods = $this->methods;
        return view('admin.benefit.withdraw', compact('page_title', 'lists', 'controller_action', 'request', 'methods', 'sum'));
    }

    /**
     * 分润余额查询
     */
    public function balance()
    {
        // 列表
        $page_title = '分润余额查询';
        return view('admin.benefit.balance', compact('page_title'));
    }

    /**
     * 分润记录搜索
     */
    public function withdrawsearch(Request $request)
    {
        // DB类搜索逻辑
        $lists = DB::table('withdraws as w')
                    ->select(['w.id', 'w.cash_id', 'w.agent_id', 'w.method_id', 'w.sum', 'w.charge', 'w.account', 'w.status', 'w.card_id', 'w.updated_at', 'a.mobile', 'a.sid', 'c.card_number as cardNumber', 'a.name as agentName', 'w.method_id', 'am.name as methodName', 'b.name as bankName', 'w.err_msg', 'w.err_code'])
                    ->join('agents as a', 'w.agent_id', '=', 'a.id')
                    ->join('advance_methods as am', 'w.method_id', '=', 'am.id')
                    ->join('cards as c', 'w.card_id', '=', 'c.id')
                    ->join('banks as b', 'c.bank_id', '=', 'b.id')
                    ->where(function($query) use($request) {
                        $name = $request->get('name');
                        if (!empty($name)) {
                            $query->where('a.name', $name);
                        }
                    })
                    ->where(function($query) use($request) {
                        $method_id = $request->get('method_id');
                        if (!empty($method_id)) {
                            $query->where('w.method_id', $method_id);
                        }
                    })
                    ->where(function($query) use($request) {
                        $mobile = $request->get('mobile');
                        if (!empty($mobile)) {
                            $query->where('a.mobile', $mobile);
                        }
                    })
                    ->where(function($query) use($request) {
                        $sid = $request->get('sid');
                        if (!empty($sid)) {
                            $query->where('a.sid', $sid);
                        }
                    })
                    ->where(function($query) use($request) {
                        $start_time = $request->get('start_time');
                        if (!empty($start_time)) {
                            $query->where('w.created_at', '>=', $start_time);
                        }
                    })
                    ->where(function($query) use($request) {
                        $end_time = $request->get('end_time');
                        if (!empty($end_time)) {
                            $query->where('w.created_at', '<=', $end_time);
                        }
                    })
                    ->where(function($query) use($request) {
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

            // // 卡类型
            // $cardinfo = BankCard::info($list->cardNumber);

            // // 判定是不是非法卡
            // if (empty($cardinfo['cardTypeName'])) {
            //     $lists[$k]->cardType = '非法卡';
            // } else {
            //     $lists[$k]->cardType = $cardinfo['cardTypeName'];
            // }

            // 提现强制为储蓄卡
            $lists[$k]->cardType = '储蓄卡';
        }

        // 汇总逻辑
        $total_lists = DB::table('withdraws as w')
                    ->select(DB::raw("sum(w.sum) as total_sum"))
                    ->join('agents as a', 'w.agent_id', '=', 'a.id')
                    ->join('advance_methods as am', 'w.method_id', '=', 'am.id')
                    ->join('cards as c', 'w.card_id', '=', 'c.id')
                    ->join('banks as b', 'c.bank_id', '=', 'b.id')
                    ->where(function($query) use($request) {
                        $name = $request->get('name');
                        if (!empty($name)) {
                            $query->where('a.name', $name);
                        }
                    })
                    ->where(function($query) use($request) {
                        $method_id = $request->get('method_id');
                        if (!empty($method_id)) {
                            $query->where('w.method_id', $method_id);
                        }
                    })
                    ->where(function($query) use($request) {
                        $mobile = $request->get('mobile');
                        if (!empty($mobile)) {
                            $query->where('a.mobile', $mobile);
                        }
                    })
                    ->where(function($query) use($request) {
                        $sid = $request->get('sid');
                        if (!empty($sid)) {
                            $query->where('a.sid', $sid);
                        }
                    })
                    ->where(function($query) use($request) {
                        $start_time = $request->get('start_time');
                        if (!empty($start_time)) {
                            $query->where('w.created_at', '>=', $start_time);
                        }
                    })
                    ->where(function($query) use($request) {
                        $end_time = $request->get('end_time');
                        if (!empty($end_time)) {
                            $query->where('w.created_at', '<=', $end_time);
                        }
                    })
                    ->where(function($query) use($request) {
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
        return view('admin.benefit.withdraw', compact('page_title', 'lists', 'request', 'controller_action', 'methods', 'sum'));
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
                    ->where(function($query) use($request) {
                        $name = $request->get('name');
                        if (!empty($name)) {
                            $query->where('a.name', $name);
                        }
                    })
                    ->where(function($query) use($request) {
                        $method_id = $request->get('method_id');
                        if (!empty($method_id)) {
                            $query->where('w.method_id', $method_id);
                        }
                    })
                    ->where(function($query) use($request) {
                        $mobile = $request->get('mobile');
                        if (!empty($mobile)) {
                            $query->where('a.mobile', $mobile);
                        }
                    })
                    ->where(function($query) use($request) {
                        $sid = $request->get('sid');
                        if (!empty($sid)) {
                            $query->where('a.sid', $sid);
                        }
                    })
                    ->where(function($query) use($request) {
                        $start_time = $request->get('start_time');
                        if (!empty($start_time)) {
                            $query->where('w.created_at', '>=', $start_time);
                        }
                    })
                    ->where(function($query) use($request) {
                        $end_time = $request->get('end_time');
                        if (!empty($end_time)) {
                            $query->where('w.created_at', '<=', $end_time);
                        }
                    })
                    ->where(function($query) use($request) {
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
            $cellData[] = [$list->cash_id, $list->sid, $list->agentName, $list->mobile, $list->sum, $list->charge, $list->account, $list->updated_at, $status, $list->bankName, "\t".$list->cardNumber, '储蓄卡', $list->err_code, $list->err_msg];

        }

        // cellData头部插入标题
        array_unshift($cellData, ['结算订单号','合伙人ID','合伙人姓名','手机号', '结算金额', '手续费','到账金额','结算时间','结算状态','结算银行','结算卡号','卡类型','错误代码','错误详情']);

        // excel导出逻辑
        Excel::create('分润提现记录',function($excel) use ($cellData) {
            $excel->sheet('分润提现记录', function($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

}
