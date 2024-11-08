<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'cin' => $this->cin,
            'date' => $this->date,
            'address' => $this->address,
            'sex' => $this->sex,
            'phoneNumber' => $this->phone_number,
            'mutuelle' => $this->mutuelle,
            'note' => $this->note,
            'allergy' => $this->allergy ? explode(',', $this->allergy) : [],
            'disease' => $this->disease ? explode(',', $this->disease) : [],
            'referral' => $this->referral ? explode(',', $this->referral) : [],
            'appointments' => CustomAppointmentResource::collection($this->appointments),
            'ordonances' => OrdonanceResource::collection($this->Ordonance),
        ];
    }
}
