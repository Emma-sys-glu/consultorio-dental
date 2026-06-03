<?php

namespace Database\Seeders;

use App\Models\Dentista;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'               => 'Administrador DentalTec',
            'email'              => 'admin@dentaltec.com',
            'password'           => Hash::make('admin123'),
            'rol'                => 'administrador',
            'email_verified_at'  => now(),
        ]);

        $recepcionistas = [
            ['name' => 'María García',    'email' => 'recepcion1@dentaltec.com'],
            ['name' => 'Ana López',       'email' => 'recepcion2@dentaltec.com'],
            ['name' => 'Laura Martínez',  'email' => 'recepcion3@dentaltec.com'],
        ];

        foreach ($recepcionistas as $data) {
            User::create(array_merge($data, [
                'password'          => Hash::make('recepcion123'),
                'rol'               => 'recepcionista',
                'email_verified_at' => now(),
            ]));
        }

        Dentista::take(50)->get()->each(function (Dentista $dentista) {
            User::create([
                'name'              => "{$dentista->nombre} {$dentista->apellido_paterno}",
                'email'             => "dentista_{$dentista->id}@dentaltec.com",
                'password'          => Hash::make('dentista123'),
                'rol'               => 'dentista',
                'dentista_id'       => $dentista->id,
                'email_verified_at' => now(),
            ]);
        });

        Paciente::take(200)->get()->each(function (Paciente $paciente) {
            User::create([
                'name'              => "{$paciente->nombre} {$paciente->apellido_paterno}",
                'email'             => "paciente_{$paciente->id}@dentaltec.com",
                'password'          => Hash::make('paciente123'),
                'rol'               => 'paciente',
                'paciente_id'       => $paciente->id,
                'email_verified_at' => now(),
            ]);
        });
    }
}
