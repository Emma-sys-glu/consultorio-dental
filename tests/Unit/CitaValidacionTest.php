<?php

namespace Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pruebas unitarias de la lógica de validación de citas.
 *
 * Verifica las reglas de negocio de forma aislada, sin HTTP:
 * detección de empalmes, validación de horario laboral y
 * cálculo de hora de fin a partir de la duración.
 */
class CitaValidacionTest extends TestCase
{
    // ── Helper: detectar empalme ──────────────────────────────────────

    /**
     * Replica la lógica de empalme del CitaController.
     * Retorna true si dos bloques de tiempo se solapan.
     */
    private function hayEmpalme(
        string $inicio1, string $fin1,
        string $inicio2, string $fin2
    ): bool {
        $i1 = Carbon::parse($inicio1);
        $f1 = Carbon::parse($fin1);
        $i2 = Carbon::parse($inicio2);
        $f2 = Carbon::parse($fin2);

        // Empalme si: inicio1 < fin2 Y fin1 > inicio2
        return $i1->lt($f2) && $f1->gt($i2);
    }

    private function horaFin(string $horaInicio, int $duracionMinutos): Carbon
    {
        return Carbon::parse($horaInicio)->addMinutes($duracionMinutos);
    }

    // ── Cálculo de hora de fin ────────────────────────────────────────

    #[Test]
    public function hora_de_fin_se_calcula_sumando_duracion(): void
    {
        $fin = $this->horaFin('10:00', 60);
        $this->assertEquals('11:00', $fin->format('H:i'));
    }

    #[Test]
    public function hora_de_fin_con_duracion_30_minutos(): void
    {
        $fin = $this->horaFin('09:30', 30);
        $this->assertEquals('10:00', $fin->format('H:i'));
    }

    #[Test]
    public function hora_de_fin_con_duracion_90_minutos(): void
    {
        $fin = $this->horaFin('14:00', 90);
        $this->assertEquals('15:30', $fin->format('H:i'));
    }

    // ── Detección de empalmes ─────────────────────────────────────────

    #[Test]
    public function detecta_empalme_cuando_segunda_cita_empieza_dentro_de_la_primera(): void
    {
        // Cita 1: 10:00–11:00  /  Cita 2: 10:30–11:30  → empalme
        $this->assertTrue(
            $this->hayEmpalme('10:00', '11:00', '10:30', '11:30')
        );
    }

    #[Test]
    public function detecta_empalme_cuando_segunda_cita_cubre_completamente_a_la_primera(): void
    {
        // Cita 1: 10:00–11:00  /  Cita 2: 09:30–11:30  → empalme
        $this->assertTrue(
            $this->hayEmpalme('10:00', '11:00', '09:30', '11:30')
        );
    }

    #[Test]
    public function no_hay_empalme_cuando_citas_son_consecutivas(): void
    {
        // Cita 1: 10:00–11:00  /  Cita 2: 11:00–12:00  → sin empalme
        $this->assertFalse(
            $this->hayEmpalme('10:00', '11:00', '11:00', '12:00')
        );
    }

    #[Test]
    public function no_hay_empalme_cuando_segunda_cita_es_anterior(): void
    {
        // Cita 1: 14:00–15:00  /  Cita 2: 12:00–13:00  → sin empalme
        $this->assertFalse(
            $this->hayEmpalme('14:00', '15:00', '12:00', '13:00')
        );
    }

    #[Test]
    public function no_hay_empalme_cuando_segunda_cita_es_posterior(): void
    {
        // Cita 1: 09:00–10:00  /  Cita 2: 10:00–11:00  → sin empalme
        $this->assertFalse(
            $this->hayEmpalme('09:00', '10:00', '10:00', '11:00')
        );
    }

    // ── Validación de horario laboral ─────────────────────────────────

    #[Test]
    public function cita_dentro_del_horario_laboral_es_valida(): void
    {
        $horarioInicio = Carbon::parse('08:00');
        $horarioFin    = Carbon::parse('18:00');
        $citaInicio    = Carbon::parse('10:00');
        $citaFin       = Carbon::parse('11:00');

        $dentroDelHorario = $citaInicio->gte($horarioInicio)
            && $citaFin->lte($horarioFin);

        $this->assertTrue($dentroDelHorario);
    }

    #[Test]
    public function cita_antes_del_horario_laboral_es_invalida(): void
    {
        $horarioInicio = Carbon::parse('08:00');
        $horarioFin    = Carbon::parse('18:00');
        $citaInicio    = Carbon::parse('07:00'); // antes del inicio
        $citaFin       = Carbon::parse('08:00');

        $dentroDelHorario = $citaInicio->gte($horarioInicio)
            && $citaFin->lte($horarioFin);

        $this->assertFalse($dentroDelHorario);
    }

    #[Test]
    public function cita_que_termina_despues_del_horario_laboral_es_invalida(): void
    {
        $horarioInicio = Carbon::parse('08:00');
        $horarioFin    = Carbon::parse('18:00');
        $citaInicio    = Carbon::parse('17:30');
        $citaFin       = Carbon::parse('18:30'); // excede horario fin

        $dentroDelHorario = $citaInicio->gte($horarioInicio)
            && $citaFin->lte($horarioFin);

        $this->assertFalse($dentroDelHorario);
    }

    // ── Fechas pasadas ────────────────────────────────────────────────

    #[Test]
    public function una_fecha_futura_no_es_pasada(): void
    {
        $fecha = Carbon::tomorrow()->setTime(10, 0);
        $this->assertFalse($fecha->isPast());
    }

    #[Test]
    public function una_fecha_de_ayer_es_pasada(): void
    {
        $fecha = Carbon::yesterday()->setTime(10, 0);
        $this->assertTrue($fecha->isPast());
    }
}
