<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecetaSeeder extends Seeder
{
    public function run(): void
    {
        $frecuencias = ['Cada 8 horas', 'Cada 12 horas', 'Cada 6 horas', 'Una vez al día', 'Dos veces al día'];
        $duraciones  = ['3 días', '5 días', '7 días', '10 días', '14 días'];
        $dosis       = ['1 tableta', '2 tabletas', '1 cápsula', '5 ml', '10 ml', '1 ampolleta'];
        $indicaciones = [
            'Tomar con alimentos para evitar molestias gástricas.',
            'No suspender el tratamiento aunque mejoren los síntomas.',
            'Evitar el consumo de alcohol durante el tratamiento.',
            'Mantener en refrigeración una vez abierto.',
            null, null,
        ];

        $medicamentosLibres = [
            'Paracetamol 500mg', 'Naproxeno 250mg', 'Cetirizina 10mg', 'Omeprazol 20mg',
            'Vitamina C 1g', 'Complejo B', 'Hierro ferroso 200mg',
        ];

        $pacienteIds  = DB::table('pacientes')->pluck('id')->toArray();
        $dentistaIds  = DB::table('dentistas')->pluck('id')->toArray();
        $tratamientoIds = DB::table('tratamientos')->pluck('id')->toArray();
        $invMeds      = DB::table('inventarios')
            ->where('categoria', 'Medicamentos')
            ->get(['id', 'nombre'])
            ->toArray();

        $rows  = [];
        $batch = 500;

        for ($i = 0; $i < 3000; $i++) {
            $usarInv = !empty($invMeds) && mt_rand(0, 1);

            if ($usarInv) {
                $inv          = $invMeds[array_rand($invMeds)];
                $inventarioId = $inv->id;
                $medicamento  = $inv->nombre;
                $cantidad     = mt_rand(1, 10);
            } else {
                $inventarioId = null;
                $medicamento  = $medicamentosLibres[array_rand($medicamentosLibres)];
                $cantidad     = null;
            }

            $rows[] = [
                'paciente_id'    => $pacienteIds[array_rand($pacienteIds)],
                'dentista_id'    => $dentistaIds[array_rand($dentistaIds)],
                'tratamiento_id' => (mt_rand(0, 1) && !empty($tratamientoIds))
                    ? $tratamientoIds[array_rand($tratamientoIds)]
                    : null,
                'inventario_id'  => $inventarioId,
                'cantidad'       => $cantidad,
                'medicamento'    => $medicamento,
                'dosis'          => $dosis[array_rand($dosis)],
                'frecuencia'     => $frecuencias[array_rand($frecuencias)],
                'duracion'       => $duraciones[array_rand($duraciones)],
                'indicaciones'   => $indicaciones[array_rand($indicaciones)],
                'fecha_emision'  => date('Y-m-d', strtotime('-' . mt_rand(0, 365) . ' days')),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            if (count($rows) >= $batch) {
                DB::table('recetas')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('recetas')->insert($rows);
        }
    }
}
