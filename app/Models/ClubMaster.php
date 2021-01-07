<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMaster extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'designation'
    ];

    public function members(){
        return $this->hasMany(Member::class,'designation_id','id');
    }
}
