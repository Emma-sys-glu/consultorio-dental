<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PacienteSeeder::class,       // sin dependencias
            DentistaSeeder::class,       // sin dependencias
            UserSeeder::class,           // depende de pacientes y dentistas
            CitaSeeder::class,           // depende de pacientes y dentistas
            ExpedienteSeeder::class,     // depende de pacientes
            TratamientoSeeder::class,    // depende de pacientes, dentistas, expedientes, citas
            RecetaSeeder::class,         // depende de pacientes, dentistas, tratamientos
            InventarioSeeder::class,     // sin dependencias
            NotificacionSeeder::class,   // depende de pacientes
        ]);
    }
}