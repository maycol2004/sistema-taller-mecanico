<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepuestoRequest;
use App\Http\Requests\UpdateRepuestoRequest;
use App\Models\Repuesto;
use App\Services\InventarioService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RepuestoController extends Controller
{
    public function __construct(private readonly InventarioService $inventario)
    {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $stock = $request->query('stock');

        $repuestos = Repuesto::query()
            ->withCount('ordenes')
            ->buscar($q)
            ->when($stock === 'bajo', fn ($query) => $query->stockBajo())
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $bajosStock = Repuesto::stockBajo()->count();

        return view('repuestos.index', compact('repuestos', 'q', 'stock', 'bajosStock'));
    }

    public function create(): View
    {
        return view('repuestos.create');
    }

    public function store(StoreRepuestoRequest $request): RedirectResponse
    {
        $repuesto = Repuesto::create($request->validated());

        return redirect()
            ->route('repuestos.show', $repuesto)
            ->with('success', "Repuesto «{$repuesto->nombre}» registrado correctamente.");
    }

    public function show(Repuesto $repuesto): View
    {
        $ordenes = $repuesto->ordenes()
            ->with(['vehiculo.cliente'])
            ->orderByDesc('ordenes.fecha_ingreso')
            ->paginate(8);

        $unidadesUsadas = (int) DB::table('orden_repuesto')->where('repuesto_id', $repuesto->id)->sum('cantidad');
        $totalGenerado = (float) DB::table('orden_repuesto')->where('repuesto_id', $repuesto->id)->sum('subtotal');

        return view('repuestos.show', compact('repuesto', 'ordenes', 'unidadesUsadas', 'totalGenerado'));
    }

    public function edit(Repuesto $repuesto): View
    {
        return view('repuestos.edit', compact('repuesto'));
    }

    public function update(UpdateRepuestoRequest $request, Repuesto $repuesto): RedirectResponse
    {
        $repuesto->update($request->validated());

        return redirect()
            ->route('repuestos.show', $repuesto)
            ->with('success', 'El repuesto se actualizó correctamente.');
    }

    public function reponer(Request $request, Repuesto $repuesto): RedirectResponse
    {
        $datos = $request->validate([
            'cantidad' => ['required', 'integer', 'min:1', 'max:10000'],
        ], [
            'cantidad.required' => 'Indica cuántas unidades deseas reponer.',
            'cantidad.integer' => 'La cantidad a reponer debe ser un número entero.',
            'cantidad.min' => 'La cantidad mínima a reponer es 1 unidad.',
            'cantidad.max' => 'La cantidad máxima por reposición es 10,000 unidades.',
        ]);

        $repuesto = $this->inventario->reponerStock($repuesto, (int) $datos['cantidad']);

        return back()->with(
            'success',
            "Se repusieron {$datos['cantidad']} unidades de «{$repuesto->nombre}». Stock actual: {$repuesto->stock} unidades."
        );
    }

    public function destroy(Repuesto $repuesto): RedirectResponse
    {
        $vecesUsado = $repuesto->ordenes()->count();

        if ($vecesUsado > 0) {
            return back()->with(
                'error',
                "No se puede eliminar el repuesto «{$repuesto->nombre}» porque está asignado a {$vecesUsado} orden(es) de servicio."
            );
        }

        try {
            $nombre = $repuesto->nombre;
            $repuesto->delete();

            return redirect()
                ->route('repuestos.index')
                ->with('success', "Repuesto «{$nombre}» eliminado correctamente.");
        } catch (QueryException $exception) {
            return back()->with('error', 'No se pudo eliminar el repuesto porque tiene información relacionada.');
        }
    }
}
