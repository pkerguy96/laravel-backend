<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\PatientController;
use App\Http\Controllers\API\V1\AppointmentController;
use App\Http\Controllers\API\V1\BloodTestController;
use App\Http\Controllers\API\V1\DashboardKpisController;
use App\Http\Controllers\API\V1\NurseController;
use App\Http\Controllers\API\V1\fileuploadController;
use App\Http\Controllers\API\V1\FinancialController;
use App\Http\Controllers\API\V1\HospitalController;
use App\Http\Controllers\API\V1\HospitalOperationsController;
use App\Http\Controllers\API\V1\OperationController;
use App\Http\Controllers\API\V1\OrdonanceController;
use App\Http\Controllers\API\V1\StockController;
use App\Http\Controllers\API\V1\WaitingRoomController;
use App\Http\Controllers\API\V1\XrayController;
use App\Http\Controllers\API\V1\NotificationAlertController;
use App\Http\Controllers\API\V1\OperationPrefsController;
use App\Http\Controllers\API\V1\ProductConsumableController;
use App\Http\Controllers\API\V1\ProductSupplierController;
use App\Http\Controllers\API\V1\SupplierController;
use App\Http\Controllers\API\V1\Xraypreferences;
use App\Models\OperationPref;
use App\Models\Ordonance;

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
    /* Supplier routes */
    route::apiResource('Supplier', SupplierController::class);

    Route::get('showAllSuppliers', [SupplierController::class, 'showAllSuppliers']);
    /* product consumables */
    route::apiResource('consumables', ProductConsumableController::class);
    /* stock */
    route::apiResource('Stock', StockController::class);
    Route::get('getProductsForOperation', [StockController::class, 'getProductsForOperation']);

    /* supplierproduct */
    route::apiResource('Supplierproduct', ProductSupplierController::class);

    /* xray */
    route::apiResource('xray', XrayController::class);
    Route::get('showpatientxrays/{id}', [XrayController::class, 'showpatientxrays']);
    /* operation */
    route::apiResource('Operation', OperationController::class);
    Route::get('getByOperationId/{id}', [OperationController::class, 'getByOperationId']);
    Route::get('recurringOperation', [OperationController::class, 'recurringOperation']);
    Route::get('getXraysByOperation/{id}', [OperationController::class, 'getXraysByOperation']);

    /* ordonance */
    route::apiResource('Ordonance', OrdonanceController::class);
    /* bloodtest */
    route::apiResource('bloodtest', BloodTestController::class);
    Route::post('insertWihtoutxray', [XrayController::class, 'insertWihtoutxray']);
    Route::get('GetAppointmentPagated', [AppointmentController::class, 'GetAppointmentPagated']);


    /* Payment and related routes */
    Route::post('PatientsDebt', [FinancialController::class, 'PatientsDebt']);

    Route::delete('deletePaymentDetail/{id}', [OperationController::class, 'deletePaymentDetail']);

    /* Notification */
    route::apiResource('notification', NotificationAlertController::class);
    Route::get('/notifications/{id}', [NotificationAlertController::class, 'markAsRead']);

    /* preferences */
    route::apiResource('XrayPreferences', Xraypreferences::class);
    route::apiResource('OperationPreferences', OperationPrefsController::class);
    /* hospital */
    route::apiResource('Hospital', HospitalController::class);
    /* hospital actions */
    route::apiResource('Hospitaloperations', HospitalOperationsController::class);
    /* outsource operation */


    Route::get('/searchPatients', [HospitalOperationsController::class, 'searchPatients']);
    Route::get('/searchHospitals', [HospitalOperationsController::class, 'searchHospitals']);













    /* KPIS */
    //Kpis
    Route::get('getTotalRevenue', [DashboardKpisController::class, 'getTotalRevenue']);
    Route::get('getAppointments', [DashboardKpisController::class, 'getAppointments']);
    Route::get('getCanceledAppointments', [DashboardKpisController::class, 'getCanceledAppointments']);
    Route::get('calculateAgePercentage', [DashboardKpisController::class, 'calculateAgePercentage']);
    Route::get('TotalPatients', [DashboardKpisController::class, 'TotalPatients']);
    Route::get('appointmentKpipeak', [DashboardKpisController::class, 'appointmentKpipeak']);
    Route::get('getMonthlyAppointments', [DashboardKpisController::class, 'getMonthlyAppointments']);
    Route::get('getMonthlyCanceledAppointments', [DashboardKpisController::class, 'getMonthlyCanceledAppointments']);
    Route::get('retrieveFromCashier', [DashboardKpisController::class, 'retrieveFromCashier']);
    Route::get('OnlyCashierNumber', [DashboardKpisController::class, 'OnlyCashierNumber']);
    Route::post('PatientsDebt', [DashboardKpisController::class, 'PatientsDebt']);
    /*  route::post('DashboardKpiUserPref', [UserPreferenceController::class, 'DashboardKpiUserPref']);
    route::post('OperationUserPref', [UserPreferenceController::class, 'OperationUserPref']);
    route::get('getOperationPrefs', [UserPreferenceController::class, 'getOperationPrefs']);
    route::delete('deleteOperationPrefs/{id}', [UserPreferenceController::class, 'deleteOperationPrefs']); */
    Route::delete('deletePaymentDetail/{id}', [OperationController::class, 'deletePaymentDetail']);
    Route::get('PayementVerificationCheckout/{id}', [OperationController::class, 'PayementVerificationCheckout']);
});
