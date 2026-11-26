<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateVehiculoRequest extends StoreVehiculoRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $vehiculo = $this->route('vehiculo');

        $rules['placa'] = [
            'required',
            'string',
            'max:10',
            'regex:/^[A-Z0-9-]{5,10}$/',
            Rule::unique('vehiculos', 'placa')->ignore($vehiculo?->id),
        ];

        return $rules;
    }
}
