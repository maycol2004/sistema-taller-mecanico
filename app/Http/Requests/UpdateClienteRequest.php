<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateClienteRequest extends StoreClienteRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $cliente = $this->route('cliente');

        $rules['documento'] = [
            'required',
            'string',
            'max:20',
            'regex:/^(?:\d{8}|\d{11})$/',
            Rule::unique('clientes', 'documento')->ignore($cliente?->id),
        ];

        return $rules;
    }
}
