<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddMoney extends Model
{
    protected $fillable = [
        'member_id',
        'amount',
        'provider_id',
    ];

    public function member() {
        return $this->belongsTo(Member::class,'member_id','id');
    }

    public function provider(){
        return $this->belongsTo(Provider::class,'provider_id','id');
    }

}
