<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'buy_price' => 'required|numeric|min:0.01',
            'sell_price' => 'required|numeric|min:0.01',
            'expiry_date' => 'nullable|date', 
        ]);

        $product = Product::findOrFail($validated['product_id']);

        ProductSupplier::create([
            'supplier_id' => $validated['supplier_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'sell_price' => $validated['sell_price'],
            'buy_price' => $validated['buy_price'],
            'expiry_date' => $validated['expiry_date'],
        ]);

        $product->qte += $validated['quantity'];
        $product->save();

        return response()->json(['message' => 'Product batch added successfully!']);
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
