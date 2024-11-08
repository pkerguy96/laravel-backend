<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\PatientController;
use App\Http\Controllers\API\V1\AppointmentController;
use App\Http\Controllers\API\V1\NurseController;
use App\Http\Controllers\API\V1\fileuploadController;
use App\Http\Controllers\API\V1\StockController;
use App\Http\Controllers\API\V1\WaitingRoomController;
use App\Http\Controllers\API\V1\XrayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\API\V1'], function () {

    route::post('/login', [AuthController::class, 'login']);
    Route::post('testpatientstore', [PatientController::class, 'testpatientstore']);
});


Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\API\V1', 'middleware' => ['auth:sanctum']], function () {
    Route::get('Admin/logout', [AuthController::class, 'Logout']);
    Route::get('patientDetails/{id}', [PatientController::class, 'patientDetails']);

    route::apiResource('Patient', PatientController::class);
    Route::apiResource('Nurse', NurseController::class);
    Route::get('uploadsInfo', [fileuploadController::class, 'uploadsInfo']);
    Route::apiResource('Appointment', AppointmentController::class);
    Route::apiResource('Filesupload', fileuploadController::class);


    /* waiting room */
    route::apiResource('Waitingroom', WaitingRoomController::class);
    route::post('incrementPatient', [WaitingRoomController::class, 'addPatient']);
    route::post('PatientsWaitingRoom', [WaitingRoomController::class, 'PatientsWaitingRoom']);
    route::delete('decrementPatient/{id}', [WaitingRoomController::class, 'decrementPatient']);
    route::get('resetPatientCounter', [WaitingRoomController::class, 'resetPatientCounter']);
    route::get('GetWaitingList', [WaitingRoomController::class, 'GetWaitingList']);


    /* stock */
    route::apiResource('Stock', StockController::class);
    /* xray */
    route::apiResource('xray', XrayController::class);
});
