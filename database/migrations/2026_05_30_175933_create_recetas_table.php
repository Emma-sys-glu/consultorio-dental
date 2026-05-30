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
    Schema::create('recetas', function (Blueprint $table) {
        $table->id();

        $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
        $table->foreignId('dentista_id')->constrained('dentistas')->onDelete('cascade');
        $table->foreignId('tratamiento_id')->nullable()->constrained('tratamientos')->onDelete('set null');

        $table->string('medicamento');
        $table->string('dosis');
        $table->string('frecuencia');
        $table->string('duracion');
        $table->text('indicaciones')->nullable();
        $table->date('fecha_emision');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
