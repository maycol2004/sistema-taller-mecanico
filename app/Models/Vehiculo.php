<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'cliente_id',
        'placa',
        'marca',
        'modelo',
        'anio',
        'color',
        'kilometraje',
    ];

    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'kilometraje' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Vehiculo $vehiculo): void {
            $vehiculo->placa = strtoupper(trim((string) $vehiculo->placa));
        });
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(Orden::class);
    }

    protected function descripcion(): Attribute
    {
        return Attribute::get(
            fn (): string => "{$this->marca} {$this->modelo} ({$this->anio})"
        );
    }

    public function scopeBuscar(Builder $query, ?string $termino): Builder
    {
        $termino = trim((string) $termino);

        if ($termino === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($termino) {
            $query->where('placa', 'ilike', "%{$termino}%")
                ->orWhere('marca', 'ilike', "%{$termino}%")
                ->orWhere('modelo', 'ilike', "%{$termino}%")
                ->orWhereHas('cliente', function (Builder $cliente) use ($termino) {
                    $cliente->where('nombres', 'ilike', "%{$termino}%")
                        ->orWhere('apellidos', 'ilike', "%{$termino}%")
                        ->orWhere('documento', 'ilike', "%{$termino}%");
                });
        });
    }
}
