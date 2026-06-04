<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DentistaSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            'Odontología general',
            'Ortodoncia',
            'Endodoncia',
            'Periodoncia',
            'Cirugía dental',
            'Odontopediatría',
        ];

        $consultorios = ['Consultorio 1', 'Consultorio 2', 'Consultorio 3', 'Consultorio 4'];

        $horarios = [
            ['08:00:00', '14:00:00'],
            ['09:00:00', '15:00:00'],
            ['10:00:00', '16:00:00'],
            ['07:00:00', '13:00:00'],
            ['12:00:00', '18:00:00'],
            ['14:00:00', '20:00:00'],
        ];

        $nombres   = ['Carlos', 'María', 'José', 'Ana', 'Luis', 'Laura', 'Miguel', 'Carmen', 'Juan', 'Rosa',
                      'Pedro', 'Elena', 'Francisco', 'Sofía', 'Antonio', 'Isabel', 'Manuel', 'Lucia', 'David',
                      'Marta', 'Javier', 'Paula', 'Sergio', 'Cristina', 'Fernando', 'Patricia', 'Roberto',
                      'Andrea', 'Alejandro', 'Silvia', 'Ricardo', 'Verónica', 'Eduardo', 'Monica', 'Raul'];
        $apellidos = ['García', 'Martínez', 'López', 'González', 'Rodríguez', 'Hernández', 'Pérez', 'Sánchez',
                      'Ramírez', 'Torres', 'Flores', 'Rivera', 'Gómez', 'Díaz', 'Reyes', 'Cruz', 'Morales',
                      'Ortiz', 'Gutierrez', 'Chávez', 'Ramos', 'Vargas', 'Castillo', 'Jiménez', 'Moreno'];

        $rows  = [];
        $batch = 500;

        for ($i = 1; $i <= 3000; $i++) {
            $horario = $horarios[array_rand($horarios)];

            $rows[] = [
                'nombre'             => $nombres[array_rand($nombres)],
                'apellido_paterno'   => $apellidos[array_rand($apellidos)],
                'apellido_materno'   => $apellidos[array_rand($apellidos)],
                'especialidad'       => $especialidades[array_rand($especialidades)],
                'cedula_profesional' => 'CED' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'telefono'           => '55' . str_pad(mt_rand(10000000, 99999999), 8, '0'),
                'correo'             => 'dentista' . $i . '@dentaltec.com',
                'horario_inicio'     => $horario[0],
                'horario_fin'        => $horario[1],
                'consultorio'        => $consultorios[array_rand($consultorios)],
                'created_at'         => now(),
                'updated_at'         => now(),
            ];

            if (count($rows) >= $batch) {
                DB::table('dentistas')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('dentistas')->insert($rows);
        }
    }
}
