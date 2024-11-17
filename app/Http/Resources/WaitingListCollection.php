<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WaitingListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($waiting) {
            return [
                'id' => $waiting->id,
                'patient_id' =>  $waiting->patient->id,
                'patient_name' =>  $waiting->patient->nom . " " . $waiting->patient->prenom,
                'entry_time' => $waiting->entry_time,
                'status' =>
                $waiting->status
            ];
        });
    }
}
