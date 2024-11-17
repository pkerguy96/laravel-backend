<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SearchOperationDebtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'name' => $this->patient->nom . ' ' . $this->patient->prenom,
            'date' => Carbon::parse($this->created_at)->toDateString(),
            'total_cost' => (float) $this->total_cost,
            'operation_type' => $this->mapOperationTypes(),
            'total_amount_paid' => (float) $this->payments->sum('amount_paid'),
            'amount_due' => (float) $this->total_cost - (float) $this->payments->sum('amount_paid'),
        ];
    }

    /**
     * Combine xray_type and operation_name into a single operation_type field.
     *
     * @return string
     */
    public function mapOperationTypes()
    {
        $xrayTypes = $this->xray->pluck('xray_type')->toArray();
        $operationNames = $this->operationdetails->pluck('operation_name')->toArray();

        // Combine both arrays and join with a comma
        $combined = array_merge($xrayTypes, $operationNames);

        return implode(', ', $combined);
    }
}
