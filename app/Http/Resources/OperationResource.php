<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OperationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'patient_id' => $this->patient_id,
            'patient_name' => $this->patient->nom . ' ' . $this->patient->prenom,
            'operation_details' => OperationDetailResource::collection($this->operationdetails),
            'xrays' => OperationXrayPayments::collection($this->xray),
            'externalOperation' => hospitaloperationresource::collection($this->externalOperations),
            'payments' => PayementResource::collection($this->payments),
            'total_cost' => $this->total_cost,
            'is_paid' => $this->is_paid,
            'note' => $this->note,

        ];
    }
}
