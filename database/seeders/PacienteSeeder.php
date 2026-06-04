<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $sangres   = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $alergias  = ['Ninguna', 'Penicilina', 'Polvo', 'Látex', 'Mariscos', 'Ibuprofeno', 'Sulfas', 'Ninguna', 'Ninguna'];
        $antecedentes = [
            'Diabetes tipo 2 controlada',
            'Hipertensión arterial',
            'Asma leve',
            'Sin antecedentes relevantes',
            'Cardiopatía bajo tratamiento',
            'Epilepsia controlada',
            'Sin enfermedades crónicas',
            'Hipotiroidismo',
        ];

        $nombres       = ['Carlos', 'María', 'José', 'Ana', 'Luis', 'Laura', 'Miguel', 'Carmen', 'Juan', 'Rosa',
                          'Pedro', 'Elena', 'Francisco', 'Sofía', 'Antonio', 'Isabel', 'Manuel', 'Lucia', 'David',
                          'Marta', 'Javier', 'Paula', 'Sergio', 'Cristina', 'Fernando', 'Patricia', 'Roberto',
                          'Andrea', 'Alejandro', 'Silvia', 'Ricardo', 'Verónica', 'Eduardo', 'Monica', 'Raul',
                          'Diana', 'Hector', 'Sandra', 'Oscar', 'Claudia', 'Mario', 'Adriana', 'Ernesto'];
        $apellidos     = ['García', 'Martínez', 'López', 'González', 'Rodríguez', 'Hernández', 'Pérez', 'Sánchez',
                          'Ramírez', 'Torres', 'Flores', 'Rivera', 'Gómez', 'Díaz', 'Reyes', 'Cruz', 'Morales',
                          'Ortiz', 'Gutierrez', 'Chávez', 'Ramos', 'Vargas', 'Castillo', 'Jiménez', 'Moreno',
                          'Ruiz', 'Aguilar', 'Mendoza', 'Rios', 'Vega', 'Fuentes', 'Guerrero', 'Luna', 'Medina'];

        $rows  = [];
        $batch = 500;

        for ($i = 1; $i <= 3000; $i++) {
            $rows[] = [
                'nombre'            => $nombres[array_rand($nombres)],
                'apellido_paterno'  => $apellidos[array_rand($apellidos)],
                'apellido_materno'  => $apellidos[array_rand($apellidos)],
                'telefono'          => '55' . str_pad(mt_rand(10000000, 99999999), 8, '0'),
                'correo'            => 'paciente' . $i . '@mail.com',
                'fecha_nacimiento'  => date('Y-m-d', strtotime('-' . mt_rand(18, 75) . ' years -' . mt_rand(0, 365) . ' days')),
                'curp'              => strtoupper('PACD' . str_pad($i, 6, '0', STR_PAD_LEFT) . 'HMXXX00'),
                'tipo_sangre'       => $sangres[array_rand($sangres)],
                'alergias'          => $alergias[array_rand($alergias)],
                'antecedentes_medicos' => $antecedentes[array_rand($antecedentes)],
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            if (count($rows) >= $batch) {
                DB::table('pacientes')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('pacientes')->insert($rows);
        }
    }
}
