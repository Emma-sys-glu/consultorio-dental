<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TratamientoSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Extracción dental simple', 'Extracción de molar', 'Endodoncia unirradicular',
            'Endodoncia birradicular', 'Ortodoncia fija metálica', 'Ortodoncia fija estética',
            'Limpieza dental básica', 'Limpieza dental profunda', 'Blanqueamiento dental',
            'Corona de metal porcelana', 'Corona de zirconia', 'Implante dental',
            'Obturación resina simple', 'Obturación resina compuesta', 'Cirugía periodontal',
            'Selladores de fosas', 'Prótesis parcial removible', 'Prótesis total',
            'Carillas dentales', 'Tratamiento de bruxismo',
        ];

        $estados = ['pendiente', 'en_proceso', 'finalizado', 'finalizado', 'cancelado'];
        $costos  = [500, 800, 1200, 1500, 2000, 3000, 4500, 6000, 8000, 12000, 15000];

        $pacienteIds  = DB::table('pacientes')->pluck('id')->toArray();
        $dentistaIds  = DB::table('dentistas')->pluck('id')->toArray();
        $expedientes  = DB::table('expedientes')->pluck('id', 'paciente_id')->toArray();
        $citaIds      = DB::table('citas')->pluck('id')->toArray();

        $rows  = [];
        $batch = 500;

        for ($i = 0; $i < 3000; $i++) {
            $pacienteId   = $pacienteIds[array_rand($pacienteIds)];
            $expedienteId = $expedientes[$pacienteId] ?? null;
            if (!$expedienteId) continue;

            $estado      = $estados[array_rand($estados)];
            $fechaInicio = date('Y-m-d', strtotime('-' . mt_rand(0, 730) . ' days'));
            $fechaFin    = in_array($estado, ['finalizado', 'cancelado'])
                ? date('Y-m-d', strtotime($fechaInicio . ' +' . mt_rand(7, 180) . ' days'))
                : null;

            $rows[] = [
                'paciente_id'   => $pacienteId,
                'dentista_id'   => $dentistaIds[array_rand($dentistaIds)],
                'expediente_id' => $expedienteId,
                'cita_id'       => (mt_rand(0, 1) && !empty($citaIds))
                    ? $citaIds[array_rand($citaIds)]
                    : null,
                'nombre'        => $nombres[array_rand($nombres)],
                'descripcion'   => mt_rand(0, 1) ? 'Tratamiento realizado sin complicaciones.' : null,
                'costo'         => $costos[array_rand($costos)],
                'estado'        => $estado,
                'fecha_inicio'  => $fechaInicio,
                'fecha_fin'     => $fechaFin,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            if (count($rows) >= $batch) {
                DB::table('tratamientos')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('tratamientos')->insert($rows);
        }
    }
}
