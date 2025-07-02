<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\PermisoController;

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

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Empleados (protegido)
Route::middleware('auth:sanctum')->apiResource('empleados', EmpleadosController::class);

// Clientes (versión v1)
Route::prefix('v1')->group(function () {
    Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/{id}', [ClientesController::class, 'show'])->where('id', '[0-9]+')->name('clientes.show');
    Route::post('/clientes', [ClientesController::class, 'store'])->name('clientes.store');
    Route::put('/clientes/{id}', [ClientesController::class, 'update'])->where('id', '[0-9]+')->name('clientes.update');
    Route::patch('/clientes/{id}', [ClientesController::class, 'update'])->where('id', '[0-9]+')->name('clientes.patch');
    Route::delete('/clientes/{id}', [ClientesController::class, 'destroy'])->where('id', '[0-9]+')->name('clientes.destroy');
});

// Productos (protegido)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('productos', ProductosController::class);
    Route::get('productos/search', [ProductosController::class, 'search']);
});

Route::get('/per-index', [PermisoController::class, 'index'])->name('permiso.index');
Route::get('/per-show', [PermisoController::class, 'show'])->name('permiso.show');
Route::post('/per-store', [PermisoController::class, 'store'])->name('permiso.store');
Route::put('/per-update/{id}', [PermisoController::class, 'update'])->name('permiso.update');
Route::delete('/per-destroy/{id}', [PermisoController::class, 'destroy'])->name('permiso.destroy');
