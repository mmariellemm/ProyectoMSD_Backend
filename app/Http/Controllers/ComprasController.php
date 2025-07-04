<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use Illuminate\Http\Request;

class ComprasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compras = Compras::with(['empleado', 'cliente', 'metodoPago'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($compras);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required|exists:empleados,id',
            'id_cliente' => 'required|exists:clientes,id',
            'fecha_compra' => 'required|date',
            'estado' => 'required|in:pendiente,completada,cancelada',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
        ]);

        $compra = Compras::create($request->all());

        return response()->json($compra->load(['empleado', 'cliente', 'metodoPago']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Compras $compras)
    {
        return response()->json($compras->load(['empleado', 'cliente', 'metodoPago']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Compras $compras)
    {
        $request->validate([
            'id_empleado' => 'sometimes|exists:empleados,id',
            'id_cliente' => 'sometimes|exists:clientes,id',
            'fecha_compra' => 'sometimes|date',
            'estado' => 'sometimes|in:pendiente,completada,cancelada',
            'metodo_pago_id' => 'sometimes|exists:metodos_pago,id',
        ]);

        $compras->update($request->all());

        return response()->json($compras->load(['empleado', 'cliente', 'metodoPago']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compras $compras)
    {
        // Verificar si se puede eliminar
        if ($compras->estado === 'completada') {
            return response()->json(['error' => 'No se puede eliminar una compra completada'], 400);
        }

        $compras->delete();

        return response()->json(['message' => 'Compra eliminada exitosamente']);
    }

    /**
     * Obtener compras por empleado
     */
    public function porEmpleado($empleadoId)
    {
        $compras = Compras::where('id_empleado', $empleadoId)
            ->with(['empleado', 'cliente', 'metodoPago'])
            ->get();

        return response()->json($compras);
    }

    /**
     * Obtener compras por cliente
     */
    public function porCliente($clienteId)
    {
        $compras = Compras::where('id_cliente', $clienteId)
            ->with(['empleado', 'cliente', 'metodoPago'])
            ->get();

        return response()->json($compras);
    }

    /**
     * Obtener compras por estado
     */
    public function porEstado($estado)
    {
        $compras = Compras::where('estado', $estado)
            ->with(['empleado', 'cliente', 'metodoPago'])
            ->get();

        return response()->json($compras);
    }
}