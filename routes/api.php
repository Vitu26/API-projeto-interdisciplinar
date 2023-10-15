<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultasController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('consultas', [ConsultasController::class, 'index']);
Route::get('consultas/{id}', [ConsultasController::class, 'show']);
Route::post('consultas', [ConsultasController::class, 'store']);
Route::put('consultasupdate/{id}', [ConsultasController::class, 'update']);
Route::delete('consultasdelete/{id}', [ConsultasController::class, 'destroy']);
