<?php

namespace App\Http\Controllers;

use App\Models\Empleados;
use App\Models\Compras;
use App\Models\DetalleCompra;
use App\Models\Perfil;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmpleadosController extends Controller
{
    /**
     * Listar todos los empleados.
     */
    public function index()
    {
        return response()->json(Empleados::all());
    }

    /**
     * Registrar un nuevo empleado con ventas generadas desde Compras.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:80|unique:empleados,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empleado = Empleados::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $empleado->ventas = $this->generarVentasDesdeCompras($empleado->id);
        $empleado->save();

        return response()->json($empleado, 201);
    }

    /**
     * Mostrar un empleado específico.
     */
    public function show(Empleados $empleados)
    {
        return response()->json($empleados);
    }

    /**
     * Actualizar los datos de un empleado y su lista de ventas.
     */
    public function update(Request $request, Empleados $empleados)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:100',
            'email'    => 'sometimes|required|email|max:80|unique:empleados,email,' . $empleados->id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empleados->update([
            'name'     => $request->name ?? $empleados->name,
            'email'    => $request->email ?? $empleados->email,
            'password' => $request->filled('password')
                ? Hash::make($request->password)
                : $empleados->password,
        ]);

        $empleados->ventas = $this->generarVentasDesdeCompras($empleados->id);
        $empleados->save();

        return response()->json($empleados);
    }

    /**
     * Eliminar un empleado.
     */
    public function destroy(Empleados $empleados)
    {
        $empleados->delete();
        return response()->json(['message' => 'Empleado eliminado correctamente']);
    }

    // =====================================================
    // FUNCIONES PRIVADAS PARA REUTILIZACIÓN DE LÓGICA
    // =====================================================

    /**
     * Generar el array de ventas desde la tabla Compras.
     */
    private function generarVentasDesdeCompras($empleadoId)
    {
        return Compras::where('id_empleado', $empleadoId)
            ->get()
            ->map(function ($compra) {
                return [
                    'id_detalle_compras' => $compra->id_detalle_compras,
                    'fecha_compra'       => optional($compra->fecha_compra)->format('Y-m-d'),
                    'estado'             => $compra->estado,
                    'metodo_pago_id'     => $compra->metodo_pago_id,
                ];
            })
            ->toArray(); // convierte la colección en array puro
    }
}
