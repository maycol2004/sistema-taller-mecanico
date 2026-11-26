<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Orden;
use App\Models\Repuesto;
use App\Models\Vehiculo;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::count();
        $vehiculos = Vehiculo::count();
        $repuestos = Repuesto::count();
        $unidadesStock = (int) Repuesto::sum('stock');
        $bajosStock = Repuesto::stockBajo()->count();
        $repuestosBajoStock = Repuesto::stockBajo()->orderBy('stock')->take(6)->get();

        $ordenesActivas = Orden::activas()->count();
        $ordenesMes = Orden::whereMonth('fecha_ingreso', now()->month)
            ->whereYear('fecha_ingreso', now()->year)
            ->count();

        $ingresosMes = (float) Orden::whereIn('estado', ['Finalizado', 'Entregado'])
            ->whereMonth('fecha_ingreso', now()->month)
            ->whereYear('fecha_ingreso', now()->year)
            ->sum('total');

        $ultimasOrdenes = Orden::with(['vehiculo.cliente'])
            ->orderByDesc('fecha_ingreso')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $conteoEstados = Orden::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $totalOrdenes = (int) $conteoEstados->sum();

        return view('dashboard', compact(
            'clientes',
            'vehiculos',
            'repuestos',
            'unidadesStock',
            'bajosStock',
            'repuestosBajoStock',
            'ordenesActivas',
            'ordenesMes',
            'ingresosMes',
            'ultimasOrdenes',
            'conteoEstados',
            'totalOrdenes',
        ));
    }
}
