<?php

namespace App\Model;

use App\Model;

class Card extends Model
{
    // 卡号-开户行，一对多反向
    public function bank()
    {
        return $this->belongsTo(\App\Model\Bank::class, 'bank_id', 'id');
    }

    // 卡号-合伙人，一对多反向
    public function agent()
    {
        return $this->belongsTo(\App\Model\Agent::class, 'agent_id', 'id');
    }
}
