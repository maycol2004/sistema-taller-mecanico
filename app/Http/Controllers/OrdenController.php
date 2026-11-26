<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrdenRequest;
use App\Http\Requests\UpdateOrdenRequest;
use App\Models\Orden;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Vehiculo;
use App\Services\InventarioService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrdenController extends Controller
{
    public function __construct(private readonly InventarioService $inventario)
    {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $estado = $request->query('estado');
        $estado = in_array($estado, Orden::ESTADOS, true) ? $estado : null;

        $datos = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
        ], [
            'desde.date' => 'La fecha inicial no tiene un formato válido.',
            'hasta.date' => 'La fecha final no tiene un formato válido.',
        ]);

        $desde = $datos['desde'] ?? null;
        $hasta = $datos['hasta'] ?? null;

        if ($desde && $hasta && Carbon::parse($desde)->gt(Carbon::parse($hasta))) {
            throw ValidationException::withMessages([
                'desde' => 'La fecha inicial no puede ser posterior a la fecha final.',
            ]);
        }

        $consultaBase = Orden::query()
            ->with(['vehiculo.cliente', 'usuario'])
            ->buscar($q)
            ->when($desde, fn ($query) => $query->where('fecha_ingreso', '>=', $desde))
            ->when($hasta, fn ($query) => $query->where('fecha_ingreso', '<=', $hasta));

        $consulta = (clone $consultaBase)
            ->when($estado, fn ($query) => $query->where('estado', $estado));

        $ordenes = (clone $consulta)
            ->orderByDesc('fecha_ingreso')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $conteos = (clone $consultaBase)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $totalOrdenes = (int) $conteos->sum();

        return view('ordenes.index', compact(
            'ordenes',
            'q',
            'estado',
            'desde',
            'hasta',
            'conteos',
            'totalOrdenes',
        ));
    }

    public function create(Request $request): View
    {
        $orden = new Orden([
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(2)->toDateString(),
            'estado' => 'Pendiente',
        ]);

        if ($request->integer('vehiculo_id')) {
            $orden->vehiculo_id = $request->integer('vehiculo_id');
        }

        return view('ordenes.create', $this->datosFormulario($orden));
    }

    public function store(StoreOrdenRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $usuarioId = $request->user()->id;

        try {
            $orden = DB::transaction(function () use ($datos, $usuarioId): Orden {
                $orden = Orden::create([
                    'codigo' => Orden::generarCodigo(Carbon::parse($datos['fecha_ingreso'])->format('Y')),
                    'vehiculo_id' => $datos['vehiculo_id'],
                    'user_id' => $usuarioId,
                    'fecha_ingreso' => $datos['fecha_ingreso'],
                    'fecha_estimada_entrega' => $datos['fecha_estimada_entrega'] ?? null,
                    'diagnostico' => $datos['diagnostico'] ?? null,
                    'observaciones' => $datos['observaciones'] ?? null,
                    'estado' => $datos['estado'],
                ]);

                $this->inventario->sincronizarDetalle(
                    $orden,
                    $datos['servicios'] ?? [],
                    $datos['repuestos'] ?? []
                );

                if ($datos['estado'] === 'Entregado') {
                    $orden->actualizarEstado('Entregado');
                }

                $orden->recalcularTotal();

                return $orden;
            });
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with('error', 'No se pudo registrar la orden. Verifica los datos e inténtalo nuevamente.');
        }

        return redirect()
            ->route('ordenes.show', $orden)
            ->with('success', "Orden {$orden->codigo} registrada correctamente.");
    }

    public function show(Orden $orden): View
    {
        $orden->load(['vehiculo.cliente', 'servicios', 'repuestos', 'usuario']);

        $subtotalServicios = $orden->servicios
            ->sum(fn (Servicio $servicio): float => (float) $servicio->pivot->subtotal);

        $subtotalRepuestos = $orden->repuestos
            ->sum(fn (Repuesto $repuesto): float => (float) $repuesto->pivot->subtotal);

        $siguienteEstado = $orden->siguienteEstado();

        return view('ordenes.show', compact('orden', 'subtotalServicios', 'subtotalRepuestos', 'siguienteEstado'));
    }

    public function edit(Orden $orden): View
    {
        $orden->load(['servicios', 'repuestos']);

        return view('ordenes.edit', $this->datosFormulario($orden));
    }

    public function update(UpdateOrdenRequest $request, Orden $orden): RedirectResponse
    {
        $datos = $request->validated();

        try {
            DB::transaction(function () use ($orden, $datos): void {
                $orden->update([
                    'vehiculo_id' => $datos['vehiculo_id'],
                    'fecha_ingreso' => $datos['fecha_ingreso'],
                    'fecha_estimada_entrega' => $datos['fecha_estimada_entrega'] ?? null,
                    'diagnostico' => $datos['diagnostico'] ?? null,
                    'observaciones' => $datos['observaciones'] ?? null,
                ]);

                $this->inventario->sincronizarDetalle(
                    $orden,
                    $datos['servicios'] ?? [],
                    $datos['repuestos'] ?? []
                );

                $orden->actualizarEstado($datos['estado']);
                $orden->recalcularTotal();
            });
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with('error', 'No se pudo actualizar la orden. Verifica los datos e inténtalo nuevamente.');
        }

        return redirect()
            ->route('ordenes.show', $orden)
            ->with('success', "Orden {$orden->codigo} actualizada correctamente.");
    }

    public function destroy(Orden $orden): RedirectResponse
    {
        $codigo = $orden->codigo;

        try {
            DB::transaction(function () use ($orden): void {
                $this->inventario->restituirStock($orden);
                $orden->servicios()->detach();
                $orden->repuestos()->detach();
                $orden->delete();
            });
        } catch (QueryException $exception) {
            return back()->with('error', 'No se pudo eliminar la orden porque tiene información relacionada.');
        }

        return redirect()
            ->route('ordenes.index')
            ->with('success', "Orden {$codigo} eliminada y stock restituido correctamente.");
    }

    public function cambiarEstado(Request $request, Orden $orden): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(Orden::ESTADOS)],
        ], [
            'estado.required' => 'Selecciona un estado para la orden.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        $orden->actualizarEstado($datos['estado']);

        $mensaje = "Estado de la orden {$orden->codigo} actualizado a «{$orden->estado}».";

        if ($orden->fecha_entrega_real) {
            $mensaje .= " Fecha real de entrega: {$orden->fecha_entrega_real->format('d/m/Y')}.";
        }

        return back()->with('success', $mensaje);
    }

    public function avanzarEstado(Orden $orden): RedirectResponse
    {
        $siguiente = $orden->siguienteEstado();

        if (! $siguiente) {
            return back()->with('error', "La orden {$orden->codigo} ya fue entregada.");
        }

        $orden->actualizarEstado($siguiente);

        $mensaje = "Orden {$orden->codigo} avanzó a «{$siguiente}».";

        if ($orden->fecha_entrega_real) {
            $mensaje .= " Fecha real de entrega: {$orden->fecha_entrega_real->format('d/m/Y')}.";
        }

        return back()->with('success', $mensaje);
    }

    public function imprimir(Orden $orden): View
    {
        $orden->load(['vehiculo.cliente', 'servicios', 'repuestos', 'usuario']);

        $subtotalServicios = $orden->servicios
            ->sum(fn (Servicio $servicio): float => (float) $servicio->pivot->subtotal);

        $subtotalRepuestos = $orden->repuestos
            ->sum(fn (Repuesto $repuesto): float => (float) $repuesto->pivot->subtotal);

        return view('ordenes.imprimir', compact('orden', 'subtotalServicios', 'subtotalRepuestos'));
    }

    private function datosFormulario(Orden $orden): array
    {
        $repuestos = Repuesto::orderBy('nombre')->get();

        if ($orden->exists) {
            $asignados = $orden->repuestos
                ->mapWithKeys(fn (Repuesto $repuesto): array => [
                    $repuesto->id => (int) $repuesto->pivot->cantidad,
                ]);

            $repuestos->each(function (Repuesto $repuesto) use ($asignados): void {
                $repuesto->setAttribute(
                    'stock_disponible',
                    (int) $repuesto->stock + (int) ($asignados[$repuesto->id] ?? 0)
                );
            });
        } else {
            $repuestos->each(function (Repuesto $repuesto): void {
                $repuesto->setAttribute('stock_disponible', (int) $repuesto->stock);
            });
        }

        return [
            'orden' => $orden,
            'vehiculos' => Vehiculo::with('cliente')->orderBy('placa')->get(),
            'servicios' => Servicio::orderBy('nombre')->get(),
            'repuestos' => $repuestos,
            'estados' => Orden::ESTADOS,
        ];
    }
}
