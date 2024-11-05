<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\NurseCollection;
use App\Models\User;
use App\Http\Requests\StoreNurseRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\NurseResource;
use App\Traits\HttpResponses;

class NurseController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return new NurseCollection(User::where('role', 'nurse')->orderBy('id', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNurseRequest $request)
    {

        $authenticatedUserId = auth()->user();
        if ($authenticatedUserId->role === 'nurse') {
            return $this->error(null, 'Only doctors can create nurses!', 401);
        }
        $attributes = $request->all();
        $attributes = $request->except('checkbox');
        /*     $attributes['doctor_id'] = $authenticatedUserId->id; */
        $attributes['password'] = Hash::make($attributes['password']);
        $attributes['role'] = 'nurse';
        if ($request->input('checkbox') === true) {

            $attributes['termination_date'] = null;
        } else {

            $attributes['termination_date'] = $request->input('termination_date');
        }
        try {
            $nurseCount = User::where('role', 'nurse')
                ->count();

            if ($nurseCount >= 6) {
                return response()->json(['message' => "Vous ne pouvez avoir que jusqu'à six infirmières."], 400);
            }


            $data = new NurseResource(User::create($attributes));
            return response()->json([
                'message' => 'Nurse created successfully',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Nurse',
                'error' => $e->getMessage(),
            ], 500);
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
