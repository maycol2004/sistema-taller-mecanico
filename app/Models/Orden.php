<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InvalidArgumentException;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    public const ESTADOS = [
        'Pendiente',
        'En Proceso',
        'Finalizado',
        'Entregado',
    ];

    public const ESTADOS_ACTIVOS = [
        'Pendiente',
        'En Proceso',
    ];

    public const BADGES_ESTADO = [
        'Pendiente' => 'warning',
        'En Proceso' => 'info',
        'Finalizado' => 'success',
        'Entregado' => 'secondary',
    ];

    public const COLORES_ESTADO = [
        'Pendiente' => '#f59e0b',
        'En Proceso' => '#0ea5e9',
        'Finalizado' => '#10b981',
        'Entregado' => '#64748b',
    ];

    protected $fillable = [
        'codigo',
        'vehiculo_id',
        'user_id',
        'fecha_ingreso',
        'fecha_estimada_entrega',
        'fecha_entrega_real',
        'diagnostico',
        'observaciones',
        'estado',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
            'fecha_estimada_entrega' => 'date',
            'fecha_entrega_real' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'orden_servicio')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal'])
            ->withTimestamps();
    }

    public function repuestos(): BelongsToMany
    {
        return $this->belongsToMany(Repuesto::class, 'orden_repuesto')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal'])
            ->withTimestamps();
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->whereIn('estado', self::ESTADOS_ACTIVOS);
    }

    public function scopeBuscar(Builder $query, ?string $termino): Builder
    {
        $termino = trim((string) $termino);

        if ($termino === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($termino) {
            $query->where('codigo', 'ilike', "%{$termino}%")
                ->orWhere('diagnostico', 'ilike', "%{$termino}%")
                ->orWhereHas('vehiculo', function (Builder $vehiculo) use ($termino) {
                    $vehiculo->where('placa', 'ilike', "%{$termino}%")
                        ->orWhere('marca', 'ilike', "%{$termino}%")
                        ->orWhere('modelo', 'ilike', "%{$termino}%");
                })
                ->orWhereHas('vehiculo.cliente', function (Builder $cliente) use ($termino) {
                    $cliente->where('nombres', 'ilike', "%{$termino}%")
                        ->orWhere('apellidos', 'ilike', "%{$termino}%")
                        ->orWhere('documento', 'ilike', "%{$termino}%");
                });
        });
    }

    /**
     * Genera el correlativo anual de la orden (OT-AAAA-####).
     * Debe invocarse dentro de una transacción para bloquear el último código.
     */
    public static function generarCodigo(?string $anio = null): string
    {
        $anio = $anio ?? now()->format('Y');

        $ultimo = static::query()
            ->where('codigo', 'like', "OT-{$anio}-%")
            ->orderByDesc('codigo')
            ->lockForUpdate()
            ->value('codigo');

        $secuencia = $ultimo ? ((int) substr($ultimo, -4)) + 1 : 1;

        return sprintf('OT-%s-%04d', $anio, $secuencia);
    }

    public function siguienteEstado(): ?string
    {
        $indice = array_search($this->estado, self::ESTADOS, true);

        if ($indice === false) {
            return null;
        }

        return self::ESTADOS[$indice + 1] ?? null;
    }

    /**
     * Actualiza el estado y mantiene coherente la fecha real de entrega.
     */
    public function actualizarEstado(string $estado): void
    {
        if (! in_array($estado, self::ESTADOS, true)) {
            throw new InvalidArgumentException("Estado de orden no válido: {$estado}");
        }

        $this->estado = $estado;

        if ($estado === 'Entregado') {
            $entrega = $this->fecha_entrega_real ?? today();

            if ($this->fecha_ingreso && $entrega->lt($this->fecha_ingreso)) {
                $entrega = $this->fecha_ingreso->copy();
            }

            $this->fecha_entrega_real = $entrega;
        } else {
            $this->fecha_entrega_real = null;
        }

        $this->save();
    }

    /**
     * Recalcula el costo total a partir de los subtotales de los pivotes.
     */
    public function recalcularTotal(): void
    {
        $totalServicios = $this->servicios()
            ->get()
            ->sum(fn (Servicio $servicio): float => (float) $servicio->pivot->subtotal);

        $totalRepuestos = $this->repuestos()
            ->get()
            ->sum(fn (Repuesto $repuesto): float => (float) $repuesto->pivot->subtotal);

        $this->forceFill([
            'total' => round($totalServicios + $totalRepuestos, 2),
        ])->save();
    }

    protected function badgeEstado(): Attribute
    {
        return Attribute::get(
            fn (): string => self::BADGES_ESTADO[$this->estado] ?? 'light'
        );
    }

    protected function totalFormateado(): Attribute
    {
        return Attribute::get(
            fn (): string => 'S/ '.number_format((float) $this->total, 2)
        );
    }

    protected function estaActiva(): Attribute
    {
        return Attribute::get(
            fn (): bool => in_array($this->estado, self::ESTADOS_ACTIVOS, true)
        );
    }
}
