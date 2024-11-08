<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class operation_detail extends Model
{
        protected $guarded=[];
    use HasFactory;
     public function Operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id');
    }
}
