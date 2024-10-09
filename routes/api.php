<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\EmployeeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/employee/{id}', [EmployeeController::class, 'update']);
Route::apiResource('/employee', EmployeeController::class)
    ->except(['update']);
