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
            'operations' => $this->mapOperations($this->operations),
        ];
    }

    protected function mapAppointments($appointments)
    {
        return $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => $appointment->title,
                'date' => $appointment->date,
                'note' => $appointment->note,
            ];
        });
    }
    protected function mapOperations($operations)
    {
        return $operations->map(function ($operation) {
            return [
                'total_cost' => $operation->total_cost,
                'note' => $operation->note,
                'date' => $operation->created_at->format('Y-m-d H:i:s'),
                'operation_type' => $this->resolveOperationType($operation), // Unified operation type
            ];
        });
    }


    /**
     * Resolve the operation type.
     * Include `operationdetails`, `xrays`, and `outsourceOperations` data.
     *
     * @param  \App\Models\Operation  $operation
     * @return array
     */
    protected function resolveOperationType($operation)
    {
        // Extract data as arrays directly to prevent issues
        $operationDetails = $operation->operationdetails->map(function ($detail) {
            return [
                'id' => $detail->id,
                'operation_type' => $detail->operation_name,
                'price' => $detail->price,
                'source' => 'operation_detail',
            ];
        })->toArray(); // Convert to array

        $xrayTypes = $operation->xray->map(function ($xray) {
            return [
                'id' => $xray->id,
                'operation_type' => $xray->xray_type ?? 'X-Ray',
                'price' => $xray->price ?? null,
                'source' => 'xray',
            ];
        })->toArray(); // Convert to array

        $outsourceOperations = $operation->externalOperations->map(function ($external) {
            return [
                'id' => $external->id,
                'operation_type' => $external->operation_type,
                'price' => $external->total_price,
                'source' => 'external_operation',
            ];
        })->toArray(); // Convert to array

        // Safely merge arrays
        $merged = array_merge($operationDetails, $xrayTypes, $outsourceOperations);

        return $merged; // Return the final merged array
    }
}
