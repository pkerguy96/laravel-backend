<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OperationDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $operationType = $this->preference ? $this->preference->name : 'Unknown'; // Provide a default value if preference is null


        return [
            'id' => $this->id,
            'tooth_id' => $this->tooth_id,
            'operation_type' => $this->operation_type,
            'name' => $operationType,
            'price' => $this->price,
        ];
    }
}
