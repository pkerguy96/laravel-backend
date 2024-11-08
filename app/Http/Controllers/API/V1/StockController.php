<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class StockController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('searchQuery');
        $patients =   Product::orderBy('id', 'desc')
            ->paginate($request->get('per_page', 20));

        if (!empty($searchQuery)) {
            // If there's a search query, apply search filters
            $patients = Product::with('appointments', 'Ordonance')
                ->where(function ($query) use ($searchQuery) {
                    $query->where('bar_code', 'like', "%{$searchQuery}%")
                        ->orWhere('product_name', 'like', "%{$searchQuery}%");
                    // Add more fields to search if necessary
                })
                ->orderBy('id', 'desc')
                ->paginate($request->get('per_page', 20));
        }

        return new ProductCollection($patients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $validatedata = $request->validated();
            Product::create($validatedata);
            return $this->success(null, "Produit inséré avec succès", 201);
        } catch (\Throwable $th) {
            return $this->error($th, "Une erreur est survenue lors de l'insertion du produit.", 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
