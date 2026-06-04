<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpedienteSeeder extends Seeder
{
    public function run(): void
    {
        $diagnosticos = [
            'Caries dental múltiple', 'Gingivitis moderada', 'Maloclusión clase II',
            'Bruxismo nocturno', 'Periodontitis crónica', 'Hipersensibilidad dental',
            'Erupción del tercer molar', 'Fractura dental', 'Absceso periapical',
            'Erosión dental leve', 'Cálculo dental supragingival', 'Sin patología relevante',
            'Edentulismo parcial', 'Disfunción temporomandibular', 'Recesión gingival',
        ];

        $observaciones = [
            'Paciente refiere dolor al masticar alimentos duros.',
            'Se observa inflamación en encías superiores.',
            'Higiene oral deficiente, se recomienda técnica de cepillado.',
            'Paciente con hábito de bruxismo nocturno confirmado.',
            'Sensibilidad en zona de molares inferiores.',
            'Buen estado general de la dentición, mantenimiento preventivo.',
            'Paciente ansioso, requiere manejo especial.',
            null, null, null,
        ];

        $procedimientos = [
            'Obturación con resina compuesta en pieza 14',
            'Limpieza dental profunda supragingival y subgingival',
            'Extracción simple de tercer molar inferior',
            'Tratamiento de conducto en pieza 26',
            'Aplicación de selladores en premolares',
            'Colocación de corona de metal porcelana',
            'Blanqueamiento dental con peróxido de carbamida',
            'Instalación de brackets metálicos',
            'Cirugía periodontal en cuadrante superior derecho',
            'Profilaxis y aplicación de flúor',
        ];

        $evoluciones = [
            'Evolución favorable, paciente sigue indicaciones.',
            'Sin complicaciones post-tratamiento.',
            'Requiere seguimiento en 30 días.',
            'Dolor residual esperado, se prescribió analgésico.',
            'Excelente respuesta al tratamiento.',
            null, null,
        ];

        $pacienteIds = DB::table('pacientes')->pluck('id')->toArray();
        $rows  = [];
        $batch = 500;

        foreach ($pacienteIds as $pacienteId) {
            $rows[] = [
                'paciente_id'               => $pacienteId,
                'diagnostico'               => $diagnosticos[array_rand($diagnosticos)],
                'observaciones'             => $observaciones[array_rand($observaciones)],
                'procedimientos_realizados' => $procedimientos[array_rand($procedimientos)],
                'evolucion_tratamiento'     => $evoluciones[array_rand($evoluciones)],
                'created_at'                => now(),
                'updated_at'                => now(),
            ];

            if (count($rows) >= $batch) {
                DB::table('expedientes')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('expedientes')->insert($rows);
        }
    }
}
