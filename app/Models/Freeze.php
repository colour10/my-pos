<?php

namespace App\Models;

/**
 * App\Models\Freeze
 *
 * @property int $id 主键ID
 * @property int $agent_id 合伙人ID
 * @property int $operater 操作人ID
 * @property int $account_type 账户类型
 * @property float $amount 调账金额
 * @property string $description 调账原因
 * @property int $status 开启状态，0：禁用，1：启用
 * @property \Illuminate\Support\Carbon|null $created_at 操作时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereOperater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Freeze whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Freeze extends Model
{
    //
}
