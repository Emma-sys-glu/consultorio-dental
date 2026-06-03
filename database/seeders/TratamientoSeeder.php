<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Expediente;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Illuminate\Database\Seeder;

class TratamientoSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Extracción dental',
            'Endodoncia',
            'Ortodoncia',
            'Limpieza dental',
            'Blanqueamiento dental',
            'Corona dental',
            'Implante dental',
            'Obturación',
            'Cirugía periodontal',
            'Sellador de fosas y fisuras',
        ];

        $estados = ['pendiente', 'en_proceso', 'finalizado', 'cancelado'];
        $pesos   = [20, 15, 50, 15];

        $pacienteIds  = Paciente::pluck('id')->toArray();
        $dentistaIds  = Dentista::pluck('id')->toArray();
        $expedientes  = Expediente::pluck('id', 'paciente_id')->toArray();
        $citaIds      = Cita::pluck('id')->toArray();

        $rows = [];
        for ($i = 0; $i < 2000; $i++) {
            $pacienteId  = $pacienteIds[array_rand($pacienteIds)];
            $expedienteId = $expedientes[$pacienteId] ?? null;

            if (!$expedienteId) {
                continue;
            }

            $estadoIndex = $this->weightedRandom($pesos);
            $estado      = $estados[$estadoIndex];

            $fechaInicio = fake()->dateTimeBetween('-2 years', 'now');
            $fechaFin    = in_array($estado, ['finalizado', 'cancelado'])
                ? fake()->dateTimeBetween($fechaInicio, 'now')
                : null;

            $rows[] = [
                'paciente_id'   => $pacienteId,
                'dentista_id'   => $dentistaIds[array_rand($dentistaIds)],
                'expediente_id' => $expedienteId,
                'cita_id'       => fake()->boolean(70) && !empty($citaIds)
                    ? $citaIds[array_rand($citaIds)]
                    : null,
                'nombre'        => $nombres[array_rand($nombres)],
                'descripcion'   => fake()->optional(0.8)->sentence(),
                'costo'         => fake()->randomFloat(2, 200, 15000),
                'estado'        => $estado,
                'fecha_inicio'  => $fechaInicio->format('Y-m-d'),
                'fecha_fin'     => $fechaFin?->format('Y-m-d'),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            if (count($rows) >= 500) {
                Tratamiento::insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            Tratamiento::insert($rows);
        }
    }

    private function weightedRandom(array $weights): int
    {
        $total      = array_sum($weights);
        $random     = mt_rand(1, $total);
        $cumulative = 0;

        foreach ($weights as $i => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $i;
            }
        }

        return count($weights) - 1;
    }
}
