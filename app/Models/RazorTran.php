<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazorTran extends Model
{
    protected $dates = [
        'joining_date',
    ];
    protected $fillable = [
        'member_id',
        'amount',
        'txn_id',
        'name',
        'description',
        'category'
    ];
    
    public function designation(){
        return $this->belongsTo(Member::class,'member_id','id');
    }

}
