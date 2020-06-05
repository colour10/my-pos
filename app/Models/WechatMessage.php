<?php

namespace App\Models;

/**
 * App\Models\WechatMessage
 *
 * @property int $id 主键ID
 * @property string $ask_openid 提问者微信openid
 * @property string|null $ask_user 提问者微信用户信息
 * @property string|null $ask_msg 提问内容
 * @property string|null $answer_openid 回复者微信openid
 * @property string|null $answer_msg 回复内容
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage whereAnswerMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage whereAnswerOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage whereAskMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage whereAskOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage whereAskUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WechatMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WechatMessage extends Model
{
    //
}
