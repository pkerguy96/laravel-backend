<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\operation_detail;
use App\Models\payement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OperationResource;

class OperationController extends Controller
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
        //TODO: refactor this
        try {

            $data = $request->all();



            $data = $request->json()->all();
            $calculator = 0;
            Log::info($data['operations']);
            foreach ($data['operations'] as $item) {
                $calculator += $item['price'];
            }
            DB::beginTransaction();
            $operation = Operation::create([

                'patient_id' => $data['patient_id'],
                'total_cost' => $calculator,
                'is_paid' => $data['is_paid'],
                'note' =>  $data['note'],
            ]);
            foreach ($data['operations'] as $item) {
                operation_detail::create([
                    'operation_id' =>  $operation->id,
                    'bone_id' => implode(',', $item['bones']),
                    'operation_type' => $item['name'],
                    'price' => $item['price'],
                ]);
            }

            Payement::create([
                'operation_id' =>  $operation->id,
                'total_cost' => $calculator,
                'amount_paid' => $data['is_paid'] ? $calculator : $data['amount_paid'],

            ]);

            DB::commit();

            return response()->json([
                'message' => 'operation created successfully',
                'operation_id' => $operation->id

            ], 201);
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'message' => 'Oops something went wrong',
                'errors' => $e->getMessage()
            ], 404);
        }
    }
    public function getByOperationId($operationId)
    {

        $operation = Operation::with(['operationdetails'])->where('id', $operationId)->first();

        logger($operation->operationdetails->pluck('preference'));
        // Transform the result using the resource
        return new OperationResource($operation);
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
