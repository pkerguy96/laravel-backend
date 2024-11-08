<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    protected $guarded=[];
    use HasFactory;
      public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function operationdetails()
    {
        return $this->hasMany(operationdetails::class, 'operation_id');
    }
     public function payments()
    {
        return $this->hasMany(payement::class, 'operation_id');
    }
}
