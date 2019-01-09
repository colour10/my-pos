<?php

namespace App\Model;

use App\Model;

class Finance extends Model
{
    // 经办记录-合伙人，一对多反向
    public function agent()
    {
        return $this->belongsTo(\App\Model\Agent::class, 'id', 'agent_id');
    }
}
