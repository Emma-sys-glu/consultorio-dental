<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Truncar todas las tablas con CASCADE (PostgreSQL respeta FK automáticamente)
        DB::statement('
            TRUNCATE TABLE
                push_subscriptions,
                notificaciones,
                recetas,
                tratamientos,
                expediente_documentos,
                expedientes,
                citas,
                inventarios,
                users,
                dentistas,
                pacientes
            RESTART IDENTITY CASCADE
        ');

        $this->call([
            PacienteSeeder::class,
            DentistaSeeder::class,
            InventarioSeeder::class,
            UserSeeder::class,
            CitaSeeder::class,
            ExpedienteSeeder::class,
            TratamientoSeeder::class,
            RecetaSeeder::class,
            NotificacionSeeder::class,
        ]);
    }
}
