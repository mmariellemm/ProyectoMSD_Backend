<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadosController;

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


//Rutas Auth
// Ruta para registrar usuarios
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
// Ruta para el inicio de sesión
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Ruta para cerrar sesión
Route::middleware('auth:sanctum')-> post('/logout', [AuthController::class, 'logout']);


// Ruta de empleados:
//GET /api/empleados	
//post    /api/empleados
//GET /api/empleados/{id}
//PUT/PATCH	/api/empleados/{id}
//DELETE	/api/empleados/{id}
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('empleados', EmpleadosController::class);
});