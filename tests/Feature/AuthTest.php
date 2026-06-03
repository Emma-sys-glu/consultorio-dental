<?php

namespace Tests\Feature;

use App\Models\Dentista;
use App\Models\Paciente;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pruebas de autenticación y redirección por rol.
 *
 * Cubre: login exitoso, credenciales incorrectas, redirección
 * diferenciada por rol y protección de rutas autenticadas.
 */
class AuthTest extends TestCase
{
    // ── Helpers ───────────────────────────────────────────────────────

    private function crearAdmin(): User
    {
        return User::factory()->create(['rol' => 'administrador']);
    }

    private function crearDentista(): User
    {
        $dentista = Dentista::factory()->create();
        return User::factory()->create([
            'rol'         => 'dentista',
            'dentista_id' => $dentista->id,
        ]);
    }

    private function crearPaciente(): User
    {
        $paciente = Paciente::factory()->create();
        return User::factory()->create([
            'rol'        => 'paciente',
            'paciente_id' => $paciente->id,
        ]);
    }

    // ── Login ─────────────────────────────────────────────────────────

    #[Test]
    public function login_con_credenciales_correctas_autentica_al_usuario(): void
    {
        $user = $this->crearAdmin();

        $response = $this->post(route('login.procesar'), [
            'email'    => $user->email,
            'password' => 'password', // valor por defecto del UserFactory
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_con_password_incorrecto_rechaza_el_acceso(): void
    {
        $user = $this->crearAdmin();

        $response = $this->post(route('login.procesar'), [
            'email'    => $user->email,
            'password' => 'clave_incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function login_con_email_inexistente_rechaza_el_acceso(): void
    {
        $response = $this->post(route('login.procesar'), [
            'email'    => 'no_existe@dentaltec.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function login_sin_email_falla_validacion(): void
    {
        $response = $this->post(route('login.procesar'), [
            'email'    => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ── Redirección por rol ───────────────────────────────────────────

    #[Test]
    public function admin_es_redirigido_a_su_dashboard_tras_login(): void
    {
        $user = $this->crearAdmin();

        $this->post(route('login.procesar'), [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function dentista_es_redirigido_a_su_dashboard_tras_login(): void
    {
        $user = $this->crearDentista();

        $this->post(route('login.procesar'), [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard.dentista'));
    }

    #[Test]
    public function paciente_es_redirigido_a_su_dashboard_tras_login(): void
    {
        $user = $this->crearPaciente();

        $this->post(route('login.procesar'), [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard.paciente'));
    }

    // ── Logout ────────────────────────────────────────────────────────

    #[Test]
    public function logout_cierra_sesion_y_redirige_a_login(): void
    {
        $user = $this->crearAdmin();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // ── Protección de rutas ───────────────────────────────────────────

    #[Test]
    public function usuario_no_autenticado_no_puede_ver_el_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_no_autenticado_no_puede_ver_lista_de_pacientes(): void
    {
        $this->get(route('pacientes.vista'))
            ->assertRedirect(route('login'));
    }
}
