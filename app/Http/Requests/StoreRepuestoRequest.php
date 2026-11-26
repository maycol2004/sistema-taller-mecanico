<?php

namespace App\Http\Requests;

class StoreRepuestoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9-]+$/', 'unique:repuestos,codigo'],
            'nombre' => ['required', 'string', 'max:120'],
            'marca' => ['nullable', 'string', 'max:60'],
            'precio_unitario' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre del repuesto',
            'marca' => 'marca',
            'precio_unitario' => 'precio unitario',
            'stock' => 'stock',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => strtoupper(preg_replace('/\s+/', '', (string) $this->input('codigo'))),
            'nombre' => trim((string) $this->input('nombre')),
            'marca' => $this->filled('marca') ? trim((string) $this->input('marca')) : null,
        ]);
    }
}
