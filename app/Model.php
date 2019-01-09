<?php

namespace App;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    // 默认所有字段都可以写入
    protected $guarded = [];
}