<?php

namespace App\Http\Requests;

use App\Models\Orden;
use App\Models\Repuesto;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreOrdenRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'vehiculo_id' => ['required', 'integer', 'exists:vehiculos,id'],
            'fecha_ingreso' => ['required', 'date', 'before_or_equal:today'],
            'fecha_estimada_entrega' => ['nullable', 'date', 'after_or_equal:fecha_ingreso'],
            'diagnostico' => ['nullable', 'string', 'max:2000'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'estado' => ['required', Rule::in(Orden::ESTADOS)],
            'servicios' => ['nullable', 'array'],
            'servicios.*.servicio_id' => ['required', 'integer', 'distinct', 'exists:servicios,id'],
            'servicios.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
            'repuestos' => ['nullable', 'array'],
            'repuestos.*.repuesto_id' => ['required', 'integer', 'distinct', 'exists:repuestos,id'],
            'repuestos.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser posterior a hoy.',
            'fecha_estimada_entrega.after_or_equal' => 'La fecha estimada de entrega debe ser igual o posterior a la fecha de ingreso.',
            'servicios.*.servicio_id.distinct' => 'No puedes repetir el mismo servicio en una orden.',
            'repuestos.*.repuesto_id.distinct' => 'No puedes repetir el mismo repuesto en una orden.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'vehiculo_id' => 'vehículo',
            'fecha_ingreso' => 'fecha de ingreso',
            'fecha_estimada_entrega' => 'fecha estimada de entrega',
            'diagnostico' => 'diagnóstico',
            'observaciones' => 'observaciones',
            'estado' => 'estado',
            'servicios.*.servicio_id' => 'servicio',
            'servicios.*.cantidad' => 'cantidad del servicio',
            'repuestos.*.repuesto_id' => 'repuesto',
            'repuestos.*.cantidad' => 'cantidad del repuesto',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('repuestos', []) as $indice => $item) {
                $repuesto = Repuesto::find($item['repuesto_id'] ?? null);

                if (! $repuesto) {
                    continue;
                }

                $disponible = (int) $repuesto->stock + $this->cantidadAsignada($repuesto->id);

                if ((int) ($item['cantidad'] ?? 0) > $disponible) {
                    $validator->errors()->add(
                        "repuestos.{$indice}.cantidad",
                        "Stock insuficiente para «{$repuesto->nombre}». Disponible: {$disponible} unidades."
                    );
                }
            }
        });
    }

    /**
     * En edición, el stock disponible incluye las unidades ya reservadas por la orden.
     */
    private function cantidadAsignada(int $repuestoId): int
    {
        $orden = $this->route('orden');

        if (! $orden instanceof Orden || ! $orden->exists) {
            return 0;
        }

        $asignado = $orden->repuestos()
            ->where('repuestos.id', $repuestoId)
            ->first();

        return (int) ($asignado?->pivot?->cantidad ?? 0);
    }

    protected function prepareForValidation(): void
    {
        $servicios = collect($this->input('servicios', []))
            ->filter(fn ($item): bool => is_array($item) && filled($item['servicio_id'] ?? null))
            ->map(fn (array $item): array => [
                'servicio_id' => (int) $item['servicio_id'],
                'cantidad' => (int) ($item['cantidad'] ?? 1),
            ])
            ->values()
            ->all();

        $repuestos = collect($this->input('repuestos', []))
            ->filter(fn ($item): bool => is_array($item) && filled($item['repuesto_id'] ?? null))
            ->map(fn (array $item): array => [
                'repuesto_id' => (int) $item['repuesto_id'],
                'cantidad' => (int) ($item['cantidad'] ?? 1),
            ])
            ->values()
            ->all();

        $this->merge([
            'diagnostico' => $this->filled('diagnostico') ? trim((string) $this->input('diagnostico')) : null,
            'observaciones' => $this->filled('observaciones') ? trim((string) $this->input('observaciones')) : null,
            'servicios' => $servicios,
            'repuestos' => $repuestos,
        ]);
    }
}
