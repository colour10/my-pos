<?php

namespace App\Model;

use App\Model;
// use Laravel\Scout\Searchable;

class Bank extends Model
{
    // // 可以搜索
    // use Searchable;
    
    // // 定义搜索里面的type
    // public function searchableAs() {
    //     return 'bank';
    // }
    
    // // 定义有哪些字段需要搜索
    // public function toSearchableArray() {
    //     return [
    //         'name' => $this->name,
    //     ];
    // }

    // 开户行-银行卡号，一对多
    public function bank()
    {
        return $this->hasMany(\App\Model\Card::class, 'bank_id', 'id');
    }

}
