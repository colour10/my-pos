<?php

namespace App\Models;

use App\Models;

/**
 * App\Models\Finance
 *
 * @property int $id 主键ID
 * @property int $agent_id 商户ID
 * @property int|null $creater 创建人ID
 * @property int|null $excel_id 从Excel文件中导入的序号ID
 * @property int $account_type 账户类型
 * @property int $type 调账类型，1：调入，2：调出
 * @property float $amount 调账金额
 * @property string $description 调账原因
 * @property int $status 财务审核状态,0：未审核,1：审核通过,2:审核失败
 * @property int|null $operater 审核人ID
 * @property string|null $operated_at 审核时间
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \App\Models\Agent $agent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereCreater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereExcelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereOperatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereOperater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Finance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Finance extends Model
{
    // 经办记录-合伙人，一对多反向
    public function agent()
    {
        return $this->belongsTo(\App\Models\Agent::class, 'id', 'agent_id');
    }
}
