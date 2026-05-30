<?php

namespace Database\Seeders;

use App\Models\Cita;
use Illuminate\Database\Seeder;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        Cita::factory()
            ->count(5000)
            ->create();
    }
}