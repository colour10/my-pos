<?php

namespace App\Models;

use App\Models;

/**
 * App\Models\Cardbox
 *
 * @property int $id 主键ID
 * @property string $merCardName 卡片名称
 * @property string $merCardImg 办卡封面图
 * @property string $advertiseImg 详情广告图
 * @property string|null $merCardJinduImg 进度封面图
 * @property string|null $merCardOrderImg 订单封面图
 * @property int|null $sort 排序
 * @property float|null $cardBankAmount 银行返佣金额
 * @property float|null $cardAmount 合伙人返佣金额
 * @property float|null $cardTopAmount 合伙人上级返佣金额
 * @property string|null $cardContent 卡片简介
 * @property string|null $creditCardUrl 办卡URL链接地址
 * @property string|null $littleFlag 办卡醒目标识，一般2-4个汉字之间
 * @property string|null $creditCardJinduUrl 查询卡片申请进度URL地址
 * @property int|null $status 卡片状态，0：禁用，1：启用
 * @property string $rate 下卡比率
 * @property string $method 结算方式
 * @property string $source 渠道来源
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereAdvertiseImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereCardAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereCardBankAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereCardContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereCardTopAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereCreditCardJinduUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereCreditCardUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereLittleFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereMerCardImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereMerCardJinduImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereMerCardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereMerCardOrderImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cardbox whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cardbox extends Model
{

}
