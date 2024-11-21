<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class hospitaloperationresource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalPaid = $this->operation->payments->sum('amount_paid');
        return [
            'id' => $this->id,
            'operation_id' => $this->operation->id,
            'hospital' => $this->hospital->name,
            'patient_name' => $this->patient->nom . ' ' . $this->patient->prenom,
            'operation_type' => $this->operation_type,
            'operation_date' => $this->operation_date
                ? Carbon::parse($this->operation_date)->format('Y-m-d')
                : null,
            'total_price' => number_format($this->total_price, 2),
            'amount_paid'
            => number_format($totalPaid, 2),
            'description' => $this->description,


        ];
    }
}
