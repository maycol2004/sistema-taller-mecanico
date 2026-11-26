<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    private const ESTADOS_FACTURADOS = ['Finalizado', 'Entregado'];

    public function index(Request $request): View
    {
        return view('reportes.index', $this->obtenerDatos($request));
    }

    public function exportar(Request $request): StreamedResponse
    {
        $datos = $this->obtenerDatos($request);
        $nombre = sprintf(
            'reporte_taller_%s_%s.csv',
            $datos['desde']->format('Ymd'),
            $datos['hasta']->format('Ymd')
        );

        return response()->streamDownload(function () use ($datos): void {
            $salida = fopen('php://output', 'w');

            if ($salida === false) {
                return;
            }

            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, ['Reporte ejecutivo del taller', config('taller.nombre')], ';');
            fputcsv($salida, ['Periodo', $datos['desde']->format('d/m/Y').' al '.$datos['hasta']->format('d/m/Y')], ';');
            fputcsv($salida, ['Generado', now()->format('d/m/Y H:i')], ';');
            fputcsv($salida, [], ';');

            fputcsv($salida, ['RESUMEN'], ';');
            fputcsv($salida, ['Atenciones registradas', $datos['atenciones']], ';');
            fputcsv($salida, ['Órdenes facturadas (Finalizado/Entregado)', $datos['ordenesFacturadas']], ';');
            fputcsv($salida, ['Ingresos del periodo (S/)', number_format($datos['ingresos'], 2, '.', '')], ';');
            fputcsv($salida, ['Ticket promedio (S/)', number_format($datos['ticketPromedio'], 2, '.', '')], ';');
            fputcsv($salida, [], ';');

            fputcsv($salida, ['TOP SERVICIOS'], ';');
            fputcsv($salida, ['Servicio', 'Cantidad', 'Total (S/)'], ';');

            foreach ($datos['topServicios'] as $servicio) {
                fputcsv($salida, [
                    $servicio->nombre,
                    (int) $servicio->cantidad,
                    number_format((float) $servicio->total, 2, '.', ''),
                ], ';');
            }

            if ($datos['topServicios']->isEmpty()) {
                fputcsv($salida, ['Sin servicios facturados en el periodo'], ';');
            }

            fputcsv($salida, [], ';');

            fputcsv($salida, ['TOP REPUESTOS'], ';');
            fputcsv($salida, ['Repuesto', 'Cantidad', 'Total (S/)'], ';');

            foreach ($datos['topRepuestos'] as $repuesto) {
                fputcsv($salida, [
                    $repuesto->nombre,
                    (int) $repuesto->cantidad,
                    number_format((float) $repuesto->total, 2, '.', ''),
                ], ';');
            }

            if ($datos['topRepuestos']->isEmpty()) {
                fputcsv($salida, ['Sin repuestos facturados en el periodo'], ';');
            }

            fputcsv($salida, [], ';');

            fputcsv($salida, ['INVENTARIO VALORIZADO'], ';');
            fputcsv($salida, ['Repuestos registrados', $datos['inventario']['repuestos']], ';');
            fputcsv($salida, ['Unidades en stock', $datos['inventario']['unidades']], ';');
            fputcsv($salida, ['Valor del inventario (S/)', number_format($datos['inventario']['valor'], 2, '.', '')], ';');
            fputcsv($salida, ['Repuestos con stock bajo', $datos['inventario']['bajo']], ';');
            fputcsv($salida, [], ';');

            fputcsv($salida, ['ALERTAS DE STOCK'], ';');
            fputcsv($salida, ['Código', 'Repuesto', 'Stock actual'], ';');

            foreach ($datos['alertas'] as $repuesto) {
                fputcsv($salida, [
                    $repuesto->codigo,
                    $repuesto->nombre,
                    (int) $repuesto->stock,
                ], ';');
            }

            if ($datos['alertas']->isEmpty()) {
                fputcsv($salida, ['Sin alertas de stock'], ';');
            }

            fclose($salida);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function obtenerDatos(Request $request): array
    {
        $validados = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
        ], [
            'desde.date' => 'La fecha inicial no tiene un formato válido.',
            'hasta.date' => 'La fecha final no tiene un formato válido.',
        ]);

        $desde = filled($validados['desde'] ?? null)
            ? Carbon::parse($validados['desde'])->startOfDay()
            : now()->startOfMonth();

        $hasta = filled($validados['hasta'] ?? null)
            ? Carbon::parse($validados['hasta'])->startOfDay()
            : now()->startOfDay();

        if ($desde->gt($hasta)) {
            throw ValidationException::withMessages([
                'desde' => 'La fecha inicial no puede ser posterior a la fecha final.',
            ]);
        }

        $desdeFecha = $desde->toDateString();
        $hastaFecha = $hasta->toDateString();

        $base = Orden::query()->whereBetween('fecha_ingreso', [$desdeFecha, $hastaFecha]);
        $facturadas = (clone $base)->whereIn('estado', self::ESTADOS_FACTURADOS);

        $atenciones = (clone $base)->count();
        $ordenesFacturadas = (clone $facturadas)->count();
        $ingresos = (float) (clone $facturadas)->sum('total');

        $topServicios = DB::table('orden_servicio')
            ->join('ordenes', 'ordenes.id', '=', 'orden_servicio.orden_id')
            ->join('servicios', 'servicios.id', '=', 'orden_servicio.servicio_id')
            ->whereBetween('ordenes.fecha_ingreso', [$desdeFecha, $hastaFecha])
            ->whereIn('ordenes.estado', self::ESTADOS_FACTURADOS)
            ->select(
                'servicios.nombre',
                DB::raw('SUM(orden_servicio.cantidad) as cantidad'),
                DB::raw('SUM(orden_servicio.subtotal) as total')
            )
            ->groupBy('servicios.id', 'servicios.nombre')
            ->orderByDesc('cantidad')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topRepuestos = DB::table('orden_repuesto')
            ->join('ordenes', 'ordenes.id', '=', 'orden_repuesto.orden_id')
            ->join('repuestos', 'repuestos.id', '=', 'orden_repuesto.repuesto_id')
            ->whereBetween('ordenes.fecha_ingreso', [$desdeFecha, $hastaFecha])
            ->whereIn('ordenes.estado', self::ESTADOS_FACTURADOS)
            ->select(
                'repuestos.codigo',
                'repuestos.nombre',
                DB::raw('SUM(orden_repuesto.cantidad) as cantidad'),
                DB::raw('SUM(orden_repuesto.subtotal) as total')
            )
            ->groupBy('repuestos.id', 'repuestos.codigo', 'repuestos.nombre')
            ->orderByDesc('cantidad')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $valorInventario = (float) DB::table('repuestos')
            ->selectRaw('COALESCE(SUM(stock * precio_unitario), 0) as valor')
            ->value('valor');

        $inventario = [
            'repuestos' => Repuesto::count(),
            'unidades' => (int) Repuesto::sum('stock'),
            'valor' => $valorInventario,
            'bajo' => Repuesto::stockBajo()->count(),
        ];

        $alertas = Repuesto::stockBajo()
            ->orderBy('stock')
            ->orderBy('nombre')
            ->take(8)
            ->get();

        $porEstado = (clone $base)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $ultimasOrdenes = (clone $base)
            ->with(['vehiculo.cliente'])
            ->orderByDesc('fecha_ingreso')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'atenciones' => $atenciones,
            'ordenesFacturadas' => $ordenesFacturadas,
            'ingresos' => $ingresos,
            'ticketPromedio' => $ordenesFacturadas > 0 ? $ingresos / $ordenesFacturadas : 0.0,
            'topServicios' => $topServicios,
            'topRepuestos' => $topRepuestos,
            'inventario' => $inventario,
            'alertas' => $alertas,
            'porEstado' => $porEstado,
            'ultimasOrdenes' => $ultimasOrdenes,
        ];
    }
}
