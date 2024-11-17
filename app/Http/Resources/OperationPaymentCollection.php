<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OperationPaymentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($operation) {
            $totalAmountPaid = $operation->payments->sum('amount_paid');
            return [
                'id' => $operation->id,
                'full_name' => $operation->patient->nom . ' ' . $operation->patient->prenom,
                'date' => $operation->created_at->toDateString(),
                'total_cost' => $operation->total_cost,
                'totalPaid' => $totalAmountPaid,
                'isPaid' => (bool) $operation->is_paid,
            ];
        });
    }
}
