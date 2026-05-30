<?php

namespace Database\Seeders;

use App\Models\Dentista;
use Illuminate\Database\Seeder;

class DentistaSeeder extends Seeder
{
    public function run(): void
    {
        Dentista::factory()
            ->count(200)
            ->create();
    }
}