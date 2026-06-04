<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── Administrador ────────────────────────────────────────
        DB::table('users')->insert([
            'name'              => 'Administrador DentalTec',
            'email'             => 'admin@dentaltec.com',
            'password'          => Hash::make('admin123'),
            'rol'               => 'administrador',
            'email_verified_at' => $now,
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        // ── Recepcionistas ───────────────────────────────────────
        $recepcionistas = [
            ['name' => 'María García López',   'email' => 'recepcion1@dentaltec.com'],
            ['name' => 'Ana López Martínez',   'email' => 'recepcion2@dentaltec.com'],
            ['name' => 'Laura Sánchez Torres', 'email' => 'recepcion3@dentaltec.com'],
        ];

        foreach ($recepcionistas as $data) {
            DB::table('users')->insert(array_merge($data, [
                'password'          => Hash::make('recepcion123'),
                'rol'               => 'recepcionista',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]));
        }

        // ── Usuarios Dentista (primeros 100) ─────────────────────
        $dentistas = DB::table('dentistas')->orderBy('id')->limit(100)->get();

        $usersDentistas = [];
        foreach ($dentistas as $d) {
            $usersDentistas[] = [
                'name'              => "{$d->nombre} {$d->apellido_paterno}",
                'email'             => "dentista{$d->id}@dentaltec.com",
                'password'          => Hash::make('dentista123'),
                'rol'               => 'dentista',
                'dentista_id'       => $d->id,
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }
        foreach (array_chunk($usersDentistas, 100) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        // ── Usuarios Paciente (primeros 200) ─────────────────────
        $pacientes = DB::table('pacientes')->orderBy('id')->limit(200)->get();

        $usersPacientes = [];
        foreach ($pacientes as $p) {
            $usersPacientes[] = [
                'name'              => "{$p->nombre} {$p->apellido_paterno}",
                'email'             => "paciente{$p->id}@dentaltec.com",
                'password'          => Hash::make('paciente123'),
                'rol'               => 'paciente',
                'paciente_id'       => $p->id,
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }
        foreach (array_chunk($usersPacientes, 100) as $chunk) {
            DB::table('users')->insert($chunk);
        }
    }
}
