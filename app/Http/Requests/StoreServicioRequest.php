<?php

namespace App\Http\Requests;

class StoreServicioRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120', 'unique:servicios,nombre'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'precio_base' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'duracion_estimada' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del servicio',
            'descripcion' => 'descripción',
            'precio_base' => 'precio base',
            'duracion_estimada' => 'duración estimada',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim((string) $this->input('nombre')),
            'descripcion' => $this->filled('descripcion') ? trim((string) $this->input('descripcion')) : null,
            'duracion_estimada' => $this->filled('duracion_estimada') ? trim((string) $this->input('duracion_estimada')) : null,
        ]);
    }
}
