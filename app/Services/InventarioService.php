<?php

namespace App\Services;

use App\Models\Orden;
use App\Models\Repuesto;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventarioService
{
    /**
     * Sincroniza servicios y repuestos, ajustando el stock por diferencias.
     * Debe ejecutarse dentro de una transacción de base de datos.
     */
    public function sincronizarDetalle(Orden $orden, array $servicios, array $repuestos): void
    {
        $this->sincronizarServicios($orden, $servicios);
        $this->sincronizarRepuestos($orden, $repuestos);
    }

    /**
     * Incrementa el stock de un repuesto de forma segura y concurrente.
     */
    public function reponerStock(Repuesto $repuesto, int $cantidad): Repuesto
    {
        return DB::transaction(function () use ($repuesto, $cantidad): Repuesto {
            $bloqueado = Repuesto::whereKey($repuesto->id)
                ->lockForUpdate()
                ->firstOrFail();

            $bloqueado->increment('stock', $cantidad);

            return $bloqueado->refresh();
        });
    }

    /**
     * Devuelve al inventario todos los repuestos utilizados por la orden.
     * Debe ejecutarse dentro de una transacción de base de datos.
     */
    public function restituirStock(Orden $orden): void
    {
        $usos = $orden->repuestos()->get();

        if ($usos->isEmpty()) {
            return;
        }

        $this->bloquearRepuestos($usos->pluck('id')->all());

        foreach ($usos as $repuesto) {
            Repuesto::whereKey($repuesto->id)
                ->increment('stock', (int) $repuesto->pivot->cantidad);
        }
    }

    private function sincronizarServicios(Orden $orden, array $items): void
    {
        $servicios = [];

        foreach ($items as $item) {
            $servicio = Servicio::find($item['servicio_id'] ?? null);

            if (! $servicio) {
                throw ValidationException::withMessages([
                    'servicios' => 'Uno de los servicios seleccionados ya no existe en el catálogo.',
                ]);
            }

            $cantidad = (int) $item['cantidad'];
            $precio = (float) $servicio->precio_base;

            $servicios[$servicio->id] = [
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => round($precio * $cantidad, 2),
            ];
        }

        $orden->servicios()->sync($servicios);
    }

    private function sincronizarRepuestos(Orden $orden, array $items): void
    {
        $anteriores = $orden->repuestos()
            ->get()
            ->mapWithKeys(fn (Repuesto $repuesto): array => [
                $repuesto->id => (int) $repuesto->pivot->cantidad,
            ])
            ->all();

        $nuevos = collect($items)
            ->mapWithKeys(fn (array $item): array => [
                (int) $item['repuesto_id'] => (int) $item['cantidad'],
            ])
            ->all();

        $ids = array_values(array_unique(array_merge(array_keys($anteriores), array_keys($nuevos))));
        sort($ids);

        $bloqueados = $this->bloquearRepuestos($ids);

        foreach ($nuevos as $repuestoId => $cantidad) {
            $repuesto = $bloqueados->get($repuestoId);

            if (! $repuesto) {
                throw ValidationException::withMessages([
                    'repuestos' => 'Uno de los repuestos seleccionados ya no existe en el inventario.',
                ]);
            }

            $disponible = (int) $repuesto->stock + (int) ($anteriores[$repuestoId] ?? 0);

            if ($cantidad > $disponible) {
                throw ValidationException::withMessages([
                    'repuestos' => "Stock insuficiente para «{$repuesto->nombre}». Disponible: {$disponible} unidades.",
                ]);
            }
        }

        foreach ($ids as $repuestoId) {
            $anterior = (int) ($anteriores[$repuestoId] ?? 0);
            $nuevo = (int) ($nuevos[$repuestoId] ?? 0);
            $diferencia = $nuevo - $anterior;

            if ($diferencia > 0) {
                Repuesto::whereKey($repuestoId)->decrement('stock', $diferencia);
            } elseif ($diferencia < 0) {
                Repuesto::whereKey($repuestoId)->increment('stock', abs($diferencia));
            }
        }

        $pivote = [];

        foreach ($nuevos as $repuestoId => $cantidad) {
            $repuesto = $bloqueados->get($repuestoId);
            $precio = (float) $repuesto->precio_unitario;

            $pivote[$repuestoId] = [
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => round($precio * $cantidad, 2),
            ];
        }

        $orden->repuestos()->sync($pivote);
    }

    /**
     * Bloquea las filas de repuestos en orden ascendente para evitar bloqueos mutuos.
     *
     * @param  array<int, int>  $ids
     * @return \Illuminate\Support\Collection<int, Repuesto>
     */
    private function bloquearRepuestos(array $ids)
    {
        if ($ids === []) {
            return collect();
        }

        return Repuesto::whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }
}
