<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $dates = [
        'joining_date',
    ];
    protected $fillable = [
        'name',
        'father',
        'designation_id',
    ];
    
    public function designation(){
        return $this->belongsTo(ClubMaster::class,'designation_id','id');
    }

    public function parentMember(){
        return $this->belongsTo(Self::class,'parent_id','member_id');
    }

    public function payoutRequests(){
        return $this->hasMany(PayoutRequest::class,'member_id','id');
    }

    public function paidCommissionsTo(){
        return $this->hasMany(Commission::class,'member_id','id');
    }

    public function earnedCommissionsFrom(){
        return $this->hasMany(Commission::class,'beneficiary_id','id');
    }

    public function downMembers(){
        return $this->hasMany(MemberMap::class,'parent_id','id');
    }

    public function upMembers(){
        return $this->hasMany(MemberMap::class,'member_id','id');
    }

    public function levelAchieved(){
        return $this->hasMany(LevelAchiever::class,'member_id','id');
    }

    public function razorTrans(){
        return $this->hasMany(RazorTran::class,'member_id','id');
    }

    public function memberIncomeMembers(){
        return $this->hasMany(MemberIncome::class,'member_id','id');
    }

    public function memberIncomeRefMembers(){
        return $this->hasMany(MemberIncome::class,'ref_member_id','id');
    }

}
