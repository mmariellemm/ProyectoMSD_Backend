<?php

namespace App\Http\Controllers;

use App\Models\DetalleCompras;
use Illuminate\Http\Request;

class DetalleComprasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detalles = DetalleCompras::with(['compra', 'cliente'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($detalles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_compra' => 'required|exists:compras,id',
            'id_cliente' => 'required|exists:clientes,id',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
        ]);

        $detalle = DetalleCompras::create($request->all());

        return response()->json($detalle->load(['compra', 'cliente']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DetalleCompras $detalleCompras)
    {
        return response()->json($detalleCompras->load(['compra', 'cliente']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DetalleCompras $detalleCompras)
    {
        $request->validate([
            'id_compra' => 'sometimes|exists:compras,id',
            'id_cliente' => 'sometimes|exists:clientes,id',
            'cantidad' => 'sometimes|integer|min:1',
            'precio' => 'sometimes|numeric|min:0',
        ]);

        $detalleCompras->update($request->all());

        return response()->json($detalleCompras->load(['compra', 'cliente']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DetalleCompras $detalleCompras)
    {
        $detalleCompras->delete();

        return response()->json(['message' => 'Detalle de compra eliminado exitosamente']);
    }

    /**
     * Obtener detalles por compra
     */
    public function porCompra($compraId)
    {
        $detalles = DetalleCompras::where('id_compra', $compraId)
            ->with(['compra', 'cliente'])
            ->get();

        return response()->json($detalles);
    }

    /**
     * Obtener detalles por cliente
     */
    public function porCliente($clienteId)
    {
        $detalles = DetalleCompras::where('id_cliente', $clienteId)
            ->with(['compra', 'cliente'])
            ->get();

        return response()->json($detalles);
    }

    /**
     * Calcular total de una compra
     */
    public function totalCompra($compraId)
    {
        $total = DetalleCompras::where('id_compra', $compraId)
            ->selectRaw('SUM(cantidad * precio) as total')
            ->first();

        return response()->json([
            'id_compra' => $compraId,
            'total' => $total->total ?? 0
        ]);
    }

    /**
     * Obtener estadísticas por cliente
     */
    public function estadisticasCliente($clienteId)
    {
        $estadisticas = DetalleCompras::where('id_cliente', $clienteId)
            ->selectRaw('
                COUNT(*) as total_detalles,
                SUM(cantidad) as total_cantidad,
                SUM(cantidad * precio) as total_gastado,
                AVG(precio) as precio_promedio
            ')
            ->first();

        return response()->json([
            'id_cliente' => $clienteId,
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * Crear múltiples detalles para una compra
     */
    public function crearMultiples(Request $request)
    {
        $request->validate([
            'id_compra' => 'required|exists:compras,id',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_cliente' => 'required|exists:clientes,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio' => 'required|numeric|min:0',
        ]);

        $detallesCreados = [];

        foreach ($request->detalles as $detalle) {
            $detallesCreados[] = DetalleCompras::create([
                'id_compra' => $request->id_compra,
                'id_cliente' => $detalle['id_cliente'],
                'cantidad' => $detalle['cantidad'],
                'precio' => $detalle['precio'],
            ]);
        }

        return response()->json([
            'message' => 'Detalles creados exitosamente',
            'detalles' => $detallesCreados
        ], 201);
    }
}
