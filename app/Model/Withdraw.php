<?php

namespace App\Model;

use App\Model;

class Withdraw extends Model
{
    // 提现记录-合伙人，一对多反向
    public function agent()
    {
        return $this->belongsTo(\App\Model\Agent::class, 'agent_id', 'id');
    }

    // 提现记录-卡，一对多反向
    public function card()
    {
        return $this->belongsTo(\App\Model\Card::class, 'card_id', 'id');
    }

    // 提现记录-提现渠道，一对多反向
    public function advancemethod()
    {
        return $this->belongsTo(\App\Model\AdvanceMethod::class, 'method_id', 'id');
    }
}
