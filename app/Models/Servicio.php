<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_base',
        'duracion_estimada',
    ];

    protected function casts(): array
    {
        return [
            'precio_base' => 'decimal:2',
        ];
    }

    public function ordenes(): BelongsToMany
    {
        return $this->belongsToMany(Orden::class, 'orden_servicio')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal'])
            ->withTimestamps();
    }

    public function scopeBuscar(Builder $query, ?string $termino): Builder
    {
        $termino = trim((string) $termino);

        if ($termino === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($termino) {
            $query->where('nombre', 'ilike', "%{$termino}%")
                ->orWhere('descripcion', 'ilike', "%{$termino}%");
        });
    }
}
