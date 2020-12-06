<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelAchiever extends Model
{
    public function member(){
        return $this->belongsTo(Member::class,'member_id','id');
    }
}
