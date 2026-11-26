<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use App\Models\Servicio;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServicioController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));

        $datos = $request->validate([
            'precio_min' => ['nullable', 'numeric', 'min:0'],
            'precio_max' => ['nullable', 'numeric', 'min:0'],
        ], [
            'precio_min.numeric' => 'El precio mínimo debe ser un número válido.',
            'precio_min.min' => 'El precio mínimo no puede ser negativo.',
            'precio_max.numeric' => 'El precio máximo debe ser un número válido.',
            'precio_max.min' => 'El precio máximo no puede ser negativo.',
        ]);

        $precioMin = $datos['precio_min'] ?? null;
        $precioMax = $datos['precio_max'] ?? null;

        if ($precioMin !== null && $precioMax !== null && (float) $precioMin > (float) $precioMax) {
            throw ValidationException::withMessages([
                'precio_min' => 'El precio mínimo no puede ser mayor que el precio máximo.',
            ]);
        }

        $servicios = Servicio::query()
            ->withCount('ordenes')
            ->buscar($q)
            ->when($precioMin !== null, fn ($query) => $query->where('precio_base', '>=', $precioMin))
            ->when($precioMax !== null, fn ($query) => $query->where('precio_base', '<=', $precioMax))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('servicios.index', compact('servicios', 'q', 'precioMin', 'precioMax'));
    }

    public function create(): View
    {
        return view('servicios.create');
    }

    public function store(StoreServicioRequest $request): RedirectResponse
    {
        $servicio = Servicio::create($request->validated());

        return redirect()
            ->route('servicios.show', $servicio)
            ->with('success', "Servicio «{$servicio->nombre}» creado correctamente.");
    }

    public function show(Servicio $servicio): View
    {
        $ordenes = $servicio->ordenes()
            ->with(['vehiculo.cliente'])
            ->orderByDesc('ordenes.fecha_ingreso')
            ->paginate(8);

        $vecesUsado = DB::table('orden_servicio')->where('servicio_id', $servicio->id)->count();
        $totalGenerado = (float) DB::table('orden_servicio')->where('servicio_id', $servicio->id)->sum('subtotal');

        return view('servicios.show', compact('servicio', 'ordenes', 'vecesUsado', 'totalGenerado'));
    }

    public function edit(Servicio $servicio): View
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function update(UpdateServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $servicio->update($request->validated());

        return redirect()
            ->route('servicios.show', $servicio)
            ->with('success', 'El servicio se actualizó correctamente.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $vecesUsado = $servicio->ordenes()->count();

        if ($vecesUsado > 0) {
            return back()->with(
                'error',
                "No se puede eliminar el servicio «{$servicio->nombre}» porque está asignado a {$vecesUsado} orden(es) de servicio."
            );
        }

        try {
            $nombre = $servicio->nombre;
            $servicio->delete();

            return redirect()
                ->route('servicios.index')
                ->with('success', "Servicio «{$nombre}» eliminado correctamente.");
        } catch (QueryException $exception) {
            return back()->with('error', 'No se pudo eliminar el servicio porque tiene información relacionada.');
        }
    }
}
