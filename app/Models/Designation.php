<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'designation'
    ];

    public function members(){
        return $this->hasMany(Member::class,'designation_id','id');
    }
}
