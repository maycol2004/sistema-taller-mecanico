<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateServicioRequest extends StoreServicioRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $servicio = $this->route('servicio');

        $rules['nombre'] = [
            'required',
            'string',
            'max:120',
            Rule::unique('servicios', 'nombre')->ignore($servicio?->id),
        ];

        return $rules;
    }
}
