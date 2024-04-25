<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bursary extends Model
{
    //
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];


    public function user()
    {
        return $this->belongsToMany(User::class)->student()->active();
    }
}