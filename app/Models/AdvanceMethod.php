<?php

namespace App\Models;

/**
 * App\Models\AdvanceMethod
 *
 * @property int $id 主键ID
 * @property string $name 支付通道名称
 * @property string|null $gateway 支付通道网关
 * @property string|null $acctno 支付通道账户号
 * @property string $username 支付通道登录用户名
 * @property string $password 支付通道登录密码
 * @property string $merchant_id 支付通道商户代码
 * @property string|null $bank_code 所属银行代码
 * @property string $business_code 业务类型
 * @property float $max 单笔最高金额
 * @property float $cost_rate 成本费率
 * @property float $contract_rate 签约费率
 * @property float $per_charge 单笔结算费用
 * @property int $status 开启状态，0：禁用，1：启用
 * @property \Illuminate\Support\Carbon|null $created_at 操作时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereAcctno($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereBankCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereBusinessCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereContractRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereCostRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod wherePerCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdvanceMethod whereUsername($value)
 * @mixin \Eloquent
 */
class AdvanceMethod extends Model
{
    //
}
