<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberMap extends Model
{
    protected $fillable = [
        'member_id',
        'parent_id',
        'level_ctr',
    ];

}
