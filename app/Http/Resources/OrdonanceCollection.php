<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdonanceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($ordonance) {
            return [
                'id' => $ordonance->id,
                'doctor_id' => $ordonance->doctor_id,
                'patient_id' => $ordonance->patient_id,
                'date' => $ordonance->date,
                'ordonance_details' => OrdonanceDetailsResource::collection($ordonance->whenLoaded('OrdonanceDetails')),
                'patient' => [
                    'nom' => $ordonance->patient->nom,
                    'prenom' => $ordonance->patient->prenom,
                ],
            ];
        });
    }
}
