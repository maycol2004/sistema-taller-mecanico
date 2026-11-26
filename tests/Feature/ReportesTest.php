<?php

namespace Tests\Feature;

use App\Models\Orden;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    public function test_el_reporte_carga_con_el_resumen_del_periodo(): void
    {
        $this->get(route('reportes.index'))
            ->assertOk()
            ->assertSee('Reporte')
            ->assertSee('Ingresos del periodo')
            ->assertSee('Inventario valorizado');
    }

    public function test_el_reporte_se_exporta_en_csv_compatible_con_excel(): void
    {
        $respuesta = $this->get(route('reportes.exportar', [
            'desde' => now()->startOfMonth()->toDateString(),
            'hasta' => now()->toDateString(),
        ]));

        $respuesta->assertOk();

        $this->assertStringContainsString('text/csv', (string) $respuesta->headers->get('content-type'));

        $contenido = $respuesta->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $contenido);
        $this->assertStringContainsString(';', $contenido);
        $this->assertStringContainsString('RESUMEN', $contenido);
        $this->assertStringContainsString('TOP SERVICIOS', $contenido);
        $this->assertStringContainsString('INVENTARIO VALORIZADO', $contenido);
    }

    public function test_el_reporte_rechaza_un_rango_de_fechas_incoherente(): void
    {
        $this->from(route('reportes.index'))
            ->get(route('reportes.index', [
                'desde' => now()->toDateString(),
                'hasta' => now()->subDays(5)->toDateString(),
            ]))
            ->assertRedirect(route('reportes.index'))
            ->assertSessionHasErrors('desde');
    }

    public function test_el_mecanico_puede_consultar_reportes_pero_no_reponer_stock(): void
    {
        $mecanico = User::factory()->mecanico()->create();
        $repuesto = Repuesto::firstOrFail();

        $this->actingAs($mecanico)
            ->get(route('reportes.index'))
            ->assertOk();

        $this->actingAs($mecanico)
            ->post(route('repuestos.reponer', $repuesto), ['cantidad' => 5])
            ->assertForbidden();
    }

    public function test_el_admin_puede_reponer_stock_desde_el_inventario(): void
    {
        $repuesto = Repuesto::firstOrFail();
        $stockInicial = (int) $repuesto->stock;

        $this->post(route('repuestos.reponer', $repuesto), ['cantidad' => 5])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repuestos', [
            'id' => $repuesto->id,
            'stock' => $stockInicial + 5,
        ]);
    }

    public function test_las_ordenes_se_filtran_por_rango_de_fechas_de_ingreso(): void
    {
        $orden = Orden::firstOrFail();
        $fecha = $orden->fecha_ingreso->toDateString();

        $this->get(route('ordenes.index', ['desde' => $fecha, 'hasta' => $fecha]))
            ->assertOk()
            ->assertSee($orden->codigo);

        $this->get(route('ordenes.index', [
            'desde' => now()->addYear()->startOfYear()->toDateString(),
            'hasta' => now()->addYear()->endOfYear()->toDateString(),
        ]))
            ->assertOk()
            ->assertDontSee($orden->codigo);
    }

    public function test_los_servicios_se_filtran_por_rango_de_precio(): void
    {
        $sufijo = (string) random_int(100000, 999999);
        $nombre = 'Servicio de prueba '.$sufijo;

        Servicio::create([
            'nombre' => $nombre,
            'descripcion' => 'Servicio creado para probar el filtro de precios.',
            'precio_base' => 450,
            'duracion_estimada' => '1 hora',
        ]);

        $this->get(route('servicios.index', ['precio_min' => 400, 'precio_max' => 500]))
            ->assertOk()
            ->assertSee($nombre);

        $this->get(route('servicios.index', ['precio_min' => 600, 'precio_max' => 700]))
            ->assertOk()
            ->assertDontSee($nombre);
    }

    public function test_el_filtro_de_precios_rechaza_un_rango_invertido(): void
    {
        $this->from(route('servicios.index'))
            ->get(route('servicios.index', ['precio_min' => 500, 'precio_max' => 100]))
            ->assertRedirect(route('servicios.index'))
            ->assertSessionHasErrors('precio_min');
    }
}
