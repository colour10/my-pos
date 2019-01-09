<?php

namespace App\Model;

use App\Model;

class ApplyCard extends Model
{
    // 申请卡片记录-合伙人，一对多反向
    public function agent()
    {
        return $this->belongsTo(\App\Model\Agent::class, 'user_openid', 'openid');
    }
    // 申请记录-卡片，一对多反向
    public function cardbox()
    {
        return $this->belongsTo(\App\Model\Cardbox::class, 'card_id', 'id');
    }
}
