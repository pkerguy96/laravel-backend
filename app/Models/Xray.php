<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Xray extends Model
{
    protected $guarded = [];
    use HasFactory;
    public function preference()
    {
        return $this->belongsTo(Xraypreference::class, 'xray_preference_id');
    }
    public function Operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id');
    }
}
