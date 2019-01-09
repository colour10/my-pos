<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agent extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    // 合伙人-调账记录，一对多
    public function finances()
    {
        return $this->hasMany(\App\Model\Finance::class, 'agent_id', 'id');
    }

    // 合伙人-已经审核通过的调账记录，一对多
    public function passedfinances()
    {
        return $this->hasMany(\App\Model\Finance::class, 'agent_id', 'id')->where('status', '1');
    }

    // 合伙人-合伙人账户资金，一对一
    public function agentaccount()
    {
        return $this->hasOne(\App\Model\AgentAccount::class, 'agent_id', 'id');
    }

    // 合伙人-银行卡，一对多
    public function cards()
    {
        return $this->hasMany(\App\Model\Card::class, 'agent_id', 'id');
    }

    // 合伙人-银行卡，因为只有一张银行卡，所以是一对一
    public function card()
    {
        return $this->hasOne(\App\Model\Card::class, 'agent_id', 'id')->where('isdefault', 1);
    }

    // 合伙人-提现记录，一对多
    public function withdraws()
    {
        return $this->hasMany(\App\Model\Withdraw::class, 'agent_id', 'id');
    }

    // 合伙人-申请卡片记录，一对多
    public function applycards()
    {
        return $this->hasMany(\App\Model\ApplyCard::class, 'agent_id', 'id');
    }

}