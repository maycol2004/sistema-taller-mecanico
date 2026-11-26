<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateRepuestoRequest extends StoreRepuestoRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $repuesto = $this->route('repuesto');

        $rules['codigo'] = [
            'required',
            'string',
            'max:30',
            'regex:/^[A-Z0-9-]+$/',
            Rule::unique('repuestos', 'codigo')->ignore($repuesto?->id),
        ];

        return $rules;
    }
}
