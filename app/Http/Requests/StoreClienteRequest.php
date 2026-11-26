<?php

namespace App\Http\Requests;

class StoreClienteRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'documento' => ['required', 'string', 'max:20', 'regex:/^(?:\d{8}|\d{11})$/', 'unique:clientes,documento'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'documento' => 'documento',
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'direccion' => 'dirección',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'documento' => preg_replace('/\s+/', '', (string) $this->input('documento')),
            'nombres' => trim((string) $this->input('nombres')),
            'apellidos' => trim((string) $this->input('apellidos')),
            'telefono' => $this->filled('telefono') ? trim((string) $this->input('telefono')) : null,
            'email' => $this->filled('email') ? mb_strtolower(trim((string) $this->input('email'))) : null,
            'direccion' => $this->filled('direccion') ? trim((string) $this->input('direccion')) : null,
        ]);
    }
}
