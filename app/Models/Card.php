<?php

namespace App\Models;

/**
 * App\Models\Card
 *
 * @property int $id 主键ID
 * @property int $agent_id 合伙人ID
 * @property string $card_number 银行卡号
 * @property int $bank_id 开户行ID
 * @property string $branch 支行名称
 * @property int $isdefault 是否为默认结算卡片，0：非默认，1：默认
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \App\Models\Agent $agent
 * @property-read \App\Models\Bank $bank
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereIsdefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Card extends Model
{
    // 卡号-开户行，一对多反向
    public function bank()
    {
        return $this->belongsTo(\App\Models\Bank::class, 'bank_id', 'id');
    }

    // 卡号-合伙人，一对多反向
    public function agent()
    {
        return $this->belongsTo(\App\Models\Agent::class, 'agent_id', 'id');
    }
}
