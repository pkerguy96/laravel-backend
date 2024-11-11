<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreXrayRequest;
use App\Http\Resources\OperationXrayCollection;
use App\Http\Resources\XrayResource;
use App\Models\Patient;
use App\Models\Xray;
use App\Models\Xraypreference;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Operation;
use App\Models\operation_detail;

class XrayController extends Controller
{
    use HttpResponses;
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
    public function store(StoreXrayRequest $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validated();

            // Explode xray_type to handle multiple xray types
            $xrayTypes = explode(',', $validatedData['xray_type']);

            $totalPrice = 0;

            foreach ($xrayTypes as $type) {
                // Find Xraypreference for each type
                $xrayPreference = Xraypreference::where('xray_type', $type)->first();

                if ($xrayPreference) {
                    // Add price to total
                    $totalPrice += $xrayPreference->price;
                } else {
                    return $this->error(null, "Type de radiographie '{$type}' non trouvé dans les préférences", 404);
                }
            }

            // Create the operation record first with total cost
            $operation = Operation::create([
                'patient_id' => $validatedData['patient_id'],
                'total_cost' => $totalPrice,
                'is_paid' => false,
                'note' => $validatedData['note'] ?? null,
            ]);
            foreach ($xrayTypes as $type) {
                $xrayPreference = Xraypreference::where('xray_type', $type)->first();
                $xrayData = [
                    'patient_id' => $validatedData['patient_id'],
                    'operation_id' => $operation->id,
                    'xray_type' => $type,
                    'view_type' => $validatedData['view_type'] ?? null,
                    'body_side' => $validatedData['body_side'] ?? null,
                    'type' => $validatedData['type'] ?? 'xray',
                    'note' => $validatedData['note'] ?? null,
                    'price' => $xrayPreference->price,
                    'xray_preference_id' => $xrayPreference->id,
                ];

                Xray::create($xrayData);
            }

            return $this->success(null, 'Radiographies enregistrées avec succès', 201);
        } catch (\Throwable $th) {
            Log::error('Error storing x-ray data: ' . $th->getMessage());

            return $this->error($th->getMessage(), 'Une erreur s\'est produite lors de l\'enregistrement des radiographies', 500);
        }
    }



    public function showpatientxrays(string $id)
    {

        try {
            if (!Patient::where('id', $id)->exists()) {
                return $this->error(null, 'patient dosnt exist', 500);
            }
            $xray = Xray::where('patient_id', $id)
                ->whereDate('created_at', Carbon::today())
                ->get();
            if (!$xray) {
                return $this->error(null, 'no xray', 500);
            }
            return new OperationXrayCollection($xray);
        } catch (\Throwable $th) {
            return $this->error($th, 'something went wrong', 500);
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

        try {
            // Step 1: Parse incoming x-ray data from rows
            $incomingXrays = collect($request->input('rows')); // assume all items are x-rays
            Log::info('Request data', ['data' => $request->all()]);

            if ($incomingXrays->isEmpty()) {
                return response()->json(['message' => 'No x-ray data found in the request.'], 400);
            }
            // Step 2: Fetch existing x-rays for this operation
            $existingXrays = Xray::where('operation_id', $id)->get();
            Log::info('Request data', ['XRAYS' => $id]);
            if ($existingXrays->isEmpty()) {
                return response()->json(['message' => 'No x-rays found for this operation ID in the database.'], 404);
            }
            // Step 3: Identify deleted, new, and updated x-rays
            $incomingXrayIds = $incomingXrays->pluck('id')->filter(); // get IDs of incoming x-rays, filter out nulls for new items
            $deletedXrays = $existingXrays->whereNotIn('id', $incomingXrayIds); // x-rays to be deleted
            $newXrays = $incomingXrays->filter(fn($xray) => !isset($xray['id'])); // new x-rays to be added
            $updatedXrays = $incomingXrays->filter(fn($xray) => isset($xray['id'])); // x-rays to be updated

            Xray::destroy($deletedXrays->pluck('id')->toArray());
            Log::info('Deleted X-rays', ['ids' => $deletedXrays->pluck('id')->toArray()]);

            // Step 5: Log updated x-rays
            foreach ($updatedXrays as $xray) {
                Xray::where('id', $xray['id'])->update([
                    'price' => $xray['price'],
                    'xray_type' => $xray['xray_type'],
                ]);
                Log::info('Updated X-ray', ['id' => $xray['id'], 'data' => $xray]);
            }
            // Step 6: Log new x-rays creation
            foreach ($newXrays as $xray) {
                operation_detail::create([
                    'operation_id' => $id,
                    'operation_name' => $xray['xray_type'],
                    'price' => $xray['price'],
                ]);
                Log::info('Created new X-ray', ['data' => $xray]);
            }

            return response()->json(['message' => 'Operation updated successfully.']);
        } catch (\Throwable $th) {
            Log::error('Error updating operation', ['error' => $th->getMessage()]);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
