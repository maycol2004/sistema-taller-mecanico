<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'documento',
        'nombres',
        'apellidos',
        'telefono',
        'email',
        'direccion',
    ];

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }

    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(
            fn (): string => trim("{$this->nombres} {$this->apellidos}")
        );
    }

    protected function tipoDocumento(): Attribute
    {
        return Attribute::get(
            fn (): string => strlen(preg_replace('/\D/', '', $this->documento)) === 11 ? 'RUC' : 'DNI'
        );
    }

    public function scopeBuscar(Builder $query, ?string $termino): Builder
    {
        $termino = trim((string) $termino);

        if ($termino === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($termino) {
            $query->where('documento', 'ilike', "%{$termino}%")
                ->orWhere('nombres', 'ilike', "%{$termino}%")
                ->orWhere('apellidos', 'ilike', "%{$termino}%")
                ->orWhere('email', 'ilike', "%{$termino}%")
                ->orWhere('telefono', 'ilike', "%{$termino}%");
        });
    }
}
