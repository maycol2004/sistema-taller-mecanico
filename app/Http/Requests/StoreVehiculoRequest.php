<?php

namespace App\Http\Requests;

class StoreVehiculoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'placa' => ['required', 'string', 'max:10', 'regex:/^[A-Z0-9-]{5,10}$/', 'unique:vehiculos,placa'],
            'marca' => ['required', 'string', 'max:50'],
            'modelo' => ['required', 'string', 'max:50'],
            'anio' => ['required', 'integer', 'between:1900,'.(now()->year + 1)],
            'color' => ['nullable', 'string', 'max:30'],
            'kilometraje' => ['required', 'integer', 'min:0', 'max:2000000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'placa' => 'placa',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'anio' => 'año',
            'color' => 'color',
            'kilometraje' => 'kilometraje',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'placa' => strtoupper(preg_replace('/\s+/', '', (string) $this->input('placa'))),
            'marca' => trim((string) $this->input('marca')),
            'modelo' => trim((string) $this->input('modelo')),
            'color' => $this->filled('color') ? trim((string) $this->input('color')) : null,
        ]);
    }
}
