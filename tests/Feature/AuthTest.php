<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_el_formulario_de_login_carga_para_invitados(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    public function test_un_usuario_puede_iniciar_sesion_y_cerrar_sesion(): void
    {
        $usuario = User::factory()->admin()->create([
            'email' => 'admin.login@test.com',
            'password' => 'secreto123',
        ]);

        $this->post(route('login.store'), [
            'email' => 'admin.login@test.com',
            'password' => 'secreto123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($usuario);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_credenciales_invalidas_muestran_un_error(): void
    {
        User::factory()->create([
            'email' => 'mecanico.login@test.com',
            'password' => 'secreto123',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'mecanico.login@test.com',
                'password' => 'incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_usuario_autenticado_es_redirigido_del_login_al_panel(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }
}
