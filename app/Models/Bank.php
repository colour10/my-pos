<?php

namespace App\Models;

// use Laravel\Scout\Searchable;

/**
 * App\Models\Bank
 *
 * @property int $id 主键ID
 * @property string $name 开户行名称
 * @property \Illuminate\Support\Carbon|null $created_at 操作时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Card[] $bank
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bank query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bank whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        return $this->hasMany(Card::class, 'bank_id', 'id');
    }

}
