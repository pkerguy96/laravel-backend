<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Ordonance;
use Illuminate\Http\Request;
use App\Http\Resources\OrdonanceCollection;
use App\Http\Resources\OrdonanceResource;
use App\Models\Ordonance_Details;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdonanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $ordonances = Ordonance::with('OrdonanceDetails', 'Patient')->orderBy('id', 'desc')->get();

        return new OrdonanceCollection($ordonances);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $medicineArray = $request->medicine;

            // Start a database transaction
            DB::beginTransaction();


            // Create the Ordonance record
            $ordonance = Ordonance::create([

                'patient_id' => $request->input('patient_id'),
                'date' => $request->input('date'),
            ]);
            // Validate and create OrdonanceDetails records

            foreach ($medicineArray as $medicine) {
                Ordonance_Details::create([
                    'ordonance_id' => $ordonance->id,
                    'medicine_name' => $medicine['medicine_name'],
                    'note' => $medicine['note'],
                ]);
            }

            // Commit the transaction
            DB::commit();
            $data = new OrdonanceResource(Ordonance::with('OrdonanceDetails')->where('id', $ordonance->id)->first());
            // Return a response with the created Ordonance and OrdonanceDetails
            return response()->json([
                'message' => 'Ordonance created successfully',
                'data' => $data,
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            Log::error($e);
            // Return an error response
            return response()->json([
                'message' => 'Error creating Ordonance',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $data = Ordonance::with('OrdonanceDetails', 'Patient')->where('id', $id)->first();
        return response()->json(['data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $ordonance = Ordonance::findOrFail($id);
            // Start a database transaction
            DB::beginTransaction();

            // Update the Ordonance record with the new data
            $ordonance->update([

                'patient_id' => $request->input('patient_id'),
                'date' => $request->input('date'),
                // Add any other fields you want to update
            ]);

            // Validate and update OrdonanceDetails records
            $medicineArray = $request->medicine;

            // Delete existing OrdonanceDetails records
            $ordonance->OrdonanceDetails()->delete();

            // Create new OrdonanceDetails records
            foreach ($medicineArray as $medicine) {
                $ordonance->OrdonanceDetails()->create([
                    'ordonance_id' => $ordonance->id,
                    'medicine_name' => $medicine['medicine_name'],
                    'note' => $medicine['note'],
                ]);
            }
            DB::commit();
            $data = new OrdonanceResource(Ordonance::with('OrdonanceDetails')->find($ordonance->id));
            return response()->json([
                'message' => 'Ordonance updated successfully',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            Log::info($e);
            // Return an error response
            return response()->json(['message' => 'Error updating Ordonance'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $ordonance = Ordonance::findorfail($id);

            if ($ordonance) {
                $ordonance->OrdonanceDetails()->delete();
                $ordonance->delete();
                return $this->success(null, 'Ordonance deleted successfuly', 200);
            }
        } catch (\Exception $e) {
            return $this->success(null, 'oops there is an error:' . $e->getMessage(), 500);
        }
    }
}
