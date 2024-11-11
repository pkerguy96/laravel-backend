<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Xraypreference extends Model
{
    protected $guarded = [];
    use HasFactory;
    public function xrays()
    {
        return $this->hasMany(Xray::class, 'xray_preference_id');
    }
}
