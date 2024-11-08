<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payement extends Model
{
    protected $guarded=[];
    use HasFactory;
      public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id');
    }
}
