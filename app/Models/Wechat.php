<?php

namespace App\Models;

/**
 * App\Models\Wechat
 *
 * @property int $id 主键ID
 * @property string|null $aid 公众号对应id
 * @property string|null $wechat_app_id AppID
 * @property string|null $wechat_secret AppSecret
 * @property string|null $wechat_token Token
 * @property string|null $wechat_aes_key EncodingAESKey，兼容与安全模式下请一定要填写！！！
 * @property string|null $pay_mch_id
 * @property string|null $pay_api_key
 * @property string|null $pay_cert_path
 * @property string|null $pay_key_path
 * @property string|null $op_app_id
 * @property string|null $op_secret
 * @property string|null $op_token
 * @property string|null $op_aes_key
 * @property string|null $work_corp_id
 * @property string|null $work_agent_id
 * @property string|null $work_secret
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereAid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereOpAesKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereOpAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereOpSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereOpToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat wherePayApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat wherePayCertPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat wherePayKeyPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat wherePayMchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereWechatAesKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereWechatAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereWechatSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereWechatToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereWorkAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereWorkCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wechat whereWorkSecret($value)
 * @mixin \Eloquent
 */
class Wechat extends Model
{
    //
}
