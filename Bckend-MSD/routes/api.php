<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);




//Rutas de Clientes
Route::prefix('api/v1')->group(function () {

    // **LISTAR** - Obtener todos los clientes (con paginación y búsqueda)
    Route::get('/clientes', [ClientesController::class, 'index'])
        ->name('clientes.index');

    // **VER** - Obtener un cliente específico por ID
    Route::get('/clientes/{id}', [ClientesController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('clientes.show');

    // **AGREGAR** - Crear un nuevo cliente
    Route::post('/clientes', [ClientesController::class, 'store'])
        ->name('clientes.store');

    // **EDITAR** - Actualizar un cliente existente (PUT para actualización completa)
    Route::put('/clientes/{id}', [ClientesController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('clientes.update');

    // **EDITAR** - Actualizar parcialmente un cliente (PATCH para actualización parcial)
    Route::patch('/clientes/{id}', [ClientesController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('clientes.patch');

    // **ELIMINAR** - Eliminar un cliente
    Route::delete('/clientes/{id}', [ClientesController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('clientes.destroy');
});



//Rutas de los productos Productos
Route::apiResource('productos', ProductosController::class);
Route::get('productos/search', [ProductosController::class, 'search']);
Route::resource('productos', ProductosController::class);
Route::get('productos/search', [ProductosController::class, 'search']);
Route::middleware('auth')->group(function () {
Route::apiResource('productos', ProductosController::class);
Route::get('productos/search', [ProductosController::class, 'search']);

});


// Rutas CRUD básicas para Compras
>> Route::apiResource('compras', ComprasController::class);
>>
>> // Rutas adicionales para consultas específicas
>> Route::get('compras/empleado/{empleadoId}', [ComprasController::class, 'porEmpleado'])
>>     ->name('compras.por-empleado');
>>
>> Route::get('compras/cliente/{clienteId}', [ComprasController::class, 'porCliente'])
>>     ->name('compras.por-cliente');
>>
>> Route::get('compras/estado/{estado}', [ComprasController::class, 'porEstado'])
>>     ->name('compras.por-estado'); 

