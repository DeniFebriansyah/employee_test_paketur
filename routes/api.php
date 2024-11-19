<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('login', function (Request $request) {
    return response()->json([
        'status' => false,
        'message' => 'Unauthorized'
    ], 401);
});
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:api')->group(function () {
    Route::post('company', [CompanyController::class, 'store']);
    Route::post('store-employee', [CompanyController::class, 'storeEmployee']);
    Route::post('update-employee/{id}', [CompanyController::class, 'updateEmployee']);

    Route::get('employees', [CompanyController::class, 'getAllEmployees']);
    Route::get('employee/{id}', [CompanyController::class, 'detail']);

    Route::post('delete-employee/{id}', [CompanyController::class, 'deleteEmployee']);
    Route::post('delete-company/{id}', [CompanyController::class, 'deleteCompany']);

    Route::post('restore-employee/{id}', [CompanyController::class, 'restoreEmployee']);
    Route::post('restore-company/{id}', [CompanyController::class, 'restoreCompany']);

});