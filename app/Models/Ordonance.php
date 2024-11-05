<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ordonance_Details;
class Ordonance extends Model
{
    use HasFactory;
    public function OrdonanceDetails()
    {
        return $this->hasMany(Ordonance_Details::class, 'ordonance_id');
    }
}
