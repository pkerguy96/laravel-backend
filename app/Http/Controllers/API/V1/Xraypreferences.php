<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\XraypreferenceResource;
use App\Models\Xray;
use App\Models\Xraypreference;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class Xraypreferences extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Fetch all X-rays from the database
            $xrays = Xraypreference::all();

            // Return a JSON response
            return XraypreferenceResource::collection($xrays);
        } catch (\Exception $e) {

            return $this->error(
                'Failed to fetch X-rays',
                'error',
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'xray_type' => 'required|string|max:191',
                'price' => 'required|numeric|min:0',
            ]);

            // Create a new X-ray preference
            $xray = Xraypreference::create($validated);

            // Return the newly created resource
            return $this->success(
                new XraypreferenceResource($xray),
                'X-ray preference created successfully',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return $this->error(
                'Failed to create X-ray preference',
                'error',
                500
            );
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
        try {
            // Find the X-ray preference by ID
            $xray = Xraypreference::findOrFail($id);

            // Delete the X-ray preference
            $xray->delete();

            // Return a success response
            return $this->success(null, 'X-ray preference deleted successfully', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the X-ray preference is not found
            return $this->error('X-ray preference not found', 'error', 404);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return $this->error('Failed to delete X-ray preference', 'error', 500);
        }
    }
}
