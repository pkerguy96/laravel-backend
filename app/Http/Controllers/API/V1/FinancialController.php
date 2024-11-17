<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchOperationDebtResource;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FinancialController extends Controller
{
    public function PatientsDebt(Request $request)
    {
        // Extract pagination and filtering parameters
        $searchQuery = $request->input('searchQuery', '');
        $perPage = $request->get('per_page', 20); // Default to 20 items per page if not specified

        // Base query for operations
        $query = Operation::with('patient', 'operationdetails', 'payments', 'xray')
            ->where('is_paid', 0)
            ->whereBetween('created_at', [
                Carbon::parse($request->date)->startOfDay(),
                Carbon::parse($request->date2)->endOfDay()
            ]);

        // Apply search filters if a search query is provided
        if (!empty($searchQuery)) {
            $query->whereHas('patient', function ($q) use ($searchQuery) {
                $q->where('nom', 'like', "%{$searchQuery}%")
                    ->orWhere('prenom', 'like', "%{$searchQuery}%");
            });
        }

        // Paginate the results
        $Operations = $query->orderBy('id', 'desc')->paginate($perPage);

        // Return the paginated data as a resource collection
        return SearchOperationDebtResource::collection($Operations);
    }
}
