<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OperationPaymentCollection;
use Illuminate\Http\Request;
use App\Models\Operation;
use App\Http\Resources\OperationResource;
use App\Http\Resources\PayementResource;
use App\Http\Resources\treatementOperationCollection;
use App\Http\Resources\XrayCollectionForNurse;
use App\Models\Payment;
use App\Models\Xray;
use App\Traits\HasPermissionCheck;

class OperationController extends Controller
{
    use HasPermissionCheck;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorizePermission(['superadmin', 'access_debt', 'insert_debt', 'delete_debt']);

        $searchQuery = $request->input('searchQuery');
        $perPage = $request->get('per_page', 20);

        // Fetch operations with relationships and apply search if necessary
        $operationsQuery = Operation::with(['patient', 'payments', 'xray'])
            ->orderBy('id', 'desc');

        if (!empty($searchQuery)) {
            // Add search conditions for operations or related models
            $operationsQuery->whereHas('patient', function ($query) use ($searchQuery) {
                $query->where('nom', 'like', "%{$searchQuery}%")
                    ->orWhere('prenom', 'like', "%{$searchQuery}%");
            });
        }

        // Paginate results
        $operations = $operationsQuery->paginate($perPage);

        return new OperationPaymentCollection($operations);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}
    public function getByOperationId($operationId)
    {


        $operation = Operation::with(['operationdetails', 'xray', 'payments', 'externalOperations', 'patient:id,nom,prenom'])
            ->where('id', $operationId)
            ->first();

        if (!$operation) {
            return response()->json(['error' => 'Operation not found'], 404);
        }

        return new OperationResource($operation);
    }




    public function recurringOperation(Request $request)
    {
        $this->authorizePermission(['superadmin', 'access_operation_recurring']);

        $searchQuery = $request->input('searchQuery');
        $perPage = $request->get('per_page', 20); // Default items per page is 20

        $operationsQuery = Operation::where('treatment_nbr', '>', 0)
            ->where('treatment_isdone', 0)
            ->with(['operationdetails' => function ($query) {
                $query->select('operation_id', 'operation_name'); // Include operation_id
            }])
            ->with(['xray' => function ($query) {
                $query->select('operation_id', 'xray_type'); // Include operation_id
            }])
            ->orderBy('id', 'desc');

        // Apply search filter if a search query is provided
        if (!empty($searchQuery)) {
            $operationsQuery->whereHas('patient', function ($query) use ($searchQuery) {
                $query->where('nom', 'like', "%{$searchQuery}%")
                    ->orWhere('prenom', 'like', "%{$searchQuery}%");
            });
        }

        $operations = $operationsQuery->paginate($perPage);

        return new treatementOperationCollection($operations);
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

            $operation = Operation::findorfail($id);
            $amountPaid = (float)$request->amount_paid;
            if ($amountPaid < 0) {
                return response()->json(['error' => 'Le montant payé ne peut pas être un nombre négatif.'], 400);
            }
            if ($operation) {
                $sumAmountPaid = (float)Payment::where('operation_id', $id)->sum('amount_paid');
                $totalCost = (float)$operation->total_cost;


                if (!isset($amountPaid) || empty($amountPaid)) {
                    return response()->json(['error' => 'Le montant payé est requis'], 400);
                }
                if ($amountPaid > $totalCost) {

                    // The amount paid exceeds the total cost
                    return response()->json(['error' => "Le montant payé dépasse le coût total."], 400);
                } elseif ($sumAmountPaid + $amountPaid > $totalCost) {

                    return response()->json(['error' => "Le montant total payé dépasse le coût total."], 400);
                } elseif ($sumAmountPaid + $amountPaid <= $totalCost) {

                    $payement =   Payment::create([
                        'operation_id' => $operation->id,
                        'total_cost' => $totalCost,
                        'amount_paid' => $amountPaid,
                        'patient_id' => $operation->patient_id
                    ]);
                    $operation->update(['is_paid' => $sumAmountPaid + $amountPaid === $totalCost ? 1 : 0]);
                    return response()->json([
                        'message' => "Paiement ajouté avec succès.",
                        'data' => new PayementResource($payement)
                    ]);
                }
            } else {
                return response()->json(['message' => "Aucun identifiant d'opération n'existe."]);
            }
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        Operation::findorfail($id)->delete();

        return response()->json(['message' => 'Operation deleted successfully'], 204);
    }

    public function deletePaymentDetail($id)
    {

        // Retrieve operation ID for the payment
        $operationId = Payment::where('id', $id)->value('operation_id');
        // Delete the payment by getting the operation first 
        Payment::where('operation_id', $operationId)->findOrFail($id)->delete();
        // Calculate total paid amount and total price
        $sumAmountPaid = (float) Payment::where('operation_id', $operationId)->sum('amount_paid');
        $totalPrice = (float) Operation::where('id', $operationId)->value('total_cost');
        // Update operation status based on payment status
        Operation::where('id', $operationId)->update(['is_paid' => ($sumAmountPaid === $totalPrice) ? 1 : 0]);
        return response()->json(['message' => 'Payment deleted successfully'], 204);
    }

    public function getXraysByOperation($operationId)
    {

        $xrays = Xray::with('patient')
            ->where('operation_id', $operationId)
            ->get();

        return new XrayCollectionForNurse($xrays);
    }
}
