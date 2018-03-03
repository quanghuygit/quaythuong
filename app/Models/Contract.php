<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        "id",
        "contract_id",
        "contract_name",
        "contract_user_name",
        "tvgt",
        "tvkt",
        "code",
        "date",
        "note",
    ];

    /**
     * @param Builder $query
     * @return mixed
     */
    public static function scopeNotWin($query)
    {
        $excludes = Contract::exclude()->pluck('id');
        return $query->whereNotIn('id', $excludes);
    }

    public static function scopeExclude($query)
    {
        return $query->where('note', 'không đủ điều kiện')
            ->orWhere('note', 'fail')
            ->orWhere('note', 'not')
            ->orWhere('note', 'không')
            ->orWhere('note', 'khong');
    }

}
