<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Orden;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));

        $clientes = Cliente::query()
            ->withCount('vehiculos')
            ->buscar($q)
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->paginate(8)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'q'));
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $cliente = Cliente::create($request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', "Cliente «{$cliente->nombre_completo}» registrado correctamente.");
    }

    public function show(Cliente $cliente): View
    {
        $cliente->load([
            'vehiculos' => fn ($query) => $query->withCount('ordenes')->orderBy('placa'),
        ]);

        $vehiculoIds = $cliente->vehiculos->pluck('id');

        $ordenes = Orden::with('vehiculo')
            ->whereIn('vehiculo_id', $vehiculoIds)
            ->orderByDesc('fecha_ingreso')
            ->take(6)
            ->get();

        $totalOrdenes = Orden::whereIn('vehiculo_id', $vehiculoIds)->count();

        $totalFacturado = (float) Orden::whereIn('vehiculo_id', $vehiculoIds)
            ->whereIn('estado', ['Finalizado', 'Entregado'])
            ->sum('total');

        return view('clientes.show', compact('cliente', 'ordenes', 'totalOrdenes', 'totalFacturado'));
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', 'Los datos del cliente se actualizaron correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cantidadVehiculos = $cliente->vehiculos()->count();

        if ($cantidadVehiculos > 0) {
            return back()->with(
                'error',
                "No se puede eliminar a «{$cliente->nombre_completo}» porque tiene {$cantidadVehiculos} vehículo(s) asociado(s). Elimina o reasigna los vehículos primero."
            );
        }

        try {
            $nombre = $cliente->nombre_completo;
            $cliente->delete();

            return redirect()
                ->route('clientes.index')
                ->with('success', "Cliente «{$nombre}» eliminado correctamente.");
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'No se pudo eliminar el cliente porque tiene información relacionada en el sistema.'
            );
        }
    }
}
