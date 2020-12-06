<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    public function payoutRequest(){
        return $this->belongsTo(PayoutRequest::class,'request_id','id');
    }
}
