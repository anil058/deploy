<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberIncome extends Model
{
    public function member(){
        return $this->belongsTo(Member::class,'member_id','id');
    }

    public function bonusType(){
        return $this->belongsTo(BonusType::class,'bonus_type_id','id');
    }

    public function bonusRule(){
        return $this->belongsTo(BonusRule::class,'bonus_rule_id','id');
    }
}
