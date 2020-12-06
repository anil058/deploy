<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempMember extends Model
{
    protected $dates = [
        'creation_date',
    ];
    protected $fillable = [
        'name',
        'referal_code',
        'mobile_no',
    ];

    public function member(){
        return $this->hasOne(Member::class, 'temp_id', 'id');
    }

}
