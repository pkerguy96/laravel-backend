<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientDetailResource extends JsonResource
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
            'appointments' => $this->mapAppointments($this->appointments),
            /*   'operations' => $this->mapOperations($this->operations), */

        ];
    }
    protected function mapAppointments($appointments)
    {
        return $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => $appointment->title,
                'date' =>  $appointment->date,
                'note' => $appointment->note
            ];
        });
    }
    /*   protected function mapOperations($operations)
    {
        return $operations->map(function ($operation) {
            return [
                'total_cost' => $operation->total_cost,
                'note' => $operation->note,
                'date' => $operation->created_at->format('Y-m-d H:i:s'),
                'operation_type' => $operation->operationDetails->map(function ($operationType) {
                    return [
                        'id' => $operationType->id,
                        'tooth_id' => $operationType->tooth_id,
                        'operation_type' => $operationType->operation_type,
                        'price' => $operationType->price,
                        'name' => $operationType->preference->name,
                    ];
                }),

            ];
        });
    } */
}
