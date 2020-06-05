<?php

namespace App\Models;

/**
 * App\Models\AgentAccount
 *
 * @property int $id 主键ID
 * @property int $agent_id 合伙人ID
 * @property float $frozen_money 冻结资金
 * @property float $available_money 可用资金
 * @property float $cash_money 提现中资金
 * @property float $sum_money 账户总余额
 * @property \Illuminate\Support\Carbon|null $created_at 操作时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount whereAvailableMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount whereCashMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount whereFrozenMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount whereSumMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgentAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AgentAccount extends Model
{

}
