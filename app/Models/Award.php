<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $table = 'awards';
    protected $fillable = [
        "name",
        "number",
    ];
    
    public $timestamps = false;

    public function contract()
    {
        return $this->belongsToMany(Contract::class, 'winners', 'contract_id', 'award_id');
    }
}
