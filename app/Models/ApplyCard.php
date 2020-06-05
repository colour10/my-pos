<?php

namespace App\Models;

/**
 * App\Models\ApplyCard
 *
 * @property int $id 主键ID
 * @property string $order_id 申请订单号
 * @property string $user_openid 申请人微信openid
 * @property int $card_id 申请卡片ID
 * @property string|null $invite_openid 邀请人微信openid
 * @property string|null $top_openid 邀请人上级微信openid
 * @property float $invite_money 邀请人返现佣金
 * @property float $top_money 邀请人上级返现佣金
 * @property string|null $user_name 申请人姓名
 * @property string|null $user_identity 申请人身份证号
 * @property string|null $user_phone 申请人手机号
 * @property int $status 卡片申请状态，0：审核中; 1：审核通过；2：审核不通过；3：无记录
 * @property \Illuminate\Support\Carbon|null $created_at 申请时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \App\Models\Agent $agent
 * @property-read \App\Models\Cardbox $cardbox
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereInviteMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereInviteOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereTopMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereTopOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereUserIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereUserOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplyCard whereUserPhone($value)
 * @mixin \Eloquent
 */
class ApplyCard extends Model
{
    // 申请卡片记录-合伙人，一对多反向
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'user_openid', 'openid');
    }

    // 申请记录-卡片，一对多反向
    public function cardbox()
    {
        return $this->belongsTo(Cardbox::class, 'card_id', 'id');
    }
}
