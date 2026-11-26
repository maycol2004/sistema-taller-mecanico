<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Repuesto extends Model
{
    use HasFactory;

    protected $table = 'repuestos';

    public const STOCK_BAJO = 5;

    protected $fillable = [
        'codigo',
        'nombre',
        'marca',
        'precio_unitario',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function ordenes(): BelongsToMany
    {
        return $this->belongsToMany(Orden::class, 'orden_repuesto')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal'])
            ->withTimestamps();
    }

    public function scopeStockBajo(Builder $query): Builder
    {
        return $query->where('stock', '<=', self::STOCK_BAJO);
    }

    public function scopeBuscar(Builder $query, ?string $termino): Builder
    {
        $termino = trim((string) $termino);

        if ($termino === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($termino) {
            $query->where('codigo', 'ilike', "%{$termino}%")
                ->orWhere('nombre', 'ilike', "%{$termino}%")
                ->orWhere('marca', 'ilike', "%{$termino}%");
        });
    }
}
