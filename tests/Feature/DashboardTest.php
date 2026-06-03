<?php

namespace Tests\Feature;

use App\Models\Dentista;
use App\Models\Paciente;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pruebas de acceso al dashboard por rol.
 *
 * Verifica que cada rol ve exactamente su propio dashboard
 * y que el sistema rechaza o redirige correctamente accesos
 * a dashboards que no corresponden.
 */
class DashboardTest extends TestCase
{
    // ── Helpers ───────────────────────────────────────────────────────

    private function admin(): User
    {
        return User::factory()->create(['rol' => 'administrador']);
    }

    private function recepcionista(): User
    {
        return User::factory()->create(['rol' => 'recepcionista']);
    }

    private function dentista(): User
    {
        $d = Dentista::factory()->create();
        return User::factory()->create(['rol' => 'dentista', 'dentista_id' => $d->id]);
    }

    private function paciente(): User
    {
        $p = Paciente::factory()->create();
        return User::factory()->create(['rol' => 'paciente', 'paciente_id' => $p->id]);
    }

    // ── Acceso correcto ───────────────────────────────────────────────

    #[Test]
    public function admin_ve_el_dashboard_de_administrador(): void
    {
        $this->actingAs($this->admin())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard');
    }

    #[Test]
    public function recepcionista_ve_el_dashboard_de_administrador(): void
    {
        $this->actingAs($this->recepcionista())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard');
    }

    #[Test]
    public function dentista_ve_su_propio_dashboard(): void
    {
        $this->actingAs($this->dentista())
            ->get(route('dashboard.dentista'))
            ->assertOk()
            ->assertViewIs('dashboard-dentista');
    }

    #[Test]
    public function paciente_ve_su_propio_dashboard(): void
    {
        $this->actingAs($this->paciente())
            ->get(route('dashboard.paciente'))
            ->assertOk()
            ->assertViewIs('dashboard-paciente');
    }

    // ── Redirección automática al dashboard correcto ──────────────────

    #[Test]
    public function admin_en_raiz_es_redirigido_a_dashboard_admin(): void
    {
        $this->actingAs($this->admin())
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function dentista_en_raiz_es_redirigido_a_su_dashboard(): void
    {
        $this->actingAs($this->dentista())
            ->get('/')
            ->assertRedirect(route('dashboard.dentista'));
    }

    #[Test]
    public function paciente_en_raiz_es_redirigido_a_su_dashboard(): void
    {
        $this->actingAs($this->paciente())
            ->get('/')
            ->assertRedirect(route('dashboard.paciente'));
    }

    // ── Redirección cuando accede al dashboard incorrecto ────────────

    #[Test]
    public function dentista_que_accede_a_dashboard_admin_es_redirigido(): void
    {
        // El controlador detecta el rol incorrecto y redirige
        $this->actingAs($this->dentista())
            ->get(route('dashboard'))
            ->assertRedirect(route('dashboard.dentista'));
    }

    #[Test]
    public function paciente_que_accede_a_dashboard_admin_es_redirigido(): void
    {
        $this->actingAs($this->paciente())
            ->get(route('dashboard'))
            ->assertRedirect(route('dashboard.paciente'));
    }

    // ── Sin autenticación ─────────────────────────────────────────────

    #[Test]
    public function usuario_no_autenticado_es_redirigido_a_login_desde_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_no_autenticado_es_redirigido_a_login_desde_dashboard_dentista(): void
    {
        $this->get(route('dashboard.dentista'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_no_autenticado_es_redirigido_a_login_desde_dashboard_paciente(): void
    {
        $this->get(route('dashboard.paciente'))
            ->assertRedirect(route('login'));
    }
}
