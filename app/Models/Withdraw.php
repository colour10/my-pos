<?php

namespace App\Models;

/**
 * App\Models\Withdraw
 *
 * @property int $id 主键ID
 * @property string $cash_id 结算订单号
 * @property int $agent_id 合伙人ID
 * @property int $method_id 结算通道ID
 * @property float $sum 结算金额
 * @property float $charge 手续费
 * @property float $account 实际到账金额
 * @property int $card_id 结算银行卡ID
 * @property string|null $remark 转账附言
 * @property int $status 结算状态，0：结算中，1：成功，2：失败
 * @property int|null $err_code 结算失败代码
 * @property string|null $err_msg 结算失败原因
 * @property \Illuminate\Support\Carbon|null $created_at 结算时间
 * @property \Illuminate\Support\Carbon|null $updated_at 成功时间
 * @property-read \App\Models\AdvanceMethod $advancemethod
 * @property-read \App\Models\Agent $agent
 * @property-read \App\Models\Card $card
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereCashId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereErrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereErrMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdraw whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Withdraw extends Model
{
    // 提现记录-合伙人，一对多反向
    public function agent()
    {
        return $this->belongsTo(\App\Models\Agent::class, 'agent_id', 'id');
    }

    // 提现记录-卡，一对多反向
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }

    // 提现记录-提现渠道，一对多反向
    public function advancemethod()
    {
        return $this->belongsTo(AdvanceMethod::class, 'method_id', 'id');
    }
}
