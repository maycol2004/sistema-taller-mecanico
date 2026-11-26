<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'numeric' => 'El campo :attribute debe ser un número válido.',
            'email' => 'Ingresa un correo electrónico válido.',
            'unique' => 'El :attribute ya está registrado.',
            'exists' => 'El :attribute seleccionado no es válido.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'after_or_equal' => 'El campo :attribute debe ser igual o posterior a :date.',
            'regex' => 'El formato de :attribute no es válido.',
            'in' => 'El valor seleccionado en :attribute no es válido.',
            'array' => 'El campo :attribute debe ser una lista válida.',
            'distinct' => 'El campo :attribute contiene un valor duplicado.',
            'max.string' => 'El campo :attribute no debe superar los :max caracteres.',
            'max.numeric' => 'El campo :attribute no debe ser mayor que :max.',
            'max.integer' => 'El campo :attribute no debe ser mayor que :max.',
            'min.numeric' => 'El campo :attribute debe ser mayor o igual que :min.',
            'min.integer' => 'El campo :attribute debe ser mayor o igual que :min.',
            'between.numeric' => 'El campo :attribute debe estar entre :min y :max.',
            'between.integer' => 'El campo :attribute debe estar entre :min y :max.',
            'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
        ];
    }
}
