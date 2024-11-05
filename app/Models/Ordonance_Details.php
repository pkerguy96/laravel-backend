<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ordonance;
class Ordonance_Details extends Model
{
    use HasFactory;
    public function Ordonance()
    {
        return $this->belongsTo(Ordonance::class, 'ordonance_id');
    }
}
