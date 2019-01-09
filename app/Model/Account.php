<?php

namespace App\Model;

use App\Model;

class Account extends Model
{
    // 开启白名单
    protected $fillable = ['name'];

    // 合伙人-调账记录，一对多
    public function agent()
    {
        return $this->belongsTo(\App\Model\Agent::class);
    }

}
