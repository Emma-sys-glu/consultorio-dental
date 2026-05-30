<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('citas', function (Blueprint $table) {
        $table->id();

        $table->foreignId('paciente_id')
            ->constrained('pacientes')
            ->onDelete('cascade');

        $table->foreignId('dentista_id')
            ->constrained('dentistas')
            ->onDelete('cascade');

        $table->date('fecha');
        $table->time('hora_inicio');
        $table->time('hora_fin');
        $table->integer('duracion_minutos');

        $table->string('motivo');

        $table->enum('estado', [
            'pendiente',
            'confirmada',
            'cancelada',
            'finalizada'
        ])->default('pendiente');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
