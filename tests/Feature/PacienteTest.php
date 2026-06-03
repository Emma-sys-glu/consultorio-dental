<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pruebas de gestión de pacientes.
 *
 * Cubre: creación, actualización, eliminación, validación de campos
 * obligatorios, unicidad de correo y control de acceso por rol.
 */
class PacienteTest extends TestCase
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

    private function pacienteRol(): User
    {
        $paciente = Paciente::factory()->create();
        return User::factory()->create([
            'rol'        => 'paciente',
            'paciente_id' => $paciente->id,
        ]);
    }

    private function datosPacienteValidos(array $override = []): array
    {
        return array_merge([
            'nombre'              => 'Juan',
            'apellido_paterno'    => 'García',
            'apellido_materno'    => 'López',
            'telefono'            => '7151234567',
            'correo'              => 'juan.garcia@email.com',
            'fecha_nacimiento'    => '1990-05-15',
            'curp'                => 'GALJ900515HMCRPN01',
            'tipo_sangre'         => 'O+',
            'alergias'            => 'Ninguna',
            'antecedentes_medicos' => '',
        ], $override);
    }

    // ── Listado ───────────────────────────────────────────────────────

    #[Test]
    public function admin_puede_ver_la_lista_de_pacientes(): void
    {
        Paciente::factory()->count(3)->create();

        $this->actingAs($this->admin())
            ->get(route('pacientes.vista'))
            ->assertOk()
            ->assertViewIs('pacientes.index');
    }

    #[Test]
    public function recepcionista_puede_ver_la_lista_de_pacientes(): void
    {
        $this->actingAs($this->recepcionista())
            ->get(route('pacientes.vista'))
            ->assertOk();
    }

    #[Test]
    public function usuario_con_rol_paciente_no_puede_ver_lista_de_pacientes(): void
    {
        $this->actingAs($this->pacienteRol())
            ->get(route('pacientes.vista'))
            ->assertForbidden();
    }

    // ── Crear paciente ────────────────────────────────────────────────

    #[Test]
    public function admin_puede_crear_paciente_con_datos_validos(): void
    {
        $datos = $this->datosPacienteValidos();

        $this->actingAs($this->admin())
            ->post(route('pacientes.guardar'), $datos)
            ->assertRedirect(route('pacientes.vista'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pacientes', [
            'nombre'          => 'Juan',
            'apellido_paterno' => 'García',
            'correo'          => 'juan.garcia@email.com',
        ]);
    }

    #[Test]
    public function recepcionista_puede_crear_paciente(): void
    {
        $datos = $this->datosPacienteValidos(['correo' => 'recep@email.com']);

        $this->actingAs($this->recepcionista())
            ->post(route('pacientes.guardar'), $datos)
            ->assertRedirect(route('pacientes.vista'));

        $this->assertDatabaseHas('pacientes', ['correo' => 'recep@email.com']);
    }

    #[Test]
    public function crear_paciente_sin_nombre_falla_validacion(): void
    {
        $this->actingAs($this->admin())
            ->post(route('pacientes.guardar'), $this->datosPacienteValidos(['nombre' => '']))
            ->assertSessionHasErrors('nombre');
    }

    #[Test]
    public function crear_paciente_sin_correo_falla_validacion(): void
    {
        $this->actingAs($this->admin())
            ->post(route('pacientes.guardar'), $this->datosPacienteValidos(['correo' => '']))
            ->assertSessionHasErrors('correo');
    }

    #[Test]
    public function crear_paciente_con_correo_invalido_falla_validacion(): void
    {
        $this->actingAs($this->admin())
            ->post(route('pacientes.guardar'), $this->datosPacienteValidos(['correo' => 'no-es-email']))
            ->assertSessionHasErrors('correo');
    }

    #[Test]
    public function no_se_permiten_dos_pacientes_con_el_mismo_correo(): void
    {
        Paciente::factory()->create(['correo' => 'repetido@email.com']);

        $this->actingAs($this->admin())
            ->post(route('pacientes.guardar'), $this->datosPacienteValidos(['correo' => 'repetido@email.com']))
            ->assertSessionHasErrors('correo');
    }

    #[Test]
    public function crear_paciente_sin_telefono_falla_validacion(): void
    {
        $this->actingAs($this->admin())
            ->post(route('pacientes.guardar'), $this->datosPacienteValidos(['telefono' => '']))
            ->assertSessionHasErrors('telefono');
    }

    #[Test]
    public function crear_paciente_sin_fecha_de_nacimiento_falla_validacion(): void
    {
        $this->actingAs($this->admin())
            ->post(route('pacientes.guardar'), $this->datosPacienteValidos(['fecha_nacimiento' => '']))
            ->assertSessionHasErrors('fecha_nacimiento');
    }

    // ── Actualizar paciente ───────────────────────────────────────────

    #[Test]
    public function admin_puede_actualizar_datos_de_un_paciente(): void
    {
        $paciente = Paciente::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('pacientes.actualizar', $paciente), $this->datosPacienteValidos([
                'nombre' => 'NombreActualizado',
                'correo' => $paciente->correo, // mismo correo para evitar conflicto
            ]))
            ->assertRedirect(route('pacientes.vista'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pacientes', ['nombre' => 'NombreActualizado']);
    }

    #[Test]
    public function al_actualizar_se_permite_mantener_el_mismo_correo(): void
    {
        $paciente = Paciente::factory()->create(['correo' => 'original@email.com']);

        $this->actingAs($this->admin())
            ->put(route('pacientes.actualizar', $paciente), $this->datosPacienteValidos([
                'correo' => 'original@email.com',
            ]))
            ->assertSessionDoesntHaveErrors('correo');
    }

    // ── Eliminar paciente ─────────────────────────────────────────────

    #[Test]
    public function admin_puede_eliminar_un_paciente(): void
    {
        $paciente = Paciente::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('pacientes.eliminar', $paciente))
            ->assertRedirect(route('pacientes.vista'));

        $this->assertDatabaseMissing('pacientes', ['id' => $paciente->id]);
    }
}
