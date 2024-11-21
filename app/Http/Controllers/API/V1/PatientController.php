<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PatientCollection;
use App\Http\Requests\StorePatientRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientDetailResource;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $searchQuery = $request->input('searchQuery');
        $patients =   Patient::with('appointments', 'Ordonance')
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 20));

        if (!empty($searchQuery)) {
            // If there's a search query, apply search filters
            $patients = Patient::with('appointments', 'Ordonance')
                ->where(function ($query) use ($searchQuery) {
                    $query->where('nom', 'like', "%{$searchQuery}%")
                        ->orWhere('prenom', 'like', "%{$searchQuery}%");
                    // Add more fields to search if necessary
                })
                ->orderBy('id', 'desc')
                ->paginate($request->get('per_page', 20));
        }

        return new PatientCollection($patients);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {

        try {


            $requestData = $request->validated();
            $data = new PatientResource(Patient::create($requestData));


            return response()->json([
                'message' => 'Patient created successfully',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create patient',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function patientDetails(string $id)
    {
        return  new PatientDetailResource(Patient::with('appointments', 'operations', 'operations.operationdetails', 'Xray')->where('id', $id)->first());
    }

    public function testpatientstore(Request $request)
    {
        $patients = implode(', ', $request->input('patient_alergy'));


        dd($patients);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();


        return  new PatientResource(Patient::where('id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePatientRequest $request, string $id)
    {



        $patient = Patient::findOrFail($id);

        if (!$patient) {
            return response()->json([
                'message' => 'Patient not found.',
            ], 404);
        }

        // Validate the updated data
        $validatedData = $request->validated();
        Log::Info($validatedData);
        // Update patient details
        $patient->update($validatedData);

        return response()->json([
            'message' => 'Patient updated successfully.',
            'data' =>  new PatientResource($patient),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        Patient::findorfail($id)->delete();
        return response()->json(['message' => 'patient deleted successfully'], 204);
    }
}
