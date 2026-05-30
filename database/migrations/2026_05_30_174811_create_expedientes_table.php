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
    Schema::create('expedientes', function (Blueprint $table) {
        $table->id();

        $table->foreignId('paciente_id')
            ->constrained('pacientes')
            ->onDelete('cascade');

        $table->text('diagnostico')->nullable();
        $table->text('observaciones')->nullable();
        $table->text('procedimientos_realizados')->nullable();
        $table->text('evolucion_tratamiento')->nullable();

        $table->timestamps();

        $table->unique('paciente_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
