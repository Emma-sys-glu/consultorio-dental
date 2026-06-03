<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CitaTest extends TestCase
{
    private Dentista $dentista;
    private Paciente $paciente;
    private User     $admin;
    private User     $usuarioPaciente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dentista = Dentista::factory()->create([
            'horario_inicio' => '08:00:00',
            'horario_fin'    => '18:00:00',
        ]);
        $this->paciente = Paciente::factory()->create();
        $this->admin    = User::factory()->create(['rol' => 'administrador']);

        $paciente2 = Paciente::factory()->create();
        $this->usuarioPaciente = User::factory()->create([
            'rol'         => 'paciente',
            'paciente_id' => $paciente2->id,
        ]);
    }

    private function datosCita(array $override = []): array
    {
        return array_merge([
            'paciente_id'      => $this->paciente->id,
            'dentista_id'      => $this->dentista->id,
            'fecha'            => Carbon::tomorrow()->format('Y-m-d'),
            'hora_inicio'      => '10:00',
            'duracion_minutos' => 60,
            'motivo'           => 'Limpieza dental',
        ], $override);
    }

    // ── Creación exitosa ──────────────────────────────────────────────

    #[Test]
    public function admin_puede_crear_una_cita_con_datos_validos(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita())
            ->assertStatus(201);

        $this->assertDatabaseHas('citas', [
            'paciente_id' => $this->paciente->id,
            'dentista_id' => $this->dentista->id,
            'motivo'      => 'Limpieza dental',
        ]);
    }

    #[Test]
    public function crear_cita_devuelve_los_datos_de_la_cita_creada(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['hora_inicio' => '09:00']))
            ->assertStatus(201)
            ->assertJsonPath('data.motivo', 'Limpieza dental');
    }

    // ── Rechazo por conflicto de horario ─────────────────────────────

    #[Test]
    public function rechaza_cita_cuando_el_dentista_ya_tiene_otra_en_ese_horario(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['hora_inicio' => '11:00', 'duracion_minutos' => 60]));

        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['hora_inicio' => '11:30', 'duracion_minutos' => 60]))
            ->assertStatus(409)
            ->assertJsonPath('mensaje', 'El dentista ya tiene una cita en ese horario');
    }

    #[Test]
    public function permite_cita_inmediatamente_despues_de_otra_sin_empalme(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['hora_inicio' => '10:00', 'duracion_minutos' => 60]))
            ->assertStatus(201);

        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['hora_inicio' => '11:00', 'duracion_minutos' => 60]))
            ->assertStatus(201);
    }

    #[Test]
    public function cita_cancelada_no_bloquea_el_mismo_horario(): void
    {
        Cita::create([
            'paciente_id' => $this->paciente->id, 'dentista_id' => $this->dentista->id,
            'fecha' => Carbon::tomorrow()->format('Y-m-d'),
            'hora_inicio' => '14:00:00', 'hora_fin' => '15:00:00',
            'duracion_minutos' => 60, 'motivo' => 'Cancelada', 'estado' => 'cancelada',
        ]);

        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['hora_inicio' => '14:00', 'duracion_minutos' => 60]))
            ->assertStatus(201);
    }

    // ── Rechazo por fecha pasada ──────────────────────────────────────

    #[Test]
    public function rechaza_cita_en_fecha_pasada(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['fecha' => Carbon::yesterday()->format('Y-m-d')]))
            ->assertStatus(422)
            ->assertJsonPath('mensaje', 'No se pueden agendar citas en fechas u horas pasadas');
    }

    // ── Rechazo fuera del horario del dentista ────────────────────────

    #[Test]
    public function rechaza_cita_antes_del_horario_de_inicio_del_dentista(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['hora_inicio' => '07:00', 'duracion_minutos' => 30]))
            ->assertStatus(422)
            ->assertJsonPath('mensaje', 'La cita está fuera del horario laboral del dentista');
    }

    #[Test]
    public function rechaza_cita_que_termina_despues_del_horario_del_dentista(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['hora_inicio' => '17:30', 'duracion_minutos' => 60]))
            ->assertStatus(422)
            ->assertJsonPath('mensaje', 'La cita está fuera del horario laboral del dentista');
    }

    // ── Validación de campos ──────────────────────────────────────────

    #[Test]
    public function rechaza_cita_con_paciente_inexistente(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['paciente_id' => 99999]))
            ->assertStatus(422);
    }

    #[Test]
    public function rechaza_cita_con_duracion_menor_a_15_minutos(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/citas', $this->datosCita(['duracion_minutos' => 10]))
            ->assertStatus(422);
    }

    // ── Cancelar cita ─────────────────────────────────────────────────

    #[Test]
    public function admin_puede_cancelar_cualquier_cita(): void
    {
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id, 'dentista_id' => $this->dentista->id,
            'fecha' => Carbon::tomorrow()->format('Y-m-d'),
            'hora_inicio' => '09:00:00', 'hora_fin' => '10:00:00',
            'duracion_minutos' => 60, 'motivo' => 'Revisión', 'estado' => 'confirmada',
        ]);

        $this->actingAs($this->admin)
            ->put(route('citas.cancelar', $cita))
            ->assertRedirect(route('citas.vista'));

        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'estado' => 'cancelada']);
    }

    #[Test]
    public function paciente_no_puede_cancelar_cita_que_no_le_pertenece(): void
    {
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id, 'dentista_id' => $this->dentista->id,
            'fecha' => Carbon::tomorrow()->format('Y-m-d'),
            'hora_inicio' => '09:00:00', 'hora_fin' => '10:00:00',
            'duracion_minutos' => 60, 'motivo' => 'Revisión', 'estado' => 'confirmada',
        ]);

        $this->actingAs($this->usuarioPaciente)
            ->put(route('citas.cancelar', $cita))
            ->assertForbidden();

        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'estado' => 'confirmada']);
    }
}
