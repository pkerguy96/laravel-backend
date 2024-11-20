<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class hospitaloperationresource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hospital' => $this->hospital->name,
            'patient_name' => $this->patient->nom . ' ' . $this->patient->prenom,
            'operation_type' => $this->operation_type,
            'operation_date' => $this->operation_date->format('Y-m-d H:i:s'),
            'price' => $this->price,
            'description' => $this->description,


        ];
    }
}
