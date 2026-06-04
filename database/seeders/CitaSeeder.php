<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $motivos = [
            'Limpieza dental', 'Revisión general', 'Dolor dental', 'Ortodoncia',
            'Extracción', 'Blanqueamiento', 'Empaste', 'Revisión de ortodoncia',
            'Tratamiento de caries', 'Consulta de urgencia', 'Prótesis dental',
            'Cirugía de muela del juicio', 'Control periodontal', 'Implante dental',
        ];

        $estados = ['pendiente', 'confirmada', 'confirmada', 'finalizada', 'finalizada', 'cancelada'];

        $horas = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00',
                  '11:30', '12:00', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'];

        $duraciones = [30, 45, 60, 90];

        $pacienteIds = DB::table('pacientes')->pluck('id')->toArray();
        $dentistaIds = DB::table('dentistas')->pluck('id')->toArray();

        $rows  = [];
        $batch = 500;

        for ($i = 0; $i < 3000; $i++) {
            $hora       = $horas[array_rand($horas)];
            $duracion   = $duraciones[array_rand($duraciones)];
            $fechaBase  = date('Y-m-d', strtotime('-18 months +' . mt_rand(0, 900) . ' days'));
            [$h, $m]    = explode(':', $hora);
            $minFin     = (int)$h * 60 + (int)$m + $duracion;
            $horaFin    = sprintf('%02d:%02d:00', intdiv($minFin, 60), $minFin % 60);

            $rows[] = [
                'paciente_id'       => $pacienteIds[array_rand($pacienteIds)],
                'dentista_id'       => $dentistaIds[array_rand($dentistaIds)],
                'fecha'             => $fechaBase,
                'hora_inicio'       => $hora . ':00',
                'hora_fin'          => $horaFin,
                'duracion_minutos'  => $duracion,
                'motivo'            => $motivos[array_rand($motivos)],
                'estado'            => $estados[array_rand($estados)],
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            if (count($rows) >= $batch) {
                DB::table('citas')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('citas')->insert($rows);
        }
    }
}
