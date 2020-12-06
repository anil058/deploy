<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    protected $fillable = [
        'member_id',
    ];

    public function member(){
        return $this->belongsTo(Member::class,'member_id','id');
    }

    public function approvedBy(){
        return $this->belongsTo(Member::class, 'approved_id', 'id');
    }
}
