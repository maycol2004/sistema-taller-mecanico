<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehiculoController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $clienteId = $request->integer('cliente_id') ?: null;

        $vehiculos = Vehiculo::query()
            ->with('cliente')
            ->withCount('ordenes')
            ->buscar($q)
            ->when($clienteId, fn ($query) => $query->where('cliente_id', $clienteId))
            ->orderBy('placa')
            ->paginate(10)
            ->withQueryString();

        $clientes = Cliente::orderBy('nombres')->orderBy('apellidos')->get();

        return view('vehiculos.index', compact('vehiculos', 'clientes', 'q', 'clienteId'));
    }

    public function create(Request $request): View
    {
        $clientes = Cliente::orderBy('nombres')->orderBy('apellidos')->get();
        $clienteSeleccionado = $request->integer('cliente_id') ?: null;

        return view('vehiculos.create', compact('clientes', 'clienteSeleccionado'));
    }

    public function store(StoreVehiculoRequest $request): RedirectResponse
    {
        $vehiculo = Vehiculo::create($request->validated());

        return redirect()
            ->route('vehiculos.show', $vehiculo)
            ->with('success', "Vehículo con placa {$vehiculo->placa} registrado correctamente.");
    }

    public function show(Vehiculo $vehiculo): View
    {
        $vehiculo->load([
            'cliente',
            'ordenes' => fn ($query) => $query->orderByDesc('fecha_ingreso'),
        ]);

        $totalOrdenes = $vehiculo->ordenes->count();

        $totalFacturado = (float) $vehiculo->ordenes
            ->whereIn('estado', ['Finalizado', 'Entregado'])
            ->sum('total');

        return view('vehiculos.show', compact('vehiculo', 'totalOrdenes', 'totalFacturado'));
    }

    public function edit(Vehiculo $vehiculo): View
    {
        $clientes = Cliente::orderBy('nombres')->orderBy('apellidos')->get();

        return view('vehiculos.edit', compact('vehiculo', 'clientes'));
    }

    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo): RedirectResponse
    {
        $vehiculo->update($request->validated());

        return redirect()
            ->route('vehiculos.show', $vehiculo)
            ->with('success', 'Los datos del vehículo se actualizaron correctamente.');
    }

    public function destroy(Vehiculo $vehiculo): RedirectResponse
    {
        $cantidadOrdenes = $vehiculo->ordenes()->count();

        if ($cantidadOrdenes > 0) {
            return back()->with(
                'error',
                "No se puede eliminar el vehículo {$vehiculo->placa} porque tiene {$cantidadOrdenes} orden(es) de servicio asociada(s)."
            );
        }

        try {
            $placa = $vehiculo->placa;
            $vehiculo->delete();

            return redirect()
                ->route('vehiculos.index')
                ->with('success', "Vehículo {$placa} eliminado correctamente.");
        } catch (QueryException $exception) {
            return back()->with('error', 'No se pudo eliminar el vehículo porque tiene información relacionada.');
        }
    }
}
