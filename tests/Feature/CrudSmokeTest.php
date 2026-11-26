<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Orden;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CrudSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    public function test_paginas_principales_cargan_correctamente(): void
    {
        $cliente = Cliente::firstOrFail();
        $vehiculo = Vehiculo::firstOrFail();
        $servicio = Servicio::firstOrFail();
        $repuesto = Repuesto::firstOrFail();
        $orden = Orden::firstOrFail();

        $rutas = [
            route('dashboard'),
            route('clientes.index'),
            route('clientes.create'),
            route('clientes.show', $cliente),
            route('clientes.edit', $cliente),
            route('vehiculos.index'),
            route('vehiculos.create'),
            route('vehiculos.show', $vehiculo),
            route('vehiculos.edit', $vehiculo),
            route('servicios.index'),
            route('servicios.create'),
            route('servicios.show', $servicio),
            route('servicios.edit', $servicio),
            route('repuestos.index'),
            route('repuestos.create'),
            route('repuestos.show', $repuesto),
            route('repuestos.edit', $repuesto),
            route('ordenes.index'),
            route('ordenes.create'),
            route('ordenes.show', $orden),
            route('ordenes.edit', $orden),
            route('ordenes.imprimir', $orden),
            route('reportes.index'),
        ];

        foreach ($rutas as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_crud_de_clientes_completo(): void
    {
        $this->post(route('clientes.store'), [
            'documento' => '76543210',
            'nombres' => 'Ana LucÃ­a',
            'apellidos' => 'Rojas Medina',
            'telefono' => '987111222',
            'email' => 'ana.rojas@example.com',
            'direccion' => 'Av. Los Ãlamos 123',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $cliente = Cliente::where('documento', '76543210')->firstOrFail();
        $this->get(route('clientes.show', $cliente))->assertOk()->assertSee('Ana LucÃ­a');

        $this->put(route('clientes.update', $cliente), [
            'documento' => '76543210',
            'nombres' => 'Ana LucÃ­a',
            'apellidos' => 'Rojas Medina',
            'telefono' => '999888777',
            'email' => 'ana.rojas@example.com',
            'direccion' => 'Av. Los Ãlamos 456',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'telefono' => '999888777']);

        $this->delete(route('clientes.destroy', $cliente))->assertRedirect(route('clientes.index'));
        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }

    public function test_crud_de_vehiculos_completo(): void
    {
        $cliente = Cliente::firstOrFail();

        $this->post(route('vehiculos.store'), [
            'cliente_id' => $cliente->id,
            'placa' => 'TST-999',
            'marca' => 'Mazda',
            'modelo' => 'CX-5',
            'anio' => now()->year,
            'color' => 'Rojo',
            'kilometraje' => 15000,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $vehiculo = Vehiculo::where('placa', 'TST-999')->firstOrFail();
        $this->get(route('vehiculos.show', $vehiculo))->assertOk()->assertSee('TST-999');

        $this->put(route('vehiculos.update', $vehiculo), [
            'cliente_id' => $cliente->id,
            'placa' => 'TST-999',
            'marca' => 'Mazda',
            'modelo' => 'CX-5',
            'anio' => now()->year,
            'color' => 'Azul',
            'kilometraje' => 18500,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('vehiculos', ['id' => $vehiculo->id, 'color' => 'Azul', 'kilometraje' => 18500]);

        $this->delete(route('vehiculos.destroy', $vehiculo))->assertRedirect(route('vehiculos.index'));
        $this->assertDatabaseMissing('vehiculos', ['id' => $vehiculo->id]);
    }

    public function test_crud_de_servicios_completo(): void
    {
        $this->post(route('servicios.store'), [
            'nombre' => 'AlineaciÃ³n y balanceo',
            'descripcion' => 'Ajuste de direcciÃ³n y neumÃ¡ticos',
            'precio_base' => 90.50,
            'duracion_estimada' => '1 hora',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $servicio = Servicio::where('nombre', 'AlineaciÃ³n y balanceo')->firstOrFail();

        $this->put(route('servicios.update', $servicio), [
            'nombre' => 'AlineaciÃ³n y balanceo',
            'descripcion' => 'Ajuste de direcciÃ³n y neumÃ¡ticos',
            'precio_base' => 110,
            'duracion_estimada' => '1 hora 15 minutos',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('servicios', ['id' => $servicio->id, 'precio_base' => '110.00']);

        $this->delete(route('servicios.destroy', $servicio))->assertRedirect(route('servicios.index'));
        $this->assertDatabaseMissing('servicios', ['id' => $servicio->id]);
    }

    public function test_crud_de_repuestos_completo(): void
    {
        $this->post(route('repuestos.store'), [
            'codigo' => 'TST-REP-01',
            'nombre' => 'BujÃ­a de prueba',
            'marca' => 'NGK',
            'precio_unitario' => 25.90,
            'stock' => 40,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $repuesto = Repuesto::where('codigo', 'TST-REP-01')->firstOrFail();

        $this->put(route('repuestos.update', $repuesto), [
            'codigo' => 'TST-REP-01',
            'nombre' => 'BujÃ­a de prueba',
            'marca' => 'NGK',
            'precio_unitario' => 29.50,
            'stock' => 35,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => 35]);

        $this->delete(route('repuestos.destroy', $repuesto))->assertRedirect(route('repuestos.index'));
        $this->assertDatabaseMissing('repuestos', ['id' => $repuesto->id]);
    }

    public function test_crud_de_ordenes_completo_con_calculo_de_total(): void
    {
        $cliente = Cliente::firstOrFail();
        $vehiculo = Vehiculo::where('cliente_id', $cliente->id)->first();
        $vehiculo ??= Vehiculo::firstOrFail();
        $servicio = Servicio::firstOrFail();
        $repuesto = Repuesto::where('stock', '>=', 3)->firstOrFail();
        $stockInicial = (int) $repuesto->stock;

        $this->post(route('ordenes.store'), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(3)->toDateString(),
            'diagnostico' => 'RevisiÃ³n general de prueba',
            'observaciones' => 'Cliente solicita entrega puntual',
            'estado' => 'Pendiente',
            'servicios' => [
                ['servicio_id' => $servicio->id, 'cantidad' => 2],
            ],
            'repuestos' => [
                ['repuesto_id' => $repuesto->id, 'cantidad' => 3],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $orden = Orden::where('diagnostico', 'RevisiÃ³n general de prueba')->firstOrFail();

        $esperado = round(((float) $servicio->precio_base * 2) + ((float) $repuesto->precio_unitario * 3), 2);

        $this->assertEqualsWithDelta($esperado, (float) $orden->total, 0.01);
        $this->assertSame(2, (int) $orden->servicios()->firstOrFail()->pivot->cantidad);
        $this->assertSame(3, (int) $orden->repuestos()->firstOrFail()->pivot->cantidad);
        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => $stockInicial - 3]);

        $this->get(route('ordenes.show', $orden))->assertOk()->assertSee($orden->codigo);

        $this->patch(route('ordenes.estado', $orden), ['estado' => 'En Proceso'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('ordenes', ['id' => $orden->id, 'estado' => 'En Proceso']);

        $this->put(route('ordenes.update', $orden), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(4)->toDateString(),
            'diagnostico' => 'DiagnÃ³stico actualizado',
            'observaciones' => null,
            'estado' => 'Finalizado',
            'servicios' => [
                ['servicio_id' => $servicio->id, 'cantidad' => 1],
            ],
            'repuestos' => [],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $orden->refresh();
        $this->assertEqualsWithDelta((float) $servicio->precio_base, (float) $orden->total, 0.01);
        $this->assertSame(0, $orden->repuestos()->count());
        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => $stockInicial]);

        $this->delete(route('ordenes.destroy', $orden))->assertRedirect(route('ordenes.index'));
        $this->assertDatabaseMissing('ordenes', ['id' => $orden->id]);
        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => $stockInicial]);
    }

    public function test_editar_orden_ajusta_el_stock_solo_por_la_diferencia(): void
    {
        $cliente = Cliente::firstOrFail();
        $vehiculo = Vehiculo::where('cliente_id', $cliente->id)->firstOrFail();
        $servicio = Servicio::firstOrFail();
        $repuesto = Repuesto::where('stock', '>=', 6)->firstOrFail();
        $stockInicial = (int) $repuesto->stock;

        $this->post(route('ordenes.store'), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(2)->toDateString(),
            'estado' => 'Pendiente',
            'servicios' => [
                ['servicio_id' => $servicio->id, 'cantidad' => 1],
            ],
            'repuestos' => [
                ['repuesto_id' => $repuesto->id, 'cantidad' => 2],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $orden = Orden::latest('id')->firstOrFail();
        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => $stockInicial - 2]);

        $this->put(route('ordenes.update', $orden), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(2)->toDateString(),
            'estado' => 'Pendiente',
            'servicios' => [
                ['servicio_id' => $servicio->id, 'cantidad' => 1],
            ],
            'repuestos' => [
                ['repuesto_id' => $repuesto->id, 'cantidad' => 5],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => $stockInicial - 5]);

        $this->put(route('ordenes.update', $orden), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(2)->toDateString(),
            'estado' => 'Pendiente',
            'servicios' => [
                ['servicio_id' => $servicio->id, 'cantidad' => 1],
            ],
            'repuestos' => [
                ['repuesto_id' => $repuesto->id, 'cantidad' => 1],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => $stockInicial - 1]);

        $this->delete(route('ordenes.destroy', $orden))->assertRedirect(route('ordenes.index'));
        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => $stockInicial]);
    }

    public function test_la_edicion_considera_el_stock_ya_reservado_por_la_orden(): void
    {
        $cliente = Cliente::firstOrFail();
        $vehiculo = Vehiculo::where('cliente_id', $cliente->id)->firstOrFail();
        $repuesto = Repuesto::where('stock', '>=', 2)->firstOrFail();
        $stockInicial = (int) $repuesto->stock;

        $this->post(route('ordenes.store'), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(2)->toDateString(),
            'estado' => 'Pendiente',
            'servicios' => [],
            'repuestos' => [
                ['repuesto_id' => $repuesto->id, 'cantidad' => 2],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $orden = Orden::latest('id')->firstOrFail();

        $this->put(route('ordenes.update', $orden), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(2)->toDateString(),
            'estado' => 'Pendiente',
            'servicios' => [],
            'repuestos' => [
                ['repuesto_id' => $repuesto->id, 'cantidad' => $stockInicial],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => 0]);

        $this->put(route('ordenes.update', $orden), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_estimada_entrega' => now()->addDays(2)->toDateString(),
            'estado' => 'Pendiente',
            'servicios' => [],
            'repuestos' => [
                ['repuesto_id' => $repuesto->id, 'cantidad' => $stockInicial + 1],
            ],
        ])->assertSessionHasErrors('repuestos.0.cantidad');

        $this->assertDatabaseHas('repuestos', ['id' => $repuesto->id, 'stock' => 0]);
    }

    public function test_flujo_de_estados_registra_y_limpia_la_fecha_real_de_entrega(): void
    {
        $orden = Orden::where('estado', 'Pendiente')->firstOrFail();
        $orden->update(['fecha_entrega_real' => null]);

        $this->patch(route('ordenes.avanzar', $orden))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('ordenes', ['id' => $orden->id, 'estado' => 'En Proceso']);

        $this->patch(route('ordenes.avanzar', $orden))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('ordenes', ['id' => $orden->id, 'estado' => 'Finalizado']);

        $this->patch(route('ordenes.avanzar', $orden))->assertRedirect()->assertSessionHasNoErrors();
        $orden->refresh();

        $this->assertSame('Entregado', $orden->estado);
        $this->assertNotNull($orden->fecha_entrega_real);
        $this->assertTrue($orden->fecha_entrega_real->isSameDay(today()));

        $this->patch(route('ordenes.estado', $orden), ['estado' => 'Finalizado'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertNull($orden->fresh()->fecha_entrega_real);
    }

    public function test_no_permite_eliminar_cliente_con_vehiculos(): void
    {
        $cliente = Cliente::whereHas('vehiculos')->firstOrFail();

        $this->from(route('clientes.index'))
            ->delete(route('clientes.destroy', $cliente))
            ->assertRedirect(route('clientes.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
    }

    public function test_no_permite_documento_duplicado_ni_stock_insuficiente(): void
    {
        $existente = Cliente::firstOrFail();

        $this->post(route('clientes.store'), [
            'documento' => $existente->documento,
            'nombres' => 'Duplicado',
            'apellidos' => 'Prueba',
        ])->assertSessionHasErrors('documento');

        $vehiculo = Vehiculo::firstOrFail();
        $servicio = Servicio::firstOrFail();
        $sinStock = Repuesto::orderBy('stock')->firstOrFail();

        $this->post(route('ordenes.store'), [
            'vehiculo_id' => $vehiculo->id,
            'fecha_ingreso' => now()->toDateString(),
            'estado' => 'Pendiente',
            'servicios' => [
                ['servicio_id' => $servicio->id, 'cantidad' => 1],
            ],
            'repuestos' => [
                ['repuesto_id' => $sinStock->id, 'cantidad' => $sinStock->stock + 1],
            ],
        ])->assertSessionHasErrors();
    }

    public function test_el_mecanico_no_puede_acceder_a_acciones_de_administrador(): void
    {
        $mecanico = User::factory()->mecanico()->create();
        $cliente = Cliente::firstOrFail();
        $servicio = Servicio::firstOrFail();
        $repuesto = Repuesto::firstOrFail();
        $orden = Orden::firstOrFail();

        $this->actingAs($mecanico);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('ordenes.create'))->assertOk();
        $this->get(route('servicios.index'))->assertOk();
        $this->get(route('servicios.show', $servicio))->assertOk();
        $this->get(route('repuestos.index'))->assertOk();
        $this->get(route('repuestos.show', $repuesto))->assertOk();

        $this->get(route('servicios.create'))->assertForbidden();
        $this->get(route('servicios.edit', $servicio))->assertForbidden();
        $this->get(route('repuestos.create'))->assertForbidden();
        $this->get(route('repuestos.edit', $repuesto))->assertForbidden();

        $this->delete(route('clientes.destroy', $cliente))->assertForbidden();
        $this->delete(route('ordenes.destroy', $orden))->assertForbidden();
        $this->delete(route('servicios.destroy', $servicio))->assertForbidden();
        $this->delete(route('repuestos.destroy', $repuesto))->assertForbidden();

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
        $this->assertDatabaseHas('ordenes', ['id' => $orden->id]);
    }
}

