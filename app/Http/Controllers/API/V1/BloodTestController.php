<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodTestRequest;
use App\Models\Bloodtest;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class BloodTestController extends Controller
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
    public function store(StoreBloodTestRequest $request)
    {
        try {
            $validatedData = $request->validated();


            $bloodTests = explode(',', $validatedData['blood_test']);


            foreach ($bloodTests as $test) {
                Bloodtest::create([
                    'operation_id' => $validatedData['operation_id'] ?? null,
                    'patient_id' => $validatedData['patient_id'],
                    'blood_test' => $test,
                ]);
            }
            return $this->success(null, 'Test sanguin enregistré avec succès', 200);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 'oops something went wrong', 500);
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
