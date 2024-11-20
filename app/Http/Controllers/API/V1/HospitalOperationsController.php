<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\hospitaloperationresource;
use App\Models\outsourceOperation;
use Illuminate\Http\Request;

class HospitalOperationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = outsourceOperation::all();
        return hospitaloperationresource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
